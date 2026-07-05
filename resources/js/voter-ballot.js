/**
 * Ballot page behaviour.
 */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('ballotForm');
    if (!form) return;

    const totalPositions = Number(form.dataset.totalPositions || 0);
    const voted = new Set();

    const progressBar = document.getElementById('progressBar');
    const progressLabel = document.getElementById('progressLabel');

    const updateProgress = () => {
        const pct = totalPositions > 0 ? (voted.size / totalPositions) * 100 : 0;
        if (progressBar) progressBar.style.width = pct + '%';
        if (progressLabel) progressLabel.textContent = `${voted.size} of ${totalPositions} positions`;
    };

    form.querySelectorAll('input[type="radio"][data-position]').forEach((input) => {
        input.addEventListener('change', () => {
            voted.add(input.dataset.position);
            updateProgress();

            const checkEl = document.getElementById('check-' + input.dataset.position);
            if (checkEl) checkEl.style.background = 'var(--success)';
        });
    });

    const submitBtn = document.getElementById('submitBtn');
    submitBtn?.addEventListener('click', (e) => {
        if (!confirm('Submit your votes? Once submitted, they cannot be changed.')) {
            e.preventDefault();
        }
    });
});
