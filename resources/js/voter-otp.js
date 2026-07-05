/**
 * Shared OTP-entry page behaviour.
 */
document.addEventListener('DOMContentLoaded', () => {
    const boxes = document.querySelectorAll('.otp-box');
    const hidden = document.getElementById('otpHidden');

    if (!boxes.length || !hidden) return;

    const syncHidden = () => {
        hidden.value = [...boxes].map((b) => b.value).join('');
    };

    boxes.forEach((box, i) => {
        box.addEventListener('input', (e) => {
            const val = e.target.value.replace(/\D/g, '').slice(-1);
            box.value = val;
            box.classList.toggle('filled', val !== '');
            syncHidden();
            if (val && i < boxes.length - 1) boxes[i + 1].focus();
        });

        box.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !box.value && i > 0) {
                boxes[i - 1].value = '';
                boxes[i - 1].classList.remove('filled');
                boxes[i - 1].focus();
                syncHidden();
            }
            if (e.key === 'ArrowLeft' && i > 0) boxes[i - 1].focus();
            if (e.key === 'ArrowRight' && i < boxes.length - 1) boxes[i + 1].focus();
        });

        box.addEventListener('paste', (e) => {
            e.preventDefault();
            const digits = (e.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, boxes.length);
            [...digits].forEach((d, j) => {
                if (boxes[j]) {
                    boxes[j].value = d;
                    boxes[j].classList.add('filled');
                }
            });
            syncHidden();
            const next = Math.min(digits.length, boxes.length - 1);
            boxes[next].focus();
        });
    });

    // Countdown for resend cooldown
    let seconds = 60;
    const resendBtn = document.getElementById('resendBtn');
    const countdownText = document.getElementById('cdText');

    if (resendBtn && countdownText) {
        const timer = setInterval(() => {
            seconds--;
            countdownText.textContent = seconds > 0 ? `Resend in ${seconds}s` : '';
            if (seconds <= 0) {
                resendBtn.disabled = false;
                resendBtn.style.opacity = '1';
                clearInterval(timer);
            }
        }, 1000);
    }
});
