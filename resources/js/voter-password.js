/**
 * Voter "Change Password" page behaviour.
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const target = document.getElementById(btn.dataset.togglePassword);
            if (!target) return;

            const isPassword = target.type === 'password';
            target.type = isPassword ? 'text' : 'password';
            btn.classList.toggle('is-visible', isPassword);
            btn.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
        });
    });

    const newPassword = document.getElementById('new_password');
    const strengthFill = document.getElementById('passwordStrengthFill');
    const strengthLabel = document.getElementById('passwordStrengthLabel');
    const requirementItems = document.querySelectorAll('[data-requirement]');

    if (!newPassword) return;

    const LABELS = { 0: '', 1: 'Weak', 2: 'Fair', 3: 'Good', 4: 'Strong' };

    const checks = {
        length: (v) => v.length >= 8,
        upper: (v) => /[A-Z]/.test(v),
        number: (v) => /[0-9]/.test(v),
        symbol: (v) => /[^A-Za-z0-9]/.test(v),
    };

    newPassword.addEventListener('input', () => {
        const value = newPassword.value;
        let score = 0;

        requirementItems.forEach((item) => {
            const rule = item.dataset.requirement;
            const met = checks[rule]?.(value) ?? false;
            item.classList.toggle('met', met);
            if (met) score++;
        });

        if (strengthFill) strengthFill.dataset.level = String(score);
        if (strengthLabel) strengthLabel.textContent = LABELS[score] ?? '';
    });
});
