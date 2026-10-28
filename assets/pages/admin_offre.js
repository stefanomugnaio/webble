document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('offer-lines');

    if (!container) {
        return;
    }

    const library = document.getElementById('task-library');
    const addTaskButton = document.getElementById('add-task-line');
    const addEmptyButton = document.getElementById('add-empty-line');
    const emptyState = document.getElementById('offer-lines-empty');
    const vatInput = document.querySelector('[name$="[tauxTva]"]');
    const currency = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' });

    const field = (line, suffix) => line.querySelector(`[name$="[${suffix}]"]`);

    const updateTotals = () => {
        let totalHt = 0;
        const lines = [...container.querySelectorAll('[data-offer-line]')];

        lines.forEach((line, index) => {
            const quantity = Number.parseFloat(field(line, 'quantite')?.value || '0');
            const price = Number.parseFloat(field(line, 'prixUnitaire')?.value || '0');
            const free = field(line, 'offerte')?.checked ?? false;
            const total = free ? 0 : quantity * price;
            const position = field(line, 'position');

            if (position) position.value = index;
            line.querySelector('.offer-line__number').textContent = `Ligne ${index + 1}`;
            line.querySelector('[data-line-total]').textContent = free ? 'Offert' : currency.format(total);
            totalHt += total;
        });

        const vat = Number.parseFloat(vatInput?.value || '0');
        const vatAmount = totalHt * vat / 100;
        document.getElementById('offer-total-ht').textContent = currency.format(totalHt);
        document.getElementById('offer-total-vat').textContent = currency.format(vatAmount);
        document.getElementById('offer-total-ttc').textContent = currency.format(totalHt + vatAmount);
        emptyState?.classList.toggle('d-none', lines.length > 0);
    };

    const bindLine = (line) => {
        line.querySelector('[data-remove-line]')?.addEventListener('click', () => {
            line.remove();
            updateTotals();
        });
        line.querySelectorAll('input, textarea').forEach((input) => input.addEventListener('input', updateTotals));
        field(line, 'offerte')?.addEventListener('change', updateTotals);
    };

    const addLine = (task = null) => {
        const index = Number.parseInt(container.dataset.index || '0', 10);
        const wrapper = document.createElement('div');
        wrapper.className = 'offer-line';
        wrapper.dataset.offerLine = '';
        wrapper.innerHTML = `<div class="offer-line__head"><span class="offer-line__number"></span><button type="button" class="btn btn-sm btn-outline-danger" data-remove-line><i class="bi bi-trash me-1"></i>Retirer</button></div><div class="row g-3">${container.dataset.prototype.replace(/__name__/g, index)}</div><div class="offer-line__total">Total de la ligne : <strong data-line-total>0,00 €</strong></div>`;
        container.dataset.index = String(index + 1);
        container.appendChild(wrapper);

        const setColumn = (suffix, classes) => field(wrapper, suffix)?.closest('.mb-3')?.classList.add(...classes.split(' '));
        setColumn('libelle', 'col-lg-6');
        setColumn('quantite', 'col-sm-4 col-lg-2');
        setColumn('prixUnitaire', 'col-sm-4 col-lg-2');
        setColumn('offerte', 'col-sm-4 col-lg-2');

        if (task) {
            field(wrapper, 'libelle').value = task.dataset.label || '';
            field(wrapper, 'quantite').value = task.dataset.quantity || '1';
            field(wrapper, 'prixUnitaire').value = task.dataset.price || '0';
            field(wrapper, 'offerte').checked = task.dataset.free === '1';
        }

        bindLine(wrapper);
        updateTotals();
        field(wrapper, 'libelle')?.focus();
    };

    container.querySelectorAll('[data-offer-line]').forEach(bindLine);
    vatInput?.addEventListener('input', updateTotals);
    addTaskButton?.addEventListener('click', () => {
        const option = library?.selectedOptions[0];
        if (!option?.value) {
            library?.focus();
            return;
        }
        addLine(option);
        library.value = '';
    });
    addEmptyButton?.addEventListener('click', () => addLine());
    updateTotals();
});
