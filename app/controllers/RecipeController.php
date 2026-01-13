<?php
class RecipeController
{
    private PDO $pdo;
    private CostCalculator $calculator;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->calculator = new CostCalculator($pdo);
    }

    public function index(): void
    {
        $ingredientes = $this->pdo->query('SELECT id, nome_ingrediente, preco_por_kg FROM ingredientes ORDER BY nome_ingrediente')->fetchAll();
        $search = trim($_GET['q'] ?? '');
        if ($search !== '') {
            $stmt = $this->pdo->prepare('SELECT * FROM receitas WHERE nome_receita LIKE ? ORDER BY data_cadastro DESC');
            $stmt->execute(['%' . $search . '%']);
            $receitas = $stmt->fetchAll();
        } else {
            $receitas = $this->pdo->query('SELECT * FROM receitas ORDER BY data_cadastro DESC')->fetchAll();
        }
        $perfis = $this->pdo->query('SELECT * FROM custos_perfis ORDER BY nome')->fetchAll();
        $perfilId = $perfis[0]['id'] ?? null;

        $receitaCustos = [];
        foreach ($receitas as $receita) {
            $receitaCustos[$receita['id']] = $this->calculator->calculateRecipeCosts((int) $receita['id'], $perfilId);
        }

        View::render('recipes/index', [
            'ingredientes' => $ingredientes,
            'receitas' => $receitas,
            'perfis' => $perfis,
            'receitaCustos' => $receitaCustos,
            'percentConfig' => $this->getProfilePercents($perfilId),
            'search' => $search,
        ]);
    }

    public function store(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $nome = trim($_POST['nome_receita'] ?? '');
        $rendimento = to_decimal($_POST['rendimento_padrao'] ?? '');
        $custoExtraFixo = to_decimal($_POST['custo_extra_fixo'] ?? '');
        $custoExtraPercentual = to_decimal($_POST['custo_extra_percentual'] ?? '');
        $observacoes = trim($_POST['observacoes'] ?? '');
        $ingredientes = $_POST['ingrediente_id'] ?? [];
        $gramas = $_POST['gramas_usadas'] ?? [];

        if ($nome === '' || empty($ingredientes)) {
            $_SESSION['flash_error'] = 'Informe o nome e ao menos um ingrediente.';
            redirect('?page=receitas');
        }

        $stmt = $this->pdo->prepare('INSERT INTO receitas (nome_receita, rendimento_padrao, custo_extra_fixo, custo_extra_percentual, observacoes, data_cadastro) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$nome, $rendimento, $custoExtraFixo, $custoExtraPercentual, $observacoes]);
        $receitaId = (int) $this->pdo->lastInsertId();

        $this->storeItems($receitaId, $ingredientes, $gramas);

        $_SESSION['flash_success'] = 'Receita cadastrada.';
        redirect('?page=receitas');
    }

    public function update(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $id = (int) ($_POST['id'] ?? 0);
        $nome = trim($_POST['nome_receita'] ?? '');
        $rendimento = to_decimal($_POST['rendimento_padrao'] ?? '');
        $custoExtraFixo = to_decimal($_POST['custo_extra_fixo'] ?? '');
        $custoExtraPercentual = to_decimal($_POST['custo_extra_percentual'] ?? '');
        $observacoes = trim($_POST['observacoes'] ?? '');
        $ingredientes = $_POST['ingrediente_id'] ?? [];
        $gramas = $_POST['gramas_usadas'] ?? [];

        if ($id <= 0 || $nome === '' || empty($ingredientes)) {
            $_SESSION['flash_error'] = 'Preencha os campos obrigatórios.';
            redirect('?page=receitas');
        }

        $stmt = $this->pdo->prepare('UPDATE receitas SET nome_receita = ?, rendimento_padrao = ?, custo_extra_fixo = ?, custo_extra_percentual = ?, observacoes = ? WHERE id = ?');
        $stmt->execute([$nome, $rendimento, $custoExtraFixo, $custoExtraPercentual, $observacoes, $id]);

        $this->pdo->prepare('DELETE FROM receita_itens WHERE receita_id = ?')->execute([$id]);
        $this->storeItems($id, $ingredientes, $gramas);

        $_SESSION['flash_success'] = 'Receita atualizada.';
        redirect('?page=receitas');
    }

    public function destroy(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            redirect('?page=receitas');
        }
        $this->pdo->prepare('DELETE FROM receitas WHERE id = ?')->execute([$id]);
        $_SESSION['flash_success'] = 'Receita removida.';
        redirect('?page=receitas');
    }

    public function showItems(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $stmt = $this->pdo->prepare('SELECT ri.ingrediente_id, ri.gramas_usadas, i.nome_ingrediente, i.preco_por_kg FROM receita_itens ri JOIN ingredientes i ON i.id = ri.ingrediente_id WHERE ri.receita_id = ?');
        $stmt->execute([$id]);
        header('Content-Type: application/json');
        echo json_encode($stmt->fetchAll());
    }

    public function view(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $stmt = $this->pdo->prepare('SELECT * FROM receitas WHERE id = ?');
        $stmt->execute([$id]);
        $receita = $stmt->fetch();

        $stmt = $this->pdo->prepare('SELECT i.nome_ingrediente, i.preco_por_kg, ri.gramas_usadas FROM receita_itens ri JOIN ingredientes i ON i.id = ri.ingrediente_id WHERE ri.receita_id = ?');
        $stmt->execute([$id]);
        $itens = $stmt->fetchAll();

        $totalGramas = 0.0;
        $totalCusto = 0.0;
        foreach ($itens as &$item) {
            $valorGrama = ((float) $item['preco_por_kg']) / 1000;
            $item['valor_grama'] = $valorGrama;
            $item['custo_item'] = $valorGrama * (float) $item['gramas_usadas'];
            $totalGramas += (float) $item['gramas_usadas'];
            $totalCusto += $item['custo_item'];
        }
        unset($item);

        $totalKg = $totalGramas / 1000;
        $valorKg = $totalKg > 0 ? $totalCusto / $totalKg : 0;

        $perfis = $this->pdo->query('SELECT * FROM custos_perfis ORDER BY nome')->fetchAll();
        $perfilId = $perfis[0]['id'] ?? null;
        $percentConfig = $this->getProfilePercents($perfilId);

        View::render('recipes/view', [
            'receita' => $receita,
            'itens' => $itens,
            'totalGramas' => $totalGramas,
            'totalKg' => $totalKg,
            'totalCusto' => $totalCusto,
            'valorKg' => $valorKg,
            'percentConfig' => $percentConfig,
        ]);
    }

    public function calculate(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $perfilId = (int) ($_GET['perfil_id'] ?? 0);
        header('Content-Type: application/json');
        echo json_encode($this->calculator->calculateRecipeCosts($id, $perfilId));
    }

    public function profile(): void
    {
        $perfilId = (int) ($_GET['perfil_id'] ?? 0);
        header('Content-Type: application/json');
        echo json_encode($this->getProfilePercents($perfilId));
    }

    private function storeItems(int $receitaId, array $ingredientes, array $gramas): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO receita_itens (receita_id, ingrediente_id, gramas_usadas, custo_item) VALUES (?, ?, ?, ?)');
        foreach ($ingredientes as $index => $ingredienteId) {
            $ingredienteId = (int) $ingredienteId;
            $gramasUsadas = to_decimal($gramas[$index] ?? '0');
            if ($ingredienteId <= 0 || $gramasUsadas <= 0) {
                continue;
            }
            $valorGrama = $this->getValorGrama($ingredienteId);
            $custoItem = $gramasUsadas * $valorGrama;
            $stmt->execute([$receitaId, $ingredienteId, $gramasUsadas, $custoItem]);
        }
    }

    private function getValorGrama(int $ingredienteId): float
    {
        $stmt = $this->pdo->prepare('SELECT preco_por_kg FROM ingredientes WHERE id = ?');
        $stmt->execute([$ingredienteId]);
        $row = $stmt->fetch();
        if (!$row) {
            return 0.0;
        }
        return ((float) $row['preco_por_kg']) / 1000;
    }

    private function getProfilePercents(?int $perfilId): array
    {
        $defaults = [
            'agua_luz' => 0,
            'imposto' => 0,
            'sobre_valor' => 0,
            'sobre_custo_bruto' => 0,
            'taxa_cartao' => 0,
            'lucro' => 0,
            'total' => 0,
        ];
        if (!$perfilId) {
            return $defaults;
        }

        $stmt = $this->pdo->prepare('SELECT etiqueta, valor_percentual FROM custos_itens WHERE perfil_id = ? AND tipo = "percentual"');
        $stmt->execute([$perfilId]);
        $rows = $stmt->fetchAll();

        $map = [
            'agua e luz' => 'agua_luz',
            'imposto' => 'imposto',
            'sobre o valor' => 'sobre_valor',
            'sobre o custo bruto' => 'sobre_custo_bruto',
            'taxa de cartao' => 'taxa_cartao',
            'taxa de cartão' => 'taxa_cartao',
            'lucro' => 'lucro',
        ];

        foreach ($rows as $row) {
            $label = strtolower(trim($row['etiqueta']));
            if (isset($map[$label])) {
                $defaults[$map[$label]] = (float) $row['valor_percentual'];
            }
        }

        $defaults['total'] = array_sum([
            $defaults['agua_luz'],
            $defaults['imposto'],
            $defaults['sobre_valor'],
            $defaults['sobre_custo_bruto'],
            $defaults['taxa_cartao'],
            $defaults['lucro'],
        ]);

        return $defaults;
    }

}
