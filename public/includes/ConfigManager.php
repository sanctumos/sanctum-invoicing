<?php
declare(strict_types=1);

/**
 * Thin config facade for Sanctum Invoicing (SQLite config table).
 * Install / admin settings write through set_config / get_config.
 */
final class ConfigManager {
    private static ?self $instance = null;

    public static function getInstance(): self {
        return self::$instance ??= new self();
    }

    public function get(string $key, ?string $default = null): ?string {
        if (!function_exists('get_config')) {
            return $default;
        }
        try {
            $v = get_config($key);
        } catch (Throwable $e) {
            return $default;
        }
        if (!is_string($v) || $v === '') {
            return $default;
        }
        return $v;
    }

    public function set(string $key, string $value): void {
        if (function_exists('set_config')) {
            set_config($key, $value);
        }
    }

    public function appName(): string {
        return function_exists('dsc_invoicing_app_name')
            ? dsc_invoicing_app_name()
            : (string) ($this->get('site_name', 'Sanctum Invoicing') ?? 'Sanctum Invoicing');
    }
}
