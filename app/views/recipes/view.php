<div class="card receituario-card">
    <div class="receituario-header">
        <h2>Receituários da JP</h2>
        <h3><?= htmlspecialchars($receita['nome_receita'] ?? 'Receita') ?></h3>
    </div>
    <div class="table-wrapper">
        <table class="receituario-table">
            <thead>
                <tr>
                    <th rowspan="2">Ingredientes</th>
                    <th colspan="2">Quantidades</th>
                    <th colspan="2">Quantidades</th>
                    <th colspan="2">Quantidades</th>
                </tr>
                <tr>
                    <th>1</th>
                    <th>Kg</th>
                    <th>2</th>
                    <th>Kg</th>
                    <th>3</th>
                    <th>Kg</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itens as $item):
                    $kg = ((float) $item['gramas_usadas']) / 1000;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nome_ingrediente']) ?></td>
                        <td><?= number_format($kg, 3, ',', '.') ?></td>
                        <td>KG</td>
                        <td><?= number_format($kg * 2, 3, ',', '.') ?></td>
                        <td>KG</td>
                        <td><?= number_format($kg * 3, 3, ',', '.') ?></td>
                        <td>KG</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <?php
                $totalGramas = array_sum(array_map(fn ($item) => (float) $item['gramas_usadas'], $itens));
                $totalKg = $totalGramas / 1000;
                ?>
                <tr>
                    <td><strong>Rendimento de receita</strong></td>
                    <td><?= number_format($totalKg, 3, ',', '.') ?></td>
                    <td>KG</td>
                    <td><?= number_format($totalKg * 2, 3, ',', '.') ?></td>
                    <td>KG</td>
                    <td><?= number_format($totalKg * 3, 3, ',', '.') ?></td>
                    <td>KG</td>
                </tr>
                <tr>
                    <td><strong>Quantidade de padrão</strong></td>
                    <td>1</td>
                    <td>PD</td>
                    <td>2</td>
                    <td>PD</td>
                    <td>3</td>
                    <td>PD</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="form-actions">
        <a href="?page=receitas" class="btn ghost">Voltar</a>
    </div>
</div>
