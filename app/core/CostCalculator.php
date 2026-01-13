<?php
class CostCalculator
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function calculateRecipeCosts(int $receitaId, ?int $perfilId): array
    {
        $stmt = $this->pdo->prepare('SELECT SUM(custo_item) AS custo_total FROM receita_itens WHERE receita_id = ?');
        $stmt->execute([$receitaId]);
        $base = (float) ($stmt->fetch()['custo_total'] ?? 0);

        if (!$perfilId) {
            return [
                'custo_base' => $base,
                'custo_total' => $base,
                'preco_venda' => 0,
                'ganho' => 0,
            ];
        }

        $custos = $this->loadCostProfile($perfilId);
        $custoFixo = $custos['fixo'];
        $custoPercentual = $base * $custos['percentual_custo'];
        $maoDeObra = $custos['mao_de_obra'];
        $custoTotal = $base + $custoFixo + $custoPercentual + $maoDeObra;

        $percentuaisVenda = $custos['percentual_venda'];
        $precoVenda = $percentuaisVenda >= 1 ? 0 : ($custoTotal / (1 - $percentuaisVenda));
        $ganho = $precoVenda - $custoTotal;

        return [
            'custo_base' => $base,
            'custo_total' => $custoTotal,
            'preco_venda' => $precoVenda,
            'ganho' => $ganho,
        ];
    }

    private function loadCostProfile(int $perfilId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM custos_itens WHERE perfil_id = ?');
        $stmt->execute([$perfilId]);
        $items = $stmt->fetchAll();

        $fixo = 0.0;
        $percentualCusto = 0.0;
        $percentualVenda = 0.0;
        $maoDeObra = 0.0;

        foreach ($items as $item) {
            $valor = $item['tipo'] === 'fixo' ? (float) ($item['valor_fixo'] ?? 0) : (float) ($item['valor_percentual'] ?? 0);
            if ($item['tipo'] === 'fixo') {
                if ($item['etiqueta'] === 'mao_de_obra') {
                    $maoDeObra += $valor;
                } else {
                    $fixo += $valor;
                }
                continue;
            }

            if ($item['base_calculo'] === 'custo') {
                $percentualCusto += $valor / 100;
            }
            if ($item['base_calculo'] === 'venda') {
                $percentualVenda += $valor / 100;
            }
        }

        return [
            'fixo' => $fixo,
            'percentual_custo' => $percentualCusto,
            'percentual_venda' => $percentualVenda,
            'mao_de_obra' => $maoDeObra,
        ];
    }
}
