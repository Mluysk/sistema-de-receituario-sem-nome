<?php
class ReportController
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
        $perfis = $this->pdo->query('SELECT * FROM custos_perfis ORDER BY nome')->fetchAll();
        $perfilId = (int) ($_GET['perfil_id'] ?? ($perfis[0]['id'] ?? 0));

        $receitas = $this->pdo->query('SELECT * FROM receitas ORDER BY nome_receita')->fetchAll();
        $dados = [];
        foreach ($receitas as $receita) {
            $dados[] = array_merge($receita, $this->calculator->calculateRecipeCosts((int) $receita['id'], $perfilId));
        }

        View::render('reports/index', [
            'perfis' => $perfis,
            'perfilId' => $perfilId,
            'dados' => $dados,
        ]);
    }

    public function exportCsv(): void
    {
        $stmt = $this->pdo->query('SELECT r.id, r.nome_receita, r.rendimento_padrao, i.nome_ingrediente, ri.gramas_usadas, ri.custo_item FROM receitas r JOIN receita_itens ri ON ri.receita_id = r.id JOIN ingredientes i ON i.id = ri.ingrediente_id ORDER BY r.nome_receita');
        $rows = $stmt->fetchAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="receitas_itens.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Receita', 'Rendimento', 'Ingrediente', 'Gramas usadas', 'Custo item']);
        foreach ($rows as $row) {
            fputcsv($output, [
                $row['nome_receita'],
                $row['rendimento_padrao'],
                $row['nome_ingrediente'],
                $row['gramas_usadas'],
                $row['custo_item'],
            ]);
        }
        fclose($output);
        exit;
    }
}
