
document.addEventListener('DOMContentLoaded', () => {
    const firstError = document.querySelector('.form-error');

    if (!firstError) return;

    firstError.scrollIntoView({
        behavior: 'smooth',
        block: 'center'
    });

    const input = firstError
        .closest('.form-group')
        ?.querySelector('input, select, textarea');

    input?.focus();
});