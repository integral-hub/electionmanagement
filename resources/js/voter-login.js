/**
 * Voter login page behaviour.
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const input = btn.previousElementSibling;
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
        });
    });
});
