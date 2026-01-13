document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('unit-form');
    const cancelBtn = document.getElementById('btn-cancel-unidade');

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
