<div class="tabs" data-tabs="ingredientes">
    <button type="button" class="tab-button active" data-tab="cadastro">Cadastro</button>
    <button type="button" class="tab-button" data-tab="lista">Lista</button>
</div>

<div class="tab-content active" id="tab-cadastro">
    <div class="card">
        <h2>Novo ingrediente</h2>
        <form method="post" action="?page=ingredientes&action=store" id="ingredient-form">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" id="ingrediente-id">
            <div class="form-group">
                <label>Nome do ingrediente*</label>
                <input type="text" name="nome_ingrediente" id="nome-ingrediente" required>
            </div>
            <div class="form-group">
                <label>Fornecedor</label>
                <input type="text" name="fornecedor" id="fornecedor">
            </div>
            <div class="form-group">
                <label>Unidade padrão*</label>
                <select name="unidade_padrao" id="unidade-padrao" required>
                    <option value="">Selecione</option>
                    <?php foreach ($unidades as $unidade): ?>
                        <option value="<?= htmlspecialchars($unidade['nome_unidade']) ?>"><?= htmlspecialchars($unidade['nome_unidade']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Peso padrão (g)</label>
                <div class="input-with-unit">
                    <input type="text" name="peso_padrao_display" id="peso-padrao" class="mask-number" placeholder="Ex: 200 ou 1250">
                    <span class="unit-indicator" id="peso-padrao-unidade">g</span>
                </div>
                <input type="hidden" name="peso_padrao_g" id="peso-padrao-g">
            </div>
            <div class="form-group">
                <label>Preço por kg/unidade (R$)*</label>
                <input type="text" name="preco_por_kg" id="preco-kg" class="mask-money" required>
            </div>
            <div class="form-group">
                <label>Observações</label>
                <textarea name="observacoes" id="observacoes"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn primary" id="btn-save">Salvar</button>
                <button type="button" class="btn ghost" id="btn-cancel" hidden>Cancelar edição</button>
            </div>
        </form>
    </div>
</div>

<div class="tab-content" id="tab-lista">
    <div class="card">
        <h2>Ingredientes</h2>
        <form class="search" method="get">
            <input type="hidden" name="page" value="ingredientes">
            <input type="text" name="q" placeholder="Buscar ingrediente" value="<?= htmlspecialchars($search) ?>">
            <button class="btn ghost" type="submit">Buscar</button>
        </form>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Fornecedor</th>
                        <th>Unidade</th>
                        <th>Preço (R$)</th>
                        <th>Valor/grama</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ingredientes as $ingrediente): ?>
                        <tr>
                            <td><?= htmlspecialchars($ingrediente['nome_ingrediente']) ?></td>
                            <td><?= htmlspecialchars($ingrediente['fornecedor'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($ingrediente['unidade_padrao']) ?></td>
                            <td><?= format_money($ingrediente['preco_por_kg']) ?></td>
                            <td><?= format_money($ingrediente['preco_por_kg'] / 1000) ?></td>
                            <td>
                                <button type="button" class="btn small" data-edit='<?= json_encode($ingrediente) ?>'>Editar</button>
                                <button type="button" class="btn small" data-history="<?= $ingrediente['id'] ?>">Histórico</button>
                                <form method="post" action="?page=ingredientes&action=delete" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="id" value="<?= $ingrediente['id'] ?>">
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

<div class="modal" id="history-modal" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Histórico de preços</h3>
            <button class="btn ghost" id="close-history">Fechar</button>
        </div>
        <div class="modal-body">
            <div id="history-list" class="history-list"></div>
            <form id="history-form" class="history-form">
                <input type="hidden" name="ingrediente_id" id="history-ingrediente-id">
                <div class="form-group">
                    <label>Novo preço (R$)</label>
                    <input type="text" name="preco_por_kg" class="mask-money" required>
                </div>
                <div class="form-group">
                    <label>Fornecedor</label>
                    <input type="text" name="fornecedor">
                </div>
                <div class="form-group">
                    <label>Observação</label>
                    <input type="text" name="observacao">
                </div>
                <button type="submit" class="btn primary">Adicionar novo preço</button>
            </form>
        </div>
    </div>
</div>
