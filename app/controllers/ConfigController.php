<?php
class ConfigController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function index(): void
    {
        $unidades = $this->pdo->query('SELECT * FROM unidades_padrao ORDER BY nome_unidade')->fetchAll();
        View::render('config/index', [
            'unidades' => $unidades,
        ]);
    }

    public function store(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $nome = trim($_POST['nome_unidade'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        if ($nome === '') {
            $_SESSION['flash_error'] = 'Informe o nome da unidade.';
            redirect('?page=configuracoes');
        }
        $stmt = $this->pdo->prepare('INSERT INTO unidades_padrao (nome_unidade, descricao, data_cadastro) VALUES (?, ?, NOW())');
        $stmt->execute([$nome, $descricao]);
        $_SESSION['flash_success'] = 'Unidade cadastrada.';
        redirect('?page=configuracoes');
    }

    public function update(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $id = (int) ($_POST['id'] ?? 0);
        $nome = trim($_POST['nome_unidade'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        if ($id <= 0 || $nome === '') {
            $_SESSION['flash_error'] = 'Dados inválidos.';
            redirect('?page=configuracoes');
        }
        $stmt = $this->pdo->prepare('UPDATE unidades_padrao SET nome_unidade = ?, descricao = ? WHERE id = ?');
        $stmt->execute([$nome, $descricao, $id]);
        $_SESSION['flash_success'] = 'Unidade atualizada.';
        redirect('?page=configuracoes');
    }

    public function destroy(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            redirect('?page=configuracoes');
        }
        $stmt = $this->pdo->prepare('DELETE FROM unidades_padrao WHERE id = ?');
        $stmt->execute([$id]);
        $_SESSION['flash_success'] = 'Unidade removida.';
        redirect('?page=configuracoes');
    }
}
