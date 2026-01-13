document.addEventListener('DOMContentLoaded', () => {
    const addButton = document.getElementById('add-cost-item');
    const container = document.getElementById('cost-items');
    const template = document.getElementById('cost-item-template');
    const presetButtons = document.querySelectorAll('.preset-btn');

    if (!addButton || !container) {
        return;
    }

    function bindRemove(scope) {
        scope.querySelectorAll('.remove-cost').forEach((btn) => {
            btn.addEventListener('click', () => {
                btn.closest('.cost-item').remove();
            });
        });
    }

    addButton.addEventListener('click', () => {
        const fragment = template.content.cloneNode(true);
        container.appendChild(fragment);
        applyMasks(container);
        bindRemove(container);
    });

    presetButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const fragment = template.content.cloneNode(true);
            const item = fragment.querySelector('.cost-item');
            if (!item) {
                return;
            }
            item.querySelector('input[name="etiqueta[]"]').value = button.dataset.label || '';
            item.querySelector('select[name="tipo[]"]').value = button.dataset.tipo || 'percentual';
            item.querySelector('select[name="base_calculo[]"]').value = button.dataset.base || 'venda';
            container.appendChild(fragment);
            applyMasks(container);
            bindRemove(container);
        });
    });

    bindRemove(container);
});
