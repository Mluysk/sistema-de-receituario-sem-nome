<div class="tabs" data-tabs="configuracoes">
    <button type="button" class="tab-button active" data-tab="unidades">Unidades padrão</button>
    <button type="button" class="tab-button" data-tab="custo-detalhado">Custo detalhado</button>
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
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody id="perfil-percentuais">
                            <tr><td>Água e luz</td><td data-key="agua_luz"><?= format_percent($percentConfig['agua_luz'] ?? 0) ?></td></tr>
                            <tr><td>Imposto</td><td data-key="imposto"><?= format_percent($percentConfig['imposto'] ?? 0) ?></td></tr>
                            <tr><td>Sobre o valor</td><td data-key="sobre_valor"><?= format_percent($percentConfig['sobre_valor'] ?? 0) ?></td></tr>
                            <tr><td>Sobre o custo bruto</td><td data-key="sobre_custo_bruto"><?= format_percent($percentConfig['sobre_custo_bruto'] ?? 0) ?></td></tr>
                            <tr><td>Taxa de cartão</td><td data-key="taxa_cartao"><?= format_percent($percentConfig['taxa_cartao'] ?? 0) ?></td></tr>
                            <tr><td>Lucro</td><td data-key="lucro"><?= format_percent($percentConfig['lucro'] ?? 0) ?></td></tr>
                            <tr><td><strong>Total</strong></td><td data-key="total"><strong><?= format_percent($percentConfig['total'] ?? 0) ?></strong></td></tr>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Nenhum perfil de custo cadastrado. Crie um perfil na aba Custos para configurar os percentuais.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
