<?php
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(string $token): void
{
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('Token CSRF inválido.');
    }
}

function to_decimal(?string $value): float
{
    if ($value === null) {
        return 0.0;
    }
    $clean = str_replace(['.', ' '], ['', ''], $value);
    $clean = str_replace(',', '.', $clean);

    return (float) $clean;
}

function format_money(?float $value): string
{
    return number_format((float) $value, 2, ',', '.');
}

function format_percent(?float $value): string
{
    return number_format((float) $value, 2, ',', '.');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}
