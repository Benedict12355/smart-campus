document.addEventListener('DOMContentLoaded', () => {

    const signupView = document.getElementById('signupView');
    const signinView = document.getElementById('signinView');
    const linkToSignin = document.getElementById('linkToSignin');
    const linkToSignup = document.getElementById('linkToSignup');
    const signupForm = document.getElementById('signupForm');
    const signinForm = document.getElementById('signinForm');
    const toast = document.getElementById('toast');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');
    const termsModal = document.getElementById('termsModal');
    const openTermsModal = document.getElementById('openTermsModal');
    const closeTermsModal = document.getElementById('closeTermsModal');

    function switchToSignIn() {
        signupView.classList.remove('active-panel');
        signupView.classList.add('hidden-panel');
        setTimeout(() => {
            signinView.classList.remove('hidden-panel');
            signinView.classList.add('active-panel');
        }, 150);
    }

    function switchToSignUp() {
        signinView.classList.remove('active-panel');
        signinView.classList.add('hidden-panel');
        setTimeout(() => {
            signupView.classList.remove('hidden-panel');
            signupView.classList.add('active-panel');
        }, 150);
    }

    linkToSignin.addEventListener('click', (e) => { e.preventDefault(); switchToSignIn(); });
    linkToSignup.addEventListener('click', (e) => { e.preventDefault(); switchToSignUp(); });

    function showToast(title, message, isSuccess = true) {
        toastTitle.innerText = title;
        toastMessage.innerText = message;
        toastIcon.className = isSuccess
            ? 'fa-solid fa-circle-check text-green-500 text-xl'
            : 'fa-solid fa-circle-exclamation text-red-500 text-xl';
        toast.classList.add('show');
        setTimeout(() => { toast.classList.remove('show'); }, 3500);
    }

    openTermsModal.addEventListener('click', (e) => { e.preventDefault(); termsModal.classList.add('active'); });
    closeTermsModal.addEventListener('click', () => { termsModal.classList.remove('active'); });
    termsModal.addEventListener('click', (e) => { if (e.target === termsModal) termsModal.classList.remove('active'); });

    document.querySelectorAll('.custom-input').forEach(input => {
        input.addEventListener('input', () => {
            const group = input.closest('.input-group');
            if (group) group.classList.remove('error');
        });
    });

    signupForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const nameVal = document.getElementById('signupName').value.trim();
        const emailVal = document.getElementById('signupEmail').value.trim();
        const passVal = document.getElementById('signupPassword').value;
        const repeatPassVal = document.getElementById('signupRepeatPassword').value;
        const termsChecked = document.getElementById('signupTerms').checked;

        let isValid = true;
        if (!nameVal) { document.getElementById('group-signup-name').classList.add('error'); isValid = false; }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailVal || !emailRegex.test(emailVal)) { document.getElementById('group-signup-email').classList.add('error'); isValid = false; }

        if (!passVal || passVal.length < 6) { document.getElementById('group-signup-pass').classList.add('error'); isValid = false; }
        if (passVal !== repeatPassVal || !repeatPassVal) { document.getElementById('group-signup-repeat').classList.add('error'); isValid = false; }

        if (!isValid) { showToast('Validation Error', 'Please correct the highlighted fields.', false); return; }
        if (!termsChecked) { showToast('Terms Required', 'Please accept the Terms of service to proceed.', false); return; }

        const formData = new FormData();
        formData.append('registration', '1');
        formData.append('fullname', nameVal);
        formData.append('email', emailVal);
        formData.append('pswd', passVal);

        fetch('process.php', { method: 'POST', body: formData })
            .then(r => r.text())
            .then(data => {
                if (data.trim() === 'success') {
                    showToast('Registration Successful!', 'Welcome aboard!');
                    signupForm.reset();
                    setTimeout(switchToSignIn, 1500);
                } else {
                    showToast('Registration Failed', data.trim() || 'Unable to create your account.', false);
                }
            })
            .catch(() => showToast('Error', 'Server connection failed.', false));
    });

    signinForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const nameVal = document.getElementById('signinName').value.trim();
        const passVal = document.getElementById('signinPassword').value;

        let isValid = true;
        if (!nameVal) { document.getElementById('group-signin-name').classList.add('error'); isValid = false; }
        if (!passVal) { document.getElementById('group-signin-pass').classList.add('error'); isValid = false; }
        if (!isValid) { showToast('Validation Error', 'Please enter your login details.', false); return; }

        const btn = document.getElementById('btnLogin');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> <span>Authenticating...</span>`;

        const formData = new FormData();
        formData.append('login', '1');
        formData.append('username', nameVal);
        formData.append('password', passVal);

        fetch('process.php', { method: 'POST', body: formData })
            .then(r => r.text())
            .then(data => {
                if (data.trim() === 'success') {
                    showToast('Welcome back!', 'Redirecting to your dashboard...');
                    setTimeout(() => { window.location.href = 'dashboard.php'; }, 800);
                } else {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    showToast('Login Failed', data.trim() || 'Invalid email or password.', false);
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                showToast('Error', 'Server connection failed.', false);
            });
    });

    document.querySelectorAll('.social-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const provider = btn.classList.contains('facebook') ? 'Facebook'
                          : btn.classList.contains('twitter')  ? 'Twitter' : 'Google';
            showToast('Social Login', `Initiating authentication via ${provider}...`);
        });
    });
});