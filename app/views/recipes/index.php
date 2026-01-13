<div class="card">
    <h2>Cadastro de receita</h2>
    <form method="post" action="?page=receitas&action=store" id="recipe-form">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="id" id="receita-id">
        <div class="grid-2">
            <div class="form-group">
                <label>Nome da receita*</label>
                <input type="text" name="nome_receita" id="nome-receita" required>
            </div>
            <div class="form-group">
                <label>Rendimento padrão (unidades ou gramas)</label>
                <input type="text" name="rendimento_padrao" id="rendimento" class="mask-number">
            </div>
        </div>
        <div class="form-group">
            <label>Observações</label>
            <textarea name="observacoes" id="observacoes-receita"></textarea>
        </div>

        <div class="ingredients-list">
            <div class="list-header">
                <h3>Itens da receita</h3>
                <button type="button" class="btn ghost" id="add-item">+ Adicionar linha</button>
            </div>
            <div class="list-table" id="items-container"></div>
        </div>

        <div class="totals">
            <div><strong>Custo total:</strong> R$ <span id="custo-total">0,00</span></div>
            <div><strong>Custo por unidade:</strong> R$ <span id="custo-unidade">0,00</span></div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn primary" id="btn-save-receita">Salvar receita</button>
            <button type="button" class="btn ghost" id="btn-cancel-receita" hidden>Cancelar edição</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="list-header">
        <h2>Receitas cadastradas</h2>
        <div class="filters">
            <label>Perfil de custo</label>
            <select id="perfil-receita">
                <?php foreach ($perfis as $perfil): ?>
                    <option value="<?= $perfil['id'] ?>"><?= htmlspecialchars($perfil['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Receita</th>
                    <th>Custo total</th>
                    <th>Preço sugerido</th>
                    <th>Ganho</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($receitas as $receita):
                    $custos = $receitaCustos[$receita['id']] ?? ['custo_total' => 0, 'preco_venda' => 0, 'ganho' => 0];
                    ?>
                    <tr data-receita-id="<?= $receita['id'] ?>">
                        <td><?= htmlspecialchars($receita['nome_receita']) ?></td>
                        <td>R$ <span class="custo-total"><?= format_money($custos['custo_total']) ?></span></td>
                        <td>R$ <span class="preco-venda"><?= format_money($custos['preco_venda']) ?></span></td>
                        <td>R$ <span class="ganho"><?= format_money($custos['ganho']) ?></span></td>
                        <td>
                            <button type="button" class="btn small" data-edit='<?= json_encode($receita) ?>'>Editar</button>
                            <form method="post" action="?page=receitas&action=delete" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="id" value="<?= $receita['id'] ?>">
                                <button type="submit" class="btn danger small">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    const ingredientesData = <?= json_encode($ingredientes) ?>;
</script>
