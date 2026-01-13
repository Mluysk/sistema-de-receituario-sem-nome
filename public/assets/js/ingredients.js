document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('ingredient-form');
    const cancelBtn = document.getElementById('btn-cancel');
    const modal = document.getElementById('history-modal');
    const closeHistory = document.getElementById('close-history');
    const historyList = document.getElementById('history-list');
    const historyForm = document.getElementById('history-form');
    const historyIngredienteId = document.getElementById('history-ingrediente-id');
    const tabs = document.querySelector('[data-tabs="ingredientes"]');
    const unidadeSelect = document.getElementById('unidade-padrao');
    const pesoInput = document.getElementById('peso-padrao');
    const pesoHidden = document.getElementById('peso-padrao-g');
    const pesoIndicator = document.getElementById('peso-padrao-unidade');

    document.querySelectorAll('[data-edit]').forEach((button) => {
        button.addEventListener('click', () => {
            const data = JSON.parse(button.dataset.edit);
            form.action = '?page=ingredientes&action=update';
            document.getElementById('ingrediente-id').value = data.id;
            document.getElementById('nome-ingrediente').value = data.nome_ingrediente;
            document.getElementById('fornecedor').value = data.fornecedor || '';
            document.getElementById('unidade-padrao').value = data.unidade_padrao;
            document.getElementById('peso-padrao').value = data.peso_padrao_g || '';
            document.getElementById('preco-kg').value = formatToMoney(data.preco_por_kg);
            document.getElementById('observacoes').value = data.observacoes || '';
            cancelBtn.hidden = false;
            setActiveTab('cadastro');
            updatePesoPadrao();
        });
    });

    cancelBtn.addEventListener('click', () => {
        form.reset();
        form.action = '?page=ingredientes&action=store';
        document.getElementById('ingrediente-id').value = '';
        cancelBtn.hidden = true;
        updatePesoPadrao();
    });

    document.querySelectorAll('[data-history]').forEach((button) => {
        button.addEventListener('click', async () => {
            const id = button.dataset.history;
            historyIngredienteId.value = id;
            modal.classList.add('open');
            await loadHistory(id);
        });
    });

    closeHistory.addEventListener('click', () => {
        modal.classList.remove('open');
    });

    async function loadHistory(id) {
        historyList.innerHTML = 'Carregando...';
        const response = await fetch(`?page=ingredientes&action=history&id=${id}`);
        const data = await response.json();
        historyList.innerHTML = data.length
            ? data
                  .map(
                      (item) => `
            <div class="history-item">
                <div><strong>Data:</strong> ${item.data_registro}</div>
                <div><strong>Preço:</strong> R$ ${formatToMoney(item.preco_por_kg)}</div>
                <div><strong>Fornecedor:</strong> ${item.fornecedor || '-'}</div>
                <div><strong>Obs:</strong> ${item.observacao || '-'}</div>
            </div>`
                  )
                  .join('')
            : '<p>Sem registros.</p>';
    }

    historyForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const formData = new FormData(historyForm);
        formData.append('csrf_token', csrfToken);
        const response = await fetch('?page=ingredientes&action=add_price', {
            method: 'POST',
            body: formData,
        });
        if (response.ok) {
            await loadHistory(historyIngredienteId.value);
            historyForm.reset();
            window.location.reload();
        }
    });

    function setActiveTab(tabName) {
        document.querySelectorAll('.tab-button').forEach((btn) => {
            btn.classList.toggle('active', btn.dataset.tab === tabName);
        });
        document.querySelectorAll('.tab-content').forEach((content) => {
            content.classList.toggle('active', content.id === `tab-${tabName}`);
        });
    }

    function updatePesoPadrao() {
        if (!pesoInput || !pesoHidden || !pesoIndicator || !unidadeSelect) {
            return;
        }
        const unidade = unidadeSelect.value;
        const valor = parseMoney(pesoInput.value);
        if (!valor) {
            pesoHidden.value = '';
            pesoIndicator.textContent = unidade || 'g';
            return;
        }

        if (unidade === 'kg' || unidade === 'g') {
            if (valor >= 1000) {
                const kg = valor / 1000;
                pesoIndicator.textContent = `${formatToMoney(kg)} kg`;
            } else {
                pesoIndicator.textContent = `${formatToMoney(valor)} g`;
            }
            pesoHidden.value = valor;
            return;
        }

        pesoIndicator.textContent = unidade || 'g';
        pesoHidden.value = valor;
    }

    if (tabs) {
        tabs.querySelectorAll('.tab-button').forEach((button) => {
            button.addEventListener('click', () => setActiveTab(button.dataset.tab));
        });
    }

    if (pesoInput) {
        pesoInput.addEventListener('input', updatePesoPadrao);
    }
    if (unidadeSelect) {
        unidadeSelect.addEventListener('change', updatePesoPadrao);
    }
    updatePesoPadrao();
});
