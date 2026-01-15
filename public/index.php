<?php
session_start();

require_once __DIR__ . '/../app/helpers.php';
$config = require __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/View.php';
require_once __DIR__ . '/../app/core/CostCalculator.php';
require_once __DIR__ . '/../app/controllers/IngredientController.php';
require_once __DIR__ . '/../app/controllers/RecipeController.php';
require_once __DIR__ . '/../app/controllers/CostController.php';
require_once __DIR__ . '/../app/controllers/ReportController.php';
require_once __DIR__ . '/../app/controllers/ConfigController.php';

$config['app']['base_url'] = base_url();
$db = new Database($config['db']);
$pdo = $db->pdo();

$page = $_GET['page'] ?? 'ingredientes';
$action = $_GET['action'] ?? 'index';

switch ($page) {
    case 'ingredientes':
        $controller = new IngredientController($pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($action === 'store') {
                $controller->store();
            } elseif ($action === 'update') {
                $controller->update();
            } elseif ($action === 'delete') {
                $controller->destroy();
            } elseif ($action === 'add_price') {
                $controller->addPrice();
            }
        } else {
            if ($action === 'history') {
                $controller->history();
            } elseif ($action === 'history_page') {
                $controller->historyPage();
            } else {
                $controller->index();
            }
        }
        break;
    case 'receitas':
        $controller = new RecipeController($pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($action === 'store') {
                $controller->store();
            } elseif ($action === 'update') {
                $controller->update();
            } elseif ($action === 'delete') {
                $controller->destroy();
            }
        } else {
            if ($action === 'items') {
                $controller->showItems();
            } elseif ($action === 'calculate') {
                $controller->calculate();
            } elseif ($action === 'perfil') {
                $controller->profile();
            } elseif ($action === 'view') {
                $controller->view();
            } else {
                $controller->index();
            }
        }
        break;
    case 'custos':
        $controller = new CostController($pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($action === 'store_profile') {
                $controller->storePerfil();
            } elseif ($action === 'update_profile') {
                $controller->updatePerfil();
            } elseif ($action === 'delete_profile') {
                $controller->destroyPerfil();
            }
        } else {
            $controller->index();
        }
        break;
    case 'relatorios':
        $controller = new ReportController($pdo);
        if ($action === 'export_csv') {
            $controller->exportCsv();
        } else {
            $controller->index();
        }
        break;
    case 'configuracoes':
        $controller = new ConfigController($pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($action === 'store') {
                $controller->store();
            } elseif ($action === 'update') {
                $controller->update();
            } elseif ($action === 'delete') {
                $controller->destroy();
            } elseif ($action === 'update_profile') {
                $controller->updateProfilePercents();
            } elseif ($action === 'set_profile') {
                $controller->setProfile();
            } elseif ($action === 'save_custos_percentuais') {
                $controller->saveCustosPercentuais();
            }
        } else {
            if ($action === 'perfil') {
                $controller->profile();
            } elseif ($action === 'custos_percentuais') {
                $controller->custosPercentuais();
            } else {
                $controller->index();
            }
        }
        break;
    default:
        redirect('?page=ingredientes');
}
