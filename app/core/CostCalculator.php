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
        $receita = $this->getRecipeExtras($receitaId);
        $extraFixo = (float) ($receita['custo_extra_fixo'] ?? 0);
        $extraPercentual = (float) ($receita['custo_extra_percentual'] ?? 0);
        $extraPercentualValor = $base * ($extraPercentual / 100);
        $baseTotal = $base + $extraFixo + $extraPercentualValor;

        if (!$perfilId) {
            return [
                'custo_base' => $baseTotal,
                'custo_total' => $baseTotal,
                'preco_venda' => 0,
                'ganho' => 0,
            ];
        }

        $custos = $this->loadCostProfile($perfilId);
        $globalPercents = get_custos_percentuais($this->pdo);
        $custoFixo = $custos['fixo'];
        $percentualCusto = $custos['percentual_custo'] + ($globalPercents['agua_luz'] / 100) + ($globalPercents['outros'] / 100);
        $percentualVenda = $custos['percentual_venda']
            + ($globalPercents['imposto'] / 100)
            + ($globalPercents['taxa_cartao'] / 100)
            + ($globalPercents['lucro'] / 100);
        $custoPercentual = $baseTotal * $percentualCusto;
        $maoDeObra = $custos['mao_de_obra'];
        $custoTotal = $baseTotal + $custoFixo + $custoPercentual + $maoDeObra;

        $precoVenda = $percentualVenda >= 1 ? 0 : ($custoTotal / (1 - $percentualVenda));
        $ganho = $precoVenda - $custoTotal;

        return [
            'custo_base' => $baseTotal,
            'custo_total' => $custoTotal,
            'preco_venda' => $precoVenda,
            'ganho' => $ganho,
        ];
    }

    private function getRecipeExtras(int $receitaId): array
    {
        $stmt = $this->pdo->prepare('SELECT custo_extra_fixo, custo_extra_percentual FROM receitas WHERE id = ?');
        $stmt->execute([$receitaId]);
        return $stmt->fetch() ?: ['custo_extra_fixo' => 0, 'custo_extra_percentual' => 0];
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
