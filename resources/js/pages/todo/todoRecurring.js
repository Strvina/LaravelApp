document.addEventListener('DOMContentLoaded', function () {
    const recurring = document.getElementById('is_recurring');
    const recurrenceSelect = document.getElementById('recurrence');

    if (!recurring || !recurrenceSelect) return; // sigurnosna provera

    // inicijalno stanje
    recurrenceSelect.disabled = !recurring.checked;

    recurring.addEventListener('change', function () {
        recurrenceSelect.disabled = !this.checked;

        if (!this.checked) {
            recurrenceSelect.value = "";
            recurrenceSelect.selectedIndex = 0; // placeholder
        }
    });
});
