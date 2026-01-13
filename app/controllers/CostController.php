<?php
class CostController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function index(): void
    {
        $perfis = $this->pdo->query('SELECT * FROM custos_perfis ORDER BY nome')->fetchAll();
        $perfilAtual = null;
        $itens = [];
        if (!empty($_GET['perfil_id'])) {
            $perfilId = (int) $_GET['perfil_id'];
            $perfilAtual = $this->getPerfil($perfilId);
            $itens = $this->getItens($perfilId);
        } elseif (!empty($perfis)) {
            $perfilAtual = $perfis[0];
            $itens = $this->getItens((int) $perfilAtual['id']);
        }

        View::render('costs/index', [
            'perfis' => $perfis,
            'perfilAtual' => $perfilAtual,
            'itens' => $itens,
        ]);
    }

    public function storePerfil(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $nome = trim($_POST['nome'] ?? '');
        if ($nome === '') {
            $_SESSION['flash_error'] = 'Informe o nome do perfil.';
            redirect('?page=custos');
        }
        $stmt = $this->pdo->prepare('INSERT INTO custos_perfis (nome, data_cadastro) VALUES (?, NOW())');
        $stmt->execute([$nome]);
        error_log('perfil_custos_salvo_nome=' . $nome);
        $_SESSION['flash_success'] = 'Perfil criado.';
        redirect('?page=custos');
    }

    public function updatePerfil(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $id = (int) ($_POST['perfil_id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        if ($id <= 0 || $nome === '') {
            $_SESSION['flash_error'] = 'Dados inválidos.';
            redirect('?page=custos');
        }
        $stmt = $this->pdo->prepare('UPDATE custos_perfis SET nome = ? WHERE id = ?');
        $stmt->execute([$nome, $id]);

        $this->saveItems($id);
        error_log('perfil_custos_atualizado_id=' . $id . ' nome=' . $nome);

        $_SESSION['flash_success'] = 'Perfil atualizado.';
        redirect('?page=custos&perfil_id=' . $id);
    }

    public function destroyPerfil(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $id = (int) ($_POST['perfil_id'] ?? 0);
        if ($id <= 0) {
            redirect('?page=custos');
        }
        $this->pdo->prepare('DELETE FROM custos_perfis WHERE id = ?')->execute([$id]);
        $_SESSION['flash_success'] = 'Perfil removido.';
        redirect('?page=custos');
    }

    private function saveItems(int $perfilId): void
    {
        $etiquetas = $_POST['etiqueta'] ?? [];
        $tipos = $_POST['tipo'] ?? [];
        $valores = $_POST['valor'] ?? [];
        $bases = $_POST['base_calculo'] ?? [];

        $this->pdo->prepare('DELETE FROM custos_itens WHERE perfil_id = ?')->execute([$perfilId]);

        $stmt = $this->pdo->prepare('INSERT INTO custos_itens (perfil_id, etiqueta, tipo, valor_fixo, valor_percentual, base_calculo) VALUES (?, ?, ?, ?, ?, ?)');
        foreach ($etiquetas as $index => $etiqueta) {
            $etiqueta = trim($etiqueta);
            if ($etiqueta === '') {
                continue;
            }
            $tipo = $tipos[$index] ?? 'fixo';
            $valor = to_decimal($valores[$index] ?? '0');
            $base = $bases[$index] ?? null;
            $valorFixo = $tipo === 'fixo' ? $valor : null;
            $valorPercentual = $tipo === 'percentual' ? $valor : null;
            $stmt->execute([$perfilId, $etiqueta, $tipo, $valorFixo, $valorPercentual, $base]);
        }
    }

    private function getPerfil(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM custos_perfis WHERE id = ?');
        $stmt->execute([$id]);
        $perfil = $stmt->fetch();
        error_log('perfil_custos_lido_id=' . $id);
        return $perfil ?: null;
    }

    private function getItens(int $perfilId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM custos_itens WHERE perfil_id = ? ORDER BY id');
        $stmt->execute([$perfilId]);
        return $stmt->fetchAll();
    }
}
