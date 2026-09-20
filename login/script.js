document.addEventListener('DOMContentLoaded', () => {

    // View Elements
    const signupView = document.getElementById('signupView');
    const signinView = document.getElementById('signinView');

    const linkToSignin = document.getElementById('linkToSignin');
    const linkToSignup = document.getElementById('linkToSignup');


    // Forms
    const signupForm = document.getElementById('signupForm');
    const signinForm = document.getElementById('signinForm');


    // Toast Elements
    const toast = document.getElementById('toast');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');


    // Terms Modal
    const termsModal = document.getElementById('termsModal');
    const openTermsModal = document.getElementById('openTermsModal');
    const closeTermsModal = document.getElementById('closeTermsModal');


    // Switch to Sign In
    function switchToSignIn() {

        signupView.classList.remove('active-panel');
        signupView.classList.add('hidden-panel');

        setTimeout(() => {

            signinView.classList.remove('hidden-panel');
            signinView.classList.add('active-panel');

        }, 150);
    }


    // Switch to Sign Up
    function switchToSignUp() {

        signinView.classList.remove('active-panel');
        signinView.classList.add('hidden-panel');

        setTimeout(() => {

            signupView.classList.remove('hidden-panel');
            signupView.classList.add('active-panel');

        }, 150);
    }


    // Sign In Link
    linkToSignin.addEventListener('click', (e) => {

        e.preventDefault();

        switchToSignIn();
    });


    // Sign Up Link
    linkToSignup.addEventListener('click', (e) => {

        e.preventDefault();

        switchToSignUp();
    });


    // Toast Function
    function showToast(title, message, isSuccess = true) {

        toastTitle.innerText = title;
        toastMessage.innerText = message;

        if (isSuccess) {

            toastIcon.className =
                'fa-solid fa-circle-check text-green-500 text-xl';

        } else {

            toastIcon.className =
                'fa-solid fa-circle-exclamation text-red-500 text-xl';
        }

        toast.classList.add('show');

        setTimeout(() => {

            toast.classList.remove('show');

        }, 3500);
    }


    // Open Terms Modal
    openTermsModal.addEventListener('click', (e) => {

        e.preventDefault();

        termsModal.classList.add('active');
    });


    // Close Terms Modal
    closeTermsModal.addEventListener('click', () => {

        termsModal.classList.remove('active');
    });


    // Close Modal when clicking outside
    termsModal.addEventListener('click', (e) => {

        if (e.target === termsModal) {

            termsModal.classList.remove('active');
        }
    });


    // Remove Error when Typing
    const allInputs =
        document.querySelectorAll('.custom-input');

    allInputs.forEach(input => {

        input.addEventListener('input', () => {

            const group =
                input.closest('.input-group');

            if (group) {

                group.classList.remove('error');
            }
        });
    });


    // Sign Up Validation
    signupForm.addEventListener('submit', (e) => {

        e.preventDefault();

        const nameVal =
            document.getElementById('signupName').value.trim();

        const emailVal =
            document.getElementById('signupEmail').value.trim();

        const passVal =
            document.getElementById('signupPassword').value;

        const repeatPassVal =
            document.getElementById('signupRepeatPassword').value;

        const termsChecked =
            document.getElementById('signupTerms').checked;


        let isValid = true;


        // Name
        if (!nameVal) {

            document
                .getElementById('group-signup-name')
                .classList.add('error');

            isValid = false;
        }


        // Email
        const emailRegex =
            /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailVal || !emailRegex.test(emailVal)) {

            document
                .getElementById('group-signup-email')
                .classList.add('error');

            isValid = false;
        }


        // Password
        if (!passVal || passVal.length < 6) {

            document
                .getElementById('group-signup-pass')
                .classList.add('error');

            isValid = false;
        }


        // Repeat Password
        if (passVal !== repeatPassVal || !repeatPassVal) {

            document
                .getElementById('group-signup-repeat')
                .classList.add('error');

            isValid = false;
        }


        // Terms
        if (!termsChecked) {

            showToast(
                'Terms Required',
                'Please accept the Terms of service to proceed.',
                false
            );

            return;
        }


        // Successful Validation
        if (isValid) {

            const btn =
                document.getElementById('btnRegister');

            const originalText =
                btn.innerHTML;

            btn.disabled = true;

            btn.innerHTML =
                `<i class="fa-solid fa-spinner fa-spin"></i>
                 <span>Registering...</span>`;


            setTimeout(() => {

                btn.disabled = false;

                btn.innerHTML = originalText;

                showToast(
                    'Registration Successful!',
                    'Welcome aboard! Redirecting to dashboard...'
                );

                signupForm.reset();

                setTimeout(
                    switchToSignIn,
                    1500
                );

            }, 1200);

        } else {

            showToast(
                'Validation Error',
                'Please correct the highlighted fields.',
                false
            );
        }
    });


    // Sign In Validation
    signinForm.addEventListener('submit', (e) => {

        e.preventDefault();

        const nameVal =
            document.getElementById('signinName').value.trim();

        const passVal =
            document.getElementById('signinPassword').value;


        let isValid = true;


        if (!nameVal) {

            document
                .getElementById('group-signin-name')
                .classList.add('error');

            isValid = false;
        }


        if (!passVal) {

            document
                .getElementById('group-signin-pass')
                .classList.add('error');

            isValid = false;
        }


        if (isValid) {

            const btn =
                document.getElementById('btnLogin');

            const originalText =
                btn.innerHTML;

            btn.disabled = true;

            btn.innerHTML =
                `<i class="fa-solid fa-spinner fa-spin"></i>
                 <span>Authenticating...</span>`;


            setTimeout(() => {

                btn.disabled = false;

                btn.innerHTML = originalText;

                showToast(
                    'Welcome back!',
                    `Logged in successfully as ${nameVal}.`
                );

            }, 1200);

        } else {

            showToast(
                'Login Failed',
                'Please fill in all required credentials.',
                false
            );
        }
    });


    // Social Login Buttons
    document
        .querySelectorAll('.social-btn')
        .forEach(btn => {

            btn.addEventListener('click', () => {

                const provider =
                    btn.classList.contains('facebook')
                        ? 'Facebook'
                        : btn.classList.contains('twitter')
                            ? 'Twitter'
                            : 'Google';

                showToast(
                    'Social Login',
                    `Initiating authentication via ${provider}...`
                );
            });
        });

});