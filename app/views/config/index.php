<div class="grid-2">
    <div class="card">
        <h2>Cadastro de unidade padrão</h2>
        <form method="post" action="?page=configuracoes&action=store" id="unit-form">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" id="unidade-id">
            <div class="form-group">
                <label>Nome da unidade*</label>
                <input type="text" name="nome_unidade" id="nome-unidade" placeholder="kg, un, L" required>
            </div>
            <div class="form-group">
                <label>Descrição</label>
                <textarea name="descricao" id="descricao-unidade"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn primary" id="btn-save-unidade">Salvar</button>
                <button type="button" class="btn ghost" id="btn-cancel-unidade" hidden>Cancelar edição</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Unidades cadastradas</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Unidade</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($unidades as $unidade): ?>
                        <tr>
                            <td><?= htmlspecialchars($unidade['nome_unidade']) ?></td>
                            <td><?= htmlspecialchars($unidade['descricao'] ?? '-') ?></td>
                            <td>
                                <button type="button" class="btn small" data-edit='<?= json_encode($unidade) ?>'>Editar</button>
                                <form method="post" action="?page=configuracoes&action=delete" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="id" value="<?= $unidade['id'] ?>">
                                    <button type="submit" class="btn danger small">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
