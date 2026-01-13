function formatToMoney(value) {
    const number = Number(value || 0);
    return number.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function parseMoney(value) {
    if (!value) return 0;
    const clean = value.replace(/\./g, '').replace(',', '.');
    return Number(clean) || 0;
}

function applyMasks(scope = document) {
    scope.querySelectorAll('.mask-money').forEach((input) => {
        input.addEventListener('input', () => {
            const value = input.value.replace(/[^\d,]/g, '').replace(/(,.*?),/g, '$1');
            input.value = value;
        });
    });
    scope.querySelectorAll('.mask-number').forEach((input) => {
        input.addEventListener('input', () => {
            const value = input.value.replace(/[^\d,]/g, '').replace(/(,.*?),/g, '$1');
            input.value = value;
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    applyMasks();
});
