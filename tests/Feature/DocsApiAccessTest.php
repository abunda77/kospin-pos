<?php

namespace Tests\Feature;

use App\Http\Middleware\SecureDocsAccess;
use Tests\TestCase;

class DocsApiAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('scramble.middleware', [
            'web',
            SecureDocsAccess::class,
        ]);
    }

    public function test_docs_api_menolak_tanpa_kredensial(): void
    {
        $this->getJson('/docs/api')->assertStatus(401)
            ->assertHeader('WWW-Authenticate', 'Basic realm="API Documentation"');
    }

    public function test_docs_api_menolak_password_salah(): void
    {
        config()->set('scramble_docs_password', 'rahasia');

        $this->withHeaders([
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'salah',
        ])->getJson('/docs/api')->assertStatus(401);
    }

    public function test_docs_api_menerima_password_benar(): void
    {
        $this->withHeaders([
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => (string) env('SCRAMBLE_DOCS_PASSWORD'),
        ])->getJson('/docs/api')->assertOk();
    }
}
