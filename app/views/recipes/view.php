<div class="receituario-layout">
    <div class="card receituario-card">
        <div class="receituario-header">
            <h2>Receituários da JP</h2>
            <h3><?= htmlspecialchars($receita['nome_receita'] ?? 'Receita') ?></h3>
        </div>
        <div class="table-wrapper">
            <table class="receituario-table">
                <thead>
                    <tr>
                        <th>Ingredientes</th>
                        <th>Quantidade 1</th>
                        <th>Kg</th>
                        <th>Quantidade 2</th>
                        <th>Kg</th>
                        <th>Quantidade 3</th>
                        <th>Kg</th>
                    </tr>
                </thead>
            <tbody>
                <?php
                $padraoBase = (float) ($receita['quantidade_padrao'] ?? 1);
                $multipliers = [$padraoBase, $padraoBase * 2, $padraoBase * 3];
                ?>
                <?php foreach ($itens as $item):
                    $kg = ((float) $item['gramas_usadas']) / 1000;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nome_ingrediente']) ?></td>
                        <td><?= number_format($kg * $multipliers[0], 3, ',', '.') ?></td>
                        <td>KG</td>
                        <td><?= number_format($kg * $multipliers[1], 3, ',', '.') ?></td>
                        <td>KG</td>
                        <td><?= number_format($kg * $multipliers[2], 3, ',', '.') ?></td>
                        <td>KG</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td><strong>Rendimento de receita</strong></td>
                    <td><?= number_format($totalKg * $multipliers[0], 3, ',', '.') ?></td>
                    <td>KG</td>
                    <td><?= number_format($totalKg * $multipliers[1], 3, ',', '.') ?></td>
                    <td>KG</td>
                    <td><?= number_format($totalKg * $multipliers[2], 3, ',', '.') ?></td>
                    <td>KG</td>
                </tr>
                <tr>
                    <td><strong>Quantidade de padrão</strong></td>
                    <td><?= number_format($multipliers[0], 0, ',', '.') ?></td>
                    <td>PD</td>
                    <td><?= number_format($multipliers[1], 0, ',', '.') ?></td>
                    <td>PD</td>
                    <td><?= number_format($multipliers[2], 0, ',', '.') ?></td>
                    <td>PD</td>
                </tr>
            </tfoot>
            </table>
        </div>
    </div>

    <div class="card receituario-costs">
        <div class="receituario-header">
            <h2>Custos</h2>
            <h3>Dos ingredientes</h3>
        </div>
        <div class="table-wrapper">
            <table class="receituario-table">
                <thead>
                    <tr>
                        <th>Ingredientes</th>
                        <th>Valor (g)</th>
                        <th>3 PD</th>
                        <th>2 PD</th>
                        <th>1 PD</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itens as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['nome_ingrediente']) ?></td>
                            <td>R$ <?= number_format($item['valor_grama'], 4, ',', '.') ?></td>
                            <td>R$ <?= number_format($item['custo_item'] * $multipliers[2], 2, ',', '.') ?></td>
                            <td>R$ <?= number_format($item['custo_item'] * $multipliers[1], 2, ',', '.') ?></td>
                            <td>R$ <?= number_format($item['custo_item'] * $multipliers[0], 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td>R$ <?= number_format($totalCusto / ($totalKg > 0 ? $totalKg * 1000 : 1) * 1000, 4, ',', '.') ?></td>
                        <td>R$ <?= number_format($totalCusto * $multipliers[2], 2, ',', '.') ?></td>
                        <td>R$ <?= number_format($totalCusto * $multipliers[1], 2, ',', '.') ?></td>
                        <td>R$ <?= number_format($totalCusto * $multipliers[0], 2, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="receituario-summary">
            <div><strong>Valor do kg:</strong> R$ <?= number_format($valorKg, 2, ',', '.') ?></div>
            <div><strong>Valor com insumo:</strong> R$ <?= number_format($totalCusto, 2, ',', '.') ?></div>
        </div>
        <div class="receituario-detail">
            <h4>Custo detalhado</h4>
            <table class="receituario-table">
                <thead>
                    <tr>
                        <th>Custos invisível</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Água e luz</td><td><?= number_format($percentConfig['agua_luz'], 2, ',', '.') ?></td></tr>
                    <tr><td>Imposto</td><td><?= number_format($percentConfig['imposto'], 2, ',', '.') ?></td></tr>
                    <tr><td>Sobre o valor</td><td><?= number_format($percentConfig['sobre_valor'], 2, ',', '.') ?></td></tr>
                    <tr><td>Sobre o custo bruto</td><td><?= number_format($percentConfig['sobre_custo_bruto'], 2, ',', '.') ?></td></tr>
                    <tr><td>Taxa de cartão</td><td><?= number_format($percentConfig['taxa_cartao'], 2, ',', '.') ?></td></tr>
                    <tr><td>Lucro</td><td><?= number_format($percentConfig['lucro'], 2, ',', '.') ?></td></tr>
                    <tr><td><strong>Total %</strong></td><td><strong><?= number_format($percentConfig['total'], 2, ',', '.') ?></strong></td></tr>
                </tbody>
            </table>
        </div>
        <div class="form-actions">
            <a href="?page=receitas" class="btn ghost">Voltar</a>
        </div>
    </div>
</div>
