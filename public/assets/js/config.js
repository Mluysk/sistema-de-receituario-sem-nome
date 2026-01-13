document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelector('[data-tabs="configuracoes"]');
    const form = document.getElementById('unit-form');
    const cancelBtn = document.getElementById('btn-cancel-unidade');
    const perfilSelect = document.getElementById('perfil-configuracao');
    const percentualRows = document.getElementById('perfil-percentuais');

    if (tabs) {
        tabs.querySelectorAll('.tab-button').forEach((button) => {
            button.addEventListener('click', () => {
                tabs.querySelectorAll('.tab-button').forEach((btn) => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach((content) => content.classList.remove('active'));
                button.classList.add('active');
                document.getElementById(`tab-${button.dataset.tab}`)?.classList.add('active');
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
            if (percentualRows && data.percentConfig) {
                Object.entries(data.percentConfig).forEach(([key, value]) => {
                    const cell = percentualRows.querySelector(`[data-key="${key}"]`);
                    if (!cell) {
                        return;
                    }
                    if (key === 'total') {
                        cell.innerHTML = `<strong>${formatToMoney(value)}</strong>`;
                    } else {
                        cell.textContent = formatToMoney(value);
                    }
                });
            }
            if (data.perfil_id) {
                localStorage.setItem('perfil_custos_id', String(data.perfil_id));
            }
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
