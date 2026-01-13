document.addEventListener('DOMContentLoaded', () => {
    const addButton = document.getElementById('add-cost-item');
    const container = document.getElementById('cost-items');
    const template = document.getElementById('cost-item-template');

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

    bindRemove(container);
});
