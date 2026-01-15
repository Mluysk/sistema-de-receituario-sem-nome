<div class="card">
    <div class="list-header">
        <div>
            <h2>Histórico de preços</h2>
            <?php if ($ingrediente): ?>
                <p class="muted-text">Ingrediente: <?= htmlspecialchars($ingrediente['nome_ingrediente']) ?></p>
            <?php endif; ?>
        </div>
        <a href="?page=ingredientes" class="btn ghost">Voltar</a>
    </div>

    <?php if (!$ingrediente): ?>
        <p>Ingrediente não encontrado.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Preço (R$)</th>
                        <th>Fornecedor</th>
                        <th>Observação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historico as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['data_registro']) ?></td>
                            <td><?= format_money($item['preco_por_kg']) ?></td>
                            <td><?= htmlspecialchars($item['fornecedor'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($item['observacao'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
