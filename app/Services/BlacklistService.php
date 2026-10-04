<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class BlacklistService
{
    /**
     * Domain email blacklist (termasuk subdomain).
     */
    public function isBlacklistedEmailDomain(?string $email): bool
    {
        if (! $email || ! str_contains($email, '@')) {
            return false;
        }

        $domain = strtolower(substr(strrchr($email, '@'), 1));

        foreach ($this->all()['email'] as $blocked) {
            if ($domain === $blocked || str_ends_with($domain, '.'.$blocked)) {
                return true;
            }
        }

        return false;
    }

    /**
     * IP blacklist (exact match).
     */
    public function isBlacklistedIp(?string $ip): bool
    {
        return $ip !== null && in_array($ip, $this->all()['ip'], true);
    }

    /**
     * Baca + cache hasil parsing, di-key dengan signature file (mtime + size)
     * supaya otomatis refresh saat file berubah.
     */
    private function all(): array
    {
        $path = (string) config('security.blacklist_path');
        $signature = md5($path.'|'.(@filemtime($path) ?: 0).'|'.(@filesize($path) ?: 0));

        return Cache::rememberForever(
            'security.blacklist:'.$signature,
            fn (): array => $this->parse($path)
        );
    }

    /**
     * @return array{email: array<int, string>, ip: array<int, string>}
     */
    private function parse(string $path): array
    {
        $entries = ['email' => [], 'ip' => []];

        if (! is_readable($path)) {
            return $entries;
        }

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim(explode('#', $line, 2)[0]);

            if ($line === '' || ! str_contains($line, ':')) {
                continue;
            }

            [$type, $value] = explode(':', $line, 2);
            $type = strtolower(trim($type));
            $value = strtolower(trim($value));

            if ($value === '' || ! isset($entries[$type])) {
                continue;
            }

            if ($type === 'email') {
                $value = ltrim($value, '*@'); // dukung format *@domain.com
            }

            $entries[$type][] = $value;
        }

        return $entries;
    }
}
