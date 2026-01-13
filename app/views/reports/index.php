<div class="card">
    <div class="list-header">
        <h2>Relatórios de receitas</h2>
        <div class="filters">
            <label>Perfil de custo</label>
            <form method="get" class="inline-form">
                <input type="hidden" name="page" value="relatorios">
                <select name="perfil_id" onchange="this.form.submit()">
                    <?php foreach ($perfis as $perfil): ?>
                        <option value="<?= $perfil['id'] ?>" <?= $perfilId === (int) $perfil['id'] ? 'selected' : '' ?>><?= htmlspecialchars($perfil['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            <a class="btn ghost" href="?page=relatorios&action=export_csv">Exportar CSV</a>
        </div>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Receita</th>
                    <th>Custo total</th>
                    <th>Preço sugerido</th>
                    <th>Ganho estimado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dados as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nome_receita']) ?></td>
                        <td>R$ <?= format_money($item['custo_total']) ?></td>
                        <td>R$ <?= format_money($item['preco_venda']) ?></td>
                        <td>R$ <?= format_money($item['ganho']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
