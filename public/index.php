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
    default:
        redirect('?page=ingredientes');
}
