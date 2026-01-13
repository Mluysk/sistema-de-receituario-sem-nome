document.addEventListener('DOMContentLoaded', () => {
    const itemsContainer = document.getElementById('items-container');
    const addItemButton = document.getElementById('add-item');
    const custoTotal = document.getElementById('custo-total');
    const custoUnidade = document.getElementById('custo-unidade');
    const rendimentoInput = document.getElementById('rendimento');
    const form = document.getElementById('recipe-form');
    const cancelButton = document.getElementById('btn-cancel-receita');
    const perfilSelect = document.getElementById('perfil-receita');

    function createSelect(selectedId = '') {
        const select = document.createElement('select');
        select.name = 'ingrediente_id[]';
        select.required = true;
        ingredientesData.forEach((item) => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.nome_ingrediente;
            if (String(item.id) === String(selectedId)) {
                option.selected = true;
            }
            select.appendChild(option);
        });
        return select;
    }

    function createRow(data = {}) {
        const row = document.createElement('div');
        row.className = 'item-row';
        const select = createSelect(data.ingrediente_id || '');
        const gramasInput = document.createElement('input');
        gramasInput.type = 'text';
        gramasInput.name = 'gramas_usadas[]';
        gramasInput.className = 'mask-number';
        gramasInput.value = data.gramas_usadas ? String(data.gramas_usadas).replace('.', ',') : '';
        const custoSpan = document.createElement('span');
        custoSpan.className = 'item-cost';
        custoSpan.textContent = 'R$ 0,00';
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn danger small';
        removeBtn.textContent = 'Remover';

        row.appendChild(select);
        row.appendChild(gramasInput);
        row.appendChild(custoSpan);
        row.appendChild(removeBtn);
        itemsContainer.appendChild(row);

        function updateItemCost() {
            const ingredienteId = select.value;
            const ingrediente = ingredientesData.find((item) => String(item.id) === String(ingredienteId));
            const valorGrama = ingrediente ? Number(ingrediente.preco_por_kg) / 1000 : 0;
            const gramas = parseMoney(gramasInput.value);
            const custo = gramas * valorGrama;
            custoSpan.textContent = `R$ ${formatToMoney(custo)}`;
            updateTotals();
        }

        select.addEventListener('change', updateItemCost);
        gramasInput.addEventListener('input', updateItemCost);
        removeBtn.addEventListener('click', () => {
            row.remove();
            updateTotals();
        });

        applyMasks(row);
        updateItemCost();
    }

    function updateTotals() {
        let total = 0;
        itemsContainer.querySelectorAll('.item-row').forEach((row) => {
            const costText = row.querySelector('.item-cost').textContent.replace('R$ ', '');
            total += parseMoney(costText);
        });
        custoTotal.textContent = formatToMoney(total);
        const rendimento = parseMoney(rendimentoInput.value);
        const custoUnit = rendimento > 0 ? total / rendimento : 0;
        custoUnidade.textContent = formatToMoney(custoUnit);
    }

    addItemButton.addEventListener('click', () => createRow());
    rendimentoInput.addEventListener('input', updateTotals);

    document.querySelectorAll('[data-edit]').forEach((button) => {
        button.addEventListener('click', async () => {
            const data = JSON.parse(button.dataset.edit);
            form.action = '?page=receitas&action=update';
            document.getElementById('receita-id').value = data.id;
            document.getElementById('nome-receita').value = data.nome_receita;
            document.getElementById('rendimento').value = String(data.rendimento_padrao || '').replace('.', ',');
            document.getElementById('observacoes-receita').value = data.observacoes || '';
            cancelButton.hidden = false;

            itemsContainer.innerHTML = '';
            const response = await fetch(`?page=receitas&action=items&id=${data.id}`);
            const items = await response.json();
            items.forEach((item) => createRow(item));
            updateTotals();
        });
    });

    cancelButton.addEventListener('click', () => {
        form.reset();
        form.action = '?page=receitas&action=store';
        document.getElementById('receita-id').value = '';
        itemsContainer.innerHTML = '';
        createRow();
        updateTotals();
        cancelButton.hidden = true;
    });

    perfilSelect?.addEventListener('change', async () => {
        const perfilId = perfilSelect.value;
        const rows = document.querySelectorAll('tr[data-receita-id]');
        for (const row of rows) {
            const receitaId = row.dataset.receitaId;
            const response = await fetch(`?page=receitas&action=calculate&id=${receitaId}&perfil_id=${perfilId}`);
            const data = await response.json();
            row.querySelector('.custo-total').textContent = formatToMoney(data.custo_total);
            row.querySelector('.preco-venda').textContent = formatToMoney(data.preco_venda);
            row.querySelector('.ganho').textContent = formatToMoney(data.ganho);
        }
    });

    if (itemsContainer.children.length === 0) {
        createRow();
    }
});
