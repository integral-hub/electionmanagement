/**
 * Assign-voters page behaviour.
 */
document.addEventListener('DOMContentLoaded', () => {
    const selectAll = document.getElementById('selectAll');
    const assignBtn = document.getElementById('assignBtn');
    const checkboxes = () => document.querySelectorAll('.voter-check');

    const updateBtn = () => {
        if (!assignBtn) return;
        const any = [...checkboxes()].some((cb) => cb.checked && cb.closest('[data-voter-row]')?.style.display !== 'none');
        assignBtn.disabled = !any;
        assignBtn.style.opacity = any ? '1' : '.4';
    };

    selectAll?.addEventListener('change', function () {
        checkboxes().forEach((cb) => {
            // Only select rows that are currently visible under the active filter.
            const visible = cb.closest('[data-voter-row]')?.style.display !== 'none';
            if (visible) cb.checked = this.checked;
        });
        updateBtn();
    });

    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('voter-check')) {
            if (selectAll) selectAll.checked = [...checkboxes()].every((cb) => cb.checked);
            updateBtn();
        }
    });
});
