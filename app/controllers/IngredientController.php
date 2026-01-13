<?php
class IngredientController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function index(): void
    {
        $search = trim($_GET['q'] ?? '');
        if ($search !== '') {
            $stmt = $this->pdo->prepare('SELECT * FROM ingredientes WHERE nome_ingrediente LIKE ? ORDER BY nome_ingrediente');
            $stmt->execute(['%' . $search . '%']);
        } else {
            $stmt = $this->pdo->query('SELECT * FROM ingredientes ORDER BY nome_ingrediente');
        }
        $ingredientes = $stmt->fetchAll();
        $unidades = $this->pdo->query('SELECT nome_unidade FROM unidades_padrao ORDER BY nome_unidade')->fetchAll();

        View::render('ingredients/index', [
            'ingredientes' => $ingredientes,
            'unidades' => $unidades,
            'search' => $search,
        ]);
    }

    public function store(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $nome = trim($_POST['nome_ingrediente'] ?? '');
        $fornecedor = trim($_POST['fornecedor'] ?? '');
        $unidade = trim($_POST['unidade_padrao'] ?? '');
        $pesoPadrao = to_decimal($_POST['peso_padrao_g'] ?? '');
        $precoKg = to_decimal($_POST['preco_por_kg'] ?? '');
        $observacoes = trim($_POST['observacoes'] ?? '');

        if ($nome === '' || $unidade === '' || $precoKg <= 0) {
            $_SESSION['flash_error'] = 'Preencha nome, unidade padrão e preço.';
            redirect('?page=ingredientes');
        }

        $stmt = $this->pdo->prepare('INSERT INTO ingredientes (nome_ingrediente, fornecedor, unidade_padrao, peso_padrao_g, preco_por_kg, observacoes, data_cadastro) VALUES (?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$nome, $fornecedor, $unidade, $pesoPadrao, $precoKg, $observacoes]);
        $ingredienteId = (int) $this->pdo->lastInsertId();

        $this->addHistory($ingredienteId, $precoKg, $fornecedor, 'Cadastro inicial');

        $_SESSION['flash_success'] = 'Ingrediente cadastrado com sucesso.';
        redirect('?page=ingredientes');
    }

    public function update(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $id = (int) ($_POST['id'] ?? 0);
        $nome = trim($_POST['nome_ingrediente'] ?? '');
        $fornecedor = trim($_POST['fornecedor'] ?? '');
        $unidade = trim($_POST['unidade_padrao'] ?? '');
        $pesoPadrao = to_decimal($_POST['peso_padrao_g'] ?? '');
        $precoKg = to_decimal($_POST['preco_por_kg'] ?? '');
        $observacoes = trim($_POST['observacoes'] ?? '');

        if ($id <= 0 || $nome === '' || $unidade === '' || $precoKg <= 0) {
            $_SESSION['flash_error'] = 'Preencha os campos obrigatórios.';
            redirect('?page=ingredientes');
        }

        $stmt = $this->pdo->prepare('SELECT preco_por_kg, fornecedor FROM ingredientes WHERE id = ?');
        $stmt->execute([$id]);
        $current = $stmt->fetch();

        $stmt = $this->pdo->prepare('UPDATE ingredientes SET nome_ingrediente = ?, fornecedor = ?, unidade_padrao = ?, peso_padrao_g = ?, preco_por_kg = ?, observacoes = ? WHERE id = ?');
        $stmt->execute([$nome, $fornecedor, $unidade, $pesoPadrao, $precoKg, $observacoes, $id]);

        if ($current && (float) $current['preco_por_kg'] !== $precoKg) {
            $this->addHistory($id, $precoKg, $fornecedor, 'Atualização manual');
        }

        $_SESSION['flash_success'] = 'Ingrediente atualizado.';
        redirect('?page=ingredientes');
    }

    public function destroy(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            redirect('?page=ingredientes');
        }
        $stmt = $this->pdo->prepare('DELETE FROM ingredientes WHERE id = ?');
        $stmt->execute([$id]);
        $_SESSION['flash_success'] = 'Ingrediente removido.';
        redirect('?page=ingredientes');
    }

    public function history(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $stmt = $this->pdo->prepare('SELECT data_registro, preco_por_kg, fornecedor, observacao FROM ingrediente_precos WHERE ingrediente_id = ? ORDER BY data_registro DESC');
        $stmt->execute([$id]);
        $rows = $stmt->fetchAll();
        header('Content-Type: application/json');
        echo json_encode($rows);
    }

    public function historyPage(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $stmt = $this->pdo->prepare('SELECT * FROM ingredientes WHERE id = ?');
        $stmt->execute([$id]);
        $ingrediente = $stmt->fetch();

        $stmt = $this->pdo->prepare('SELECT data_registro, preco_por_kg, fornecedor, observacao FROM ingrediente_precos WHERE ingrediente_id = ? ORDER BY data_registro DESC');
        $stmt->execute([$id]);
        $historico = $stmt->fetchAll();

        View::render('ingredients/history', [
            'ingrediente' => $ingrediente,
            'historico' => $historico,
        ]);
    }

    public function addPrice(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $id = (int) ($_POST['ingrediente_id'] ?? 0);
        $preco = to_decimal($_POST['preco_por_kg'] ?? '');
        $fornecedor = trim($_POST['fornecedor'] ?? '');
        $observacao = trim($_POST['observacao'] ?? '');
        if ($id <= 0 || $preco <= 0) {
            http_response_code(422);
            echo json_encode(['message' => 'Dados inválidos']);
            return;
        }

        $stmt = $this->pdo->prepare('UPDATE ingredientes SET preco_por_kg = ?, fornecedor = ? WHERE id = ?');
        $stmt->execute([$preco, $fornecedor, $id]);

        $this->addHistory($id, $preco, $fornecedor, $observacao !== '' ? $observacao : 'Atualização via histórico');

        header('Content-Type: application/json');
        echo json_encode(['message' => 'Preço registrado.']);
    }

    private function addHistory(int $ingredienteId, float $precoKg, string $fornecedor, string $observacao): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO ingrediente_precos (ingrediente_id, preco_por_kg, fornecedor, observacao, data_registro) VALUES (?, ?, ?, ?, NOW())');
        $stmt->execute([$ingredienteId, $precoKg, $fornecedor, $observacao]);
    }
}
