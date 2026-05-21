document.addEventListener('DOMContentLoaded', () => {
    const password = document.getElementById('password');
    const confirm = document.getElementById('confirm_password');
    const confirmError = document.getElementById('confirm-error');
    const strengthLabel = document.getElementById('strength-label');

    const requirements = {
        length: { el: document.getElementById('req-length'), regex: /.{8,}/ },
        digit: { el: document.getElementById('req-digit'), regex: /\d/ },
        lower: { el: document.getElementById('req-lower'), regex: /[a-z]/ },
        upper: { el: document.getElementById('req-upper'), regex: /[A-Z]/ },
        special: { el: document.getElementById('req-special'), regex: /[\W_]/ }
    };

    const strengthReq = document.getElementById('req-strength');

    // Toggle Visibility for all password fields
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', () => {
            const targetId = icon.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                targetInput.type = 'password';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            }
        });
    });

    // Real-time New Password Validation
    password.addEventListener('input', () => {
        const val = password.value;
        let passed = 0;

        for (const key in requirements) {
            const isValid = requirements[key].regex.test(val);
            const icon = requirements[key].el.querySelector('i');
            
            if (isValid) {
                requirements[key].el.classList.add('valid');
                icon.classList.replace('fa-times-circle', 'fa-check-circle');
                passed++;
            } else {
                requirements[key].el.classList.remove('valid');
                icon.classList.replace('fa-check-circle', 'fa-times-circle');
            }
        }

        if (passed <= 2) {
            strengthLabel.innerText = 'Weak';
            strengthReq.classList.remove('valid');
        } else if (passed <= 4) {
            strengthLabel.innerText = 'Moderate';
            strengthReq.classList.remove('valid');
        } else {
            strengthLabel.innerText = 'Strong';
            strengthReq.classList.add('valid');
        }
    });

    // Confirmation Match Validation
    confirm.addEventListener('input', () => {
        if (confirm.value !== password.value && confirm.value !== '') {
            confirmError.classList.remove('hidden');
        } else {
            confirmError.classList.add('hidden');
        }
    });
});