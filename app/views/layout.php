<?php
$baseUrl = $config['app']['base_url'] ?? base_url();
$page = $_GET['page'] ?? 'ingredientes';
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receituário - Custos e Precificação</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/style.css">
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <div class="brand">Receituário</div>
            <nav>
                <a class="nav-link <?= $page === 'ingredientes' ? 'active' : '' ?>" href="?page=ingredientes">Ingredientes</a>
                <a class="nav-link <?= $page === 'receitas' ? 'active' : '' ?>" href="?page=receitas">Receitas</a>
                <a class="nav-link <?= $page === 'custos' ? 'active' : '' ?>" href="?page=custos">Custos</a>
                <a class="nav-link <?= $page === 'relatorios' ? 'active' : '' ?>" href="?page=relatorios">Relatórios</a>
                <a class="nav-link <?= $page === 'configuracoes' ? 'active' : '' ?>" href="?page=configuracoes">Configurações</a>
            </nav>
        </aside>
        <main class="main">
            <header class="topbar">
                <h1><?= ucfirst($page) ?></h1>
                <span class="subtitle">Sistema de custos e precificação</span>
            </header>

            <?php if ($flashSuccess): ?>
                <div class="alert success"><?= htmlspecialchars($flashSuccess) ?></div>
            <?php endif; ?>
            <?php if ($flashError): ?>
                <div class="alert error"><?= htmlspecialchars($flashError) ?></div>
            <?php endif; ?>

            <section class="content">
                <?php include $viewPath; ?>
            </section>
        </main>
    </div>

    <script>
        const baseUrl = '<?= $baseUrl ?>';
        const csrfToken = '<?= csrf_token() ?>';
    </script>
    <script src="<?= $baseUrl ?>/assets/js/app.js"></script>
    <?php if ($page === 'ingredientes'): ?>
        <script src="<?= $baseUrl ?>/assets/js/ingredients.js"></script>
    <?php endif; ?>
    <?php if ($page === 'receitas'): ?>
        <script src="<?= $baseUrl ?>/assets/js/recipes.js"></script>
    <?php endif; ?>
    <?php if ($page === 'custos'): ?>
        <script src="<?= $baseUrl ?>/assets/js/costs.js"></script>
    <?php endif; ?>
    <?php if ($page === 'configuracoes'): ?>
        <script src="<?= $baseUrl ?>/assets/js/config.js"></script>
    <?php endif; ?>
</body>
</html>
