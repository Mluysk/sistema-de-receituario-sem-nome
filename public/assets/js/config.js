document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelector('[data-tabs="configuracoes"]');
    const form = document.getElementById('unit-form');
    const cancelBtn = document.getElementById('btn-cancel-unidade');
    const perfilSelect = document.getElementById('perfil-configuracao');
    const perfilPercentualId = document.getElementById('perfil-percentual-id');
    const custosForm = document.getElementById('custos-percentuais-form');
    const custosFields = {
        agua_luz: document.getElementById('custos-agua-luz'),
        imposto: document.getElementById('custos-imposto'),
        taxa_cartao: document.getElementById('custos-taxa-cartao'),
        margem_lucro: document.getElementById('custos-margem-lucro'),
        outros: document.getElementById('custos-outros'),
    };
    const percentFields = {
        agua_luz: document.getElementById('percent-agua-luz'),
        imposto: document.getElementById('percent-imposto'),
        sobre_valor: document.getElementById('percent-sobre-valor'),
        sobre_custo_bruto: document.getElementById('percent-sobre-custo-bruto'),
        taxa_cartao: document.getElementById('percent-taxa-cartao'),
        lucro: document.getElementById('percent-lucro'),
    };

    if (tabs) {
        tabs.querySelectorAll('.tab-button').forEach((button) => {
            button.addEventListener('click', () => {
                tabs.querySelectorAll('.tab-button').forEach((btn) => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach((content) => content.classList.remove('active'));
                button.classList.add('active');
                document.getElementById(`tab-${button.dataset.tab}`)?.classList.add('active');
                if (button.dataset.tab === 'custos-percentuais') {
                    loadCustosPercentuais();
                }
            });
        });
    }

    if (perfilSelect) {
        perfilSelect.addEventListener('change', async () => {
            const perfilId = perfilSelect.value;
            console.log('perfil_custos_id', perfilId);
            const response = await fetch('?page=configuracoes&action=set_profile', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `csrf_token=${encodeURIComponent(csrfToken)}&perfil_id=${encodeURIComponent(perfilId)}`,
            });
            const data = await response.json();
            console.log('custos_carregados', data);
            if (data.percentConfig) {
                Object.entries(percentFields).forEach(([key, field]) => {
                    if (!field) {
                        return;
                    }
                    field.value = formatToMoney(data.percentConfig[key] || 0);
                });
            }
            if (perfilPercentualId) {
                perfilPercentualId.value = perfilId;
            }
            if (data.perfil_id) {
                localStorage.setItem('perfil_custos_id', String(data.perfil_id));
            }
        });
    }

    async function loadCustosPercentuais() {
        if (!custosForm) {
            return;
        }
        const response = await fetch('?page=configuracoes&action=custos_percentuais');
        const data = await response.json();
        Object.entries(custosFields).forEach(([key, field]) => {
            if (!field) {
                return;
            }
            field.value = data[key] ?? 0;
        });
    }

    if (custosForm) {
        custosForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(custosForm);
            const response = await fetch('?page=configuracoes&action=save_custos_percentuais', {
                method: 'POST',
                body: new URLSearchParams(formData),
            });
            const data = await response.json();
            console.log('custos_carregados', data);
            if (data.data) {
                Object.entries(custosFields).forEach(([key, field]) => {
                    if (!field) {
                        return;
                    }
                    field.value = data.data[key] ?? 0;
                });
            }
            localStorage.setItem('custos_percentuais_atualizados', String(Date.now()));
        });
    }

    if (!form || !cancelBtn) {
        return;
    }

    document.querySelectorAll('[data-edit]').forEach((button) => {
        button.addEventListener('click', () => {
            const data = JSON.parse(button.dataset.edit);
            form.action = '?page=configuracoes&action=update';
            document.getElementById('unidade-id').value = data.id;
            document.getElementById('nome-unidade').value = data.nome_unidade;
            document.getElementById('descricao-unidade').value = data.descricao || '';
            cancelBtn.hidden = false;
        });
    });

    cancelBtn.addEventListener('click', () => {
        form.reset();
        form.action = '?page=configuracoes&action=store';
        document.getElementById('unidade-id').value = '';
        cancelBtn.hidden = true;
    });
});
