<div class="tabs" data-tabs="configuracoes">
    <button type="button" class="tab-button active" data-tab="unidades">Unidades padrão</button>
    <button type="button" class="tab-button" data-tab="custo-detalhado">Custo detalhado</button>
    <button type="button" class="tab-button" data-tab="custos-percentuais">Custos (%)</button>
</div>

<div class="tab-content active" id="tab-unidades">
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
</div>

<div class="tab-content" id="tab-custo-detalhado">
    <div class="grid-2">
        <div class="card">
            <h2>Custo detalhado</h2>
            <p class="muted-text">
                Os percentuais do receituário são definidos pelo perfil de custos selecionado abaixo.
                Ao trocar o perfil, os valores são atualizados imediatamente.
            </p>
            <div class="form-group">
                <label>Perfil de custos</label>
                <select id="perfil-configuracao">
                    <?php foreach ($perfis as $perfil): ?>
                        <option value="<?= $perfil['id'] ?>" <?= (int) $perfilAtualId === (int) $perfil['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($perfil['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-actions">
                <a class="btn primary" href="?page=custos">Configurar percentuais</a>
            </div>
        </div>

        <div class="card">
            <h2>Percentuais do perfil</h2>
            <?php if (!empty($perfis)): ?>
                <form method="post" action="?page=configuracoes&action=update_profile" id="perfil-percentuais-form">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="perfil_id" id="perfil-percentual-id" value="<?= (int) $perfilAtualId ?>">
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Água e luz (%)</label>
                            <input type="text" name="agua_luz" id="percent-agua-luz" class="mask-number" value="<?= format_percent($percentConfig['agua_luz'] ?? 0) ?>">
                        </div>
                        <div class="form-group">
                            <label>Imposto (%)</label>
                            <input type="text" name="imposto" id="percent-imposto" class="mask-number" value="<?= format_percent($percentConfig['imposto'] ?? 0) ?>">
                        </div>
                        <div class="form-group">
                            <label>Sobre o valor (%)</label>
                            <input type="text" name="sobre_valor" id="percent-sobre-valor" class="mask-number" value="<?= format_percent($percentConfig['sobre_valor'] ?? 0) ?>">
                        </div>
                        <div class="form-group">
                            <label>Sobre o custo bruto (%)</label>
                            <input type="text" name="sobre_custo_bruto" id="percent-sobre-custo-bruto" class="mask-number" value="<?= format_percent($percentConfig['sobre_custo_bruto'] ?? 0) ?>">
                        </div>
                        <div class="form-group">
                            <label>Taxa de cartão (%)</label>
                            <input type="text" name="taxa_cartao" id="percent-taxa-cartao" class="mask-number" value="<?= format_percent($percentConfig['taxa_cartao'] ?? 0) ?>">
                        </div>
                        <div class="form-group">
                            <label>Lucro (%)</label>
                            <input type="text" name="lucro" id="percent-lucro" class="mask-number" value="<?= format_percent($percentConfig['lucro'] ?? 0) ?>">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn primary">Salvar percentuais</button>
                    </div>
                </form>
            <?php else: ?>
                <p>Nenhum perfil de custo cadastrado. Crie um perfil na aba Custos para configurar os percentuais.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="tab-content" id="tab-custos-percentuais">
    <div class="card">
        <h2>Custos (%)</h2>
        <p class="muted-text">Configure os percentuais globais usados no receituário.</p>
        <form id="custos-percentuais-form">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="grid-2">
                <div class="form-group">
                    <label>Água e Luz (%)</label>
                    <input type="number" name="agua_luz" id="custos-agua-luz" step="0.01" min="0" value="<?= htmlspecialchars($custosPercentuais['agua_luz'] ?? 0) ?>">
                </div>
                <div class="form-group">
                    <label>Imposto (%)</label>
                    <input type="number" name="imposto" id="custos-imposto" step="0.01" min="0" value="<?= htmlspecialchars($custosPercentuais['imposto'] ?? 0) ?>">
                </div>
                <div class="form-group">
                    <label>Taxa de Cartão (%)</label>
                    <input type="number" name="taxa_cartao" id="custos-taxa-cartao" step="0.01" min="0" value="<?= htmlspecialchars($custosPercentuais['taxa_cartao'] ?? 0) ?>">
                </div>
                <div class="form-group">
                    <label>Margem de Lucro (%)</label>
                    <input type="number" name="margem_lucro" id="custos-margem-lucro" step="0.01" min="0" value="<?= htmlspecialchars($custosPercentuais['lucro'] ?? 0) ?>">
                </div>
                <div class="form-group">
                    <label>Outros (%)</label>
                    <input type="number" name="outros" id="custos-outros" step="0.01" min="0" value="<?= htmlspecialchars($custosPercentuais['outros'] ?? 0) ?>">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn primary">Salvar Custos (%)</button>
            </div>
        </form>
    </div>
</div>
