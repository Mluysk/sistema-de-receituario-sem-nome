<div class="grid-2">
    <div class="card">
        <h2>Perfis de custos</h2>
        <form method="post" action="?page=custos&action=store_profile" class="inline-form">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="text" name="nome" placeholder="Novo perfil" required>
            <button class="btn primary" type="submit">Criar</button>
        </form>
        <ul class="profile-list">
            <?php foreach ($perfis as $perfil): ?>
                <li class="<?= ($perfilAtual && $perfilAtual['id'] === $perfil['id']) ? 'active' : '' ?>">
                    <a href="?page=custos&perfil_id=<?= $perfil['id'] ?>"><?= htmlspecialchars($perfil['nome']) ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php if ($perfilAtual): ?>
            <form method="post" action="?page=custos&action=delete_profile" onsubmit="return confirm('Deseja remover este perfil?');">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="perfil_id" value="<?= $perfilAtual['id'] ?>">
                <button class="btn danger" type="submit">Excluir perfil</button>
            </form>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Configuração do perfil</h2>
        <?php if ($perfilAtual): ?>
            <form method="post" action="?page=custos&action=update_profile" id="cost-form">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="perfil_id" value="<?= $perfilAtual['id'] ?>">
                <div class="form-group">
                    <label>Nome do perfil</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($perfilAtual['nome']) ?>" required>
                </div>

                <div class="list-header">
                    <h3>Itens de custo</h3>
                    <button type="button" class="btn ghost" id="add-cost-item">+ Adicionar item</button>
                </div>
                <div id="cost-items">
                    <?php foreach ($itens as $item): ?>
                        <div class="cost-item">
                            <input type="text" name="etiqueta[]" value="<?= htmlspecialchars($item['etiqueta']) ?>" placeholder="Etiqueta">
                            <select name="tipo[]">
                                <option value="fixo" <?= $item['tipo'] === 'fixo' ? 'selected' : '' ?>>Fixo (R$)</option>
                                <option value="percentual" <?= $item['tipo'] === 'percentual' ? 'selected' : '' ?>>Percentual (%)</option>
                            </select>
                            <input type="text" name="valor[]" class="mask-money" value="<?= format_money($item['tipo'] === 'fixo' ? $item['valor_fixo'] : $item['valor_percentual']) ?>">
                            <select name="base_calculo[]">
                                <option value="custo" <?= $item['base_calculo'] === 'custo' ? 'selected' : '' ?>>Base custo</option>
                                <option value="venda" <?= $item['base_calculo'] === 'venda' ? 'selected' : '' ?>>Base venda</option>
                                <option value="" <?= $item['base_calculo'] === null ? 'selected' : '' ?>>Não aplica</option>
                            </select>
                            <button type="button" class="btn danger small remove-cost">Remover</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="btn primary" type="submit">Salvar perfil</button>
            </form>
        <?php else: ?>
            <p>Crie um perfil para configurar custos adicionais.</p>
        <?php endif; ?>
    </div>
</div>

<template id="cost-item-template">
    <div class="cost-item">
        <input type="text" name="etiqueta[]" placeholder="Etiqueta (ex: embalagem, imposto)">
        <select name="tipo[]">
            <option value="fixo">Fixo (R$)</option>
            <option value="percentual">Percentual (%)</option>
        </select>
        <input type="text" name="valor[]" class="mask-money" placeholder="0,00">
        <select name="base_calculo[]">
            <option value="custo">Base custo</option>
            <option value="venda">Base venda</option>
            <option value="">Não aplica</option>
        </select>
        <button type="button" class="btn danger small remove-cost">Remover</button>
    </div>
</template>
