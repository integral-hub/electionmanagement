/**
 * Voter list filter.
 *
 * The voters index/assign tables are rendered server-side (with
 * pagination), but the q/status/batch_code filters just need to
 * narrow down what's already on the page, only filter the rendered rows in
 * the browser. Falls back to nothing if the expected elements
 * aren't present (safe to include on any page).
 */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-voter-filter]');
    if (!form) return;

    const searchInput = form.querySelector('[data-filter-q]');
    const statusSelect = form.querySelector('[data-filter-status]');
    const batchSelect = form.querySelector('[data-filter-batch]');
    const rows = document.querySelectorAll('[data-voter-row]');
    const emptyNote = document.getElementById('filterEmptyNote');

    // No live filtering possible without rows or at least one control.
    if (!rows.length || (!searchInput && !statusSelect && !batchSelect)) return;

    // This is a client-side narrowing of the current page only —
    // submitting the form still does a normal (debounced) server
    // search across the full result set.
    const applyFilter = () => {
        const q = (searchInput?.value || '').trim().toLowerCase();
        const status = statusSelect?.value || '';
        const batch = batchSelect?.value || '';
        let visibleCount = 0;

        rows.forEach((row) => {
            const haystack = (row.dataset.search || '').toLowerCase();
            const matchesQ = !q || haystack.includes(q);
            const matchesStatus = !status || row.dataset.status === status;
            const matchesBatch = !batch || row.dataset.batch === batch;
            const visible = matchesQ && matchesStatus && matchesBatch;

            row.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });

        if (emptyNote) {
            emptyNote.style.display = visibleCount === 0 ? '' : 'none';
        }
    };

    searchInput?.addEventListener('input', applyFilter);
    statusSelect?.addEventListener('change', applyFilter);
    batchSelect?.addEventListener('change', applyFilter);

    // Prevent an accidental full-page submit for what's meant to be
    // instant, client-side filtering (the "Search across all voters"
    // link still does a real server search when the person needs it).
    form.addEventListener('submit', (e) => {
        if (form.dataset.instantOnly === 'true') e.preventDefault();
    });
});
