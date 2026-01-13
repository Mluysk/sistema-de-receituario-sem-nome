<div class="tabs" data-tabs="receitas">
    <button type="button" class="tab-button active" data-tab="cadastro">Cadastro</button>
    <button type="button" class="tab-button" data-tab="receituario">Receituário</button>
</div>

<div class="tab-content active" id="tab-cadastro">
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

        <div class="recipe-layout">
            <div class="recipe-panel">
                <div class="list-header">
                    <h3>Ingredientes</h3>
                    <button type="button" class="btn ghost" id="add-item">+ Adicionar linha</button>
                </div>
                <div class="table-wrapper">
                    <table class="recipe-table">
                        <thead>
                            <tr>
                                <th>Ingrediente</th>
                                <th>Quantidade (g)</th>
                                <th>Valor/g</th>
                                <th>Custo item</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="items-container"></tbody>
                    </table>
                </div>
            </div>
            <div class="recipe-panel">
                <h3>Custos</h3>
                <div class="cost-percent-grid">
                    <div class="form-group">
                        <label>Água e luz (%)</label>
                        <input type="text" id="perfil-agua-luz" class="mask-number" value="<?= format_percent($percentConfig['agua_luz'] ?? 0) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Imposto (%)</label>
                        <input type="text" id="perfil-imposto" class="mask-number" value="<?= format_percent($percentConfig['imposto'] ?? 0) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Sobre o valor (%)</label>
                        <input type="text" id="perfil-sobre-valor" class="mask-number" value="<?= format_percent($percentConfig['sobre_valor'] ?? 0) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Sobre o custo bruto (%)</label>
                        <input type="text" id="perfil-sobre-custo-bruto" class="mask-number" value="<?= format_percent($percentConfig['sobre_custo_bruto'] ?? 0) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Taxa de cartão (%)</label>
                        <input type="text" id="perfil-taxa-cartao" class="mask-number" value="<?= format_percent($percentConfig['taxa_cartao'] ?? 0) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Lucro (%)</label>
                        <input type="text" id="perfil-lucro" class="mask-number" value="<?= format_percent($percentConfig['lucro'] ?? 0) ?>" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label>Percentual total do perfil (%)</label>
                    <input type="text" id="perfil-total" class="mask-number" value="<?= format_percent($percentConfig['total'] ?? 0) ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Custo extra fixo (R$)</label>
                    <input type="text" name="custo_extra_fixo" id="custo-extra-fixo" class="mask-money" placeholder="0,00">
                </div>
                <div class="form-group">
                    <label>Custo extra percentual total (%)</label>
                    <input type="text" name="custo_extra_percentual" id="custo-extra-percentual" class="mask-number" placeholder="0,00">
                </div>
                <div class="totals">
                    <div><strong>Custo base:</strong> R$ <span id="custo-base">0,00</span></div>
                    <div><strong>Custo total:</strong> R$ <span id="custo-total">0,00</span></div>
                    <div><strong>Custo por unidade:</strong> R$ <span id="custo-unidade">0,00</span></div>
                </div>
                <p class="muted-text">Custos adicionais são somados ao custo base antes do perfil.</p>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn primary" id="btn-save-receita">Salvar receita</button>
            <button type="button" class="btn ghost" id="btn-cancel-receita" hidden>Cancelar edição</button>
        </div>
        </form>
    </div>
</div>

<div class="tab-content" id="tab-receituario">
    <div class="card receituario-card">
        <div class="receituario-header">
            <h2>Receituários da JP</h2>
            <h3 id="receituario-nome">Nome da receita</h3>
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
                <tbody id="receituario-body"></tbody>
                <tfoot>
                    <tr>
                        <td><strong>Rendimento de receita</strong></td>
                        <td id="receituario-rendimento-1">0,000</td>
                        <td>KG</td>
                        <td id="receituario-rendimento-2">0,000</td>
                        <td>KG</td>
                        <td id="receituario-rendimento-3">0,000</td>
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
    </div>
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
