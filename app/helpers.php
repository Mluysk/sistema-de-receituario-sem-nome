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

function get_custos_percentuais(PDO $pdo): array
{
    $defaults = [
        'agua_luz' => 0.0,
        'imposto' => 0.0,
        'sobre_valor' => 0.0,
        'sobre_custo_bruto' => 0.0,
        'taxa_cartao' => 0.0,
        'lucro' => 0.0,
        'margem_lucro' => 0.0,
        'outros' => 0.0,
        'total' => 0.0,
    ];

    $stmt = $pdo->query('SELECT agua_luz, imposto, taxa_cartao, margem_lucro, outros FROM configuracoes_custos ORDER BY id DESC LIMIT 1');
    $row = $stmt ? $stmt->fetch() : null;
    if (!$row) {
        return $defaults;
    }

    $defaults['agua_luz'] = (float) ($row['agua_luz'] ?? 0);
    $defaults['imposto'] = (float) ($row['imposto'] ?? 0);
    $defaults['taxa_cartao'] = (float) ($row['taxa_cartao'] ?? 0);
    $defaults['lucro'] = (float) ($row['margem_lucro'] ?? 0);
    $defaults['margem_lucro'] = $defaults['lucro'];
    $defaults['outros'] = (float) ($row['outros'] ?? 0);
    $defaults['total'] = array_sum([
        $defaults['agua_luz'],
        $defaults['imposto'],
        $defaults['sobre_valor'],
        $defaults['sobre_custo_bruto'],
        $defaults['taxa_cartao'],
        $defaults['lucro'],
        $defaults['outros'],
    ]);

    return $defaults;
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function base_url(): string
{
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    return $base === '' ? '' : $base;
}
