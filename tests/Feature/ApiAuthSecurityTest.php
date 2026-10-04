<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ApiAuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    private string $blacklistPath;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        $this->blacklistPath = tempnam(sys_get_temp_dir(), 'blacklist_');
        config()->set('security.blacklist_path', $this->blacklistPath);
    }

    protected function tearDown(): void
    {
        @unlink($this->blacklistPath);

        parent::tearDown();
    }

    public function test_login_diblokir_setelah_lima_kegagalan(): void
    {
        $user = User::factory()->create(['email' => 'kasir@example.com']);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/api/login', ['email' => $user->email, 'password' => 'salah'])
                ->assertStatus(422);
        }

        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'password'])
            ->assertStatus(429)
            ->assertHeader('Retry-After')
            ->assertJson(['success' => false]);
    }

    public function test_login_sukses_mereset_limiter(): void
    {
        $user = User::factory()->create(['email' => 'kasir@example.com']);

        for ($attempt = 0; $attempt < 4; $attempt++) {
            $this->postJson('/api/login', ['email' => $user->email, 'password' => 'salah'])
                ->assertStatus(422);
        }

        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'password'])
            ->assertOk()
            ->assertJson(['success' => true]);

        // Counter sudah di-clear, jadi masih boleh gagal lagi tanpa 429.
        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'salah'])
            ->assertStatus(422);
    }

    public function test_middleware_throttle_menghasilkan_429_terstandar(): void
    {
        // Limiter api-login = 20 request/menit per IP (semua request, bukan hanya gagal).
        for ($attempt = 0; $attempt < 20; $attempt++) {
            $this->postJson('/api/login', ['email' => 'bukan-email', 'password' => 'x'])
                ->assertStatus(422);
        }

        $this->postJson('/api/login', ['email' => 'bukan-email', 'password' => 'x'])
            ->assertStatus(429)
            ->assertHeader('Retry-After')
            ->assertJsonStructure(['success', 'message', 'retry_after']);
    }

    public function test_email_domain_blacklist_ditolak(): void
    {
        file_put_contents($this->blacklistPath, "email:mailinator.com\n");

        User::factory()->create(['email' => 'spammer@mailinator.com']);

        $this->postJson('/api/login', [
            'email' => 'spammer@mailinator.com',
            'password' => 'password',
        ])->assertStatus(422)
            ->assertJsonPath('errors.email.0', 'Unknown occurs');
    }

    public function test_subdomain_dari_domain_blacklist_ikut_ditolak(): void
    {
        file_put_contents($this->blacklistPath, "email:*@fakedomain.com\n");

        $this->postJson('/api/login', [
            'email' => 'a@sub.fakedomain.com',
            'password' => 'password',
        ])->assertStatus(422)
            ->assertJsonPath('errors.email.0', 'Unknown occurs');
    }

    public function test_domain_normal_tetap_lolos(): void
    {
        file_put_contents($this->blacklistPath, "email:mailinator.com\n");

        $user = User::factory()->create(['email' => 'kasir@example.com']);

        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'password'])
            ->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_ip_blacklist_ditolak_403(): void
    {
        file_put_contents($this->blacklistPath, "ip:127.0.0.1\n");

        $this->postJson('/api/login', [
            'email' => 'kasir@example.com',
            'password' => 'password',
        ])->assertStatus(403)
            ->assertJson(['success' => false]);
    }
}
