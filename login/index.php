<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colorlib Sign Up & Sign In Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="background-effects">
        <div class="glow-orb glow-orb-1"></div>
        <div class="glow-orb glow-orb-2"></div>
        <div class="glow-orb glow-orb-3"></div>
    </div>
    <div class="colorlib-card">
        <div class="view-panel active-panel"
             id="signupView">
            <div class="form-column">
                <h2 class="form-title">Sign up</h2>

                <form id="signupForm" action="process.php" method="POST" novalidate>
    <div class="input-group" id="group-signup-name">
        <i class="fa-solid fa-user input-icon"></i>
        <input type="text" name="fullname" id="signupName" class="custom-input" placeholder="Your Name" required autocomplete="name">
    </div>

    <div class="input-group" id="group-signup-email">
        <i class="fa-solid fa-envelope input-icon"></i>
        <input type="email" name="email" id="signupEmail" class="custom-input" placeholder="Your Email" required autocomplete="email">
    </div>

    <div class="input-group" id="group-signup-pass">
        <i class="fa-solid fa-lock input-icon"></i>
        <input type="password" name="pswd" id="signupPassword" class="custom-input" placeholder="Password" required autocomplete="new-password">
    </div>

    <div class="input-group" id="group-signup-repeat">
        <i class="fa-solid fa-lock input-icon"></i>
        <input type="password" id="signupRepeatPassword" class="custom-input" placeholder="Repeat your password" autocomplete="new-password">
    </div>

    <label class="checkbox-container">
        <input type="checkbox" id="signupTerms">
        <span class="checkmark"></span> I agree all statements in
        <a href="#" class="terms-link" id="openTermsModal"> Terms of service </a>
    </label>

    <button type="submit" name="registration" class="btn-submit" id="btnRegister">
        <span>Register</span>
    </button>
</form>

            </div>
            <div class="illustration-column">
                <div class="image-container">
                    <img src=""alt="Sign up illustration">
                </div>
                <a href="#" class="switch-link"  id="linkToSignin">I am already member</a>
            </div>
        </div>
        <div class="view-panel hidden-panel"
             id="signinView">
            <div class="illustration-column">
                <div class="image-container">
                    <img src=""  alt="Sign in illustration">
                </div>
                <a href="#" class="switch-link" id="linkToSignup">Create an account</a> </div>
            <!-- Sign In Form -->
            <div class="form-column">
                <h2 class="form-title">Sign in</h2>
                <form id="signinForm" novalidate>
                    <div class="input-group"
                         id="group-signin-name">
                        <i class="fa-solid fa-user input-icon"></i>
                        <input type="text" id="signinName" class="custom-input" placeholder="Your Email" autocomplete="username">
                        <span class="error-text"> Please enter your username  </span>
                    </div>
                    <div class="input-group" id="group-signin-pass">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="signinPassword"class="custom-input"  placeholder="Password"autocomplete="current-password">
                        <span class="error-text"> Password is required   </span>
                    </div>
                    <label class="checkbox-container">
                        <input type="checkbox" id="signinRemember" checked>
                        <span class="checkmark"></span>Remember me
                    </label>

                    <button type="submit" class="btn-submit" id="btnLogin">
                        <span>Log in </span>
                    </button>
                </form>
                <div class="social-login-wrapper">
                    <span class="social-title">
                        Or login with
                    </span>
                    <button type="button" class="social-btn facebook" title="Login with Facebook" aria-label="Facebook Login">
                        <i class="fa-brands fa-facebook-f"></i>
                    </button>
                    <button type="button" class="social-btn twitter" title="Login with Twitter" aria-label="Twitter Login">
                        <i class="fa-brands fa-twitter"></i>
                    </button>
                    <button type="button"
                            class="social-btn google"
                            title="Login with Google"
                            aria-label="Google Login">

                        <i class="fa-brands fa-google"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast" id="toast">
        <i class="fa-solid fa-circle-check text-green-500 text-xl"
           id="toastIcon">
        </i>
        <div>
            <h4 class="font-semibold text-sm text-gray-800" id="toastTitle">  Notification </h4>

            <p class="text-xs text-gray-500" id="toastMessage"> Action completed successfully. </p>
        </div>
    </div>

    <div class="modal-overlay" id="termsModal">
 <div class="modal-card">
            <h3 class="text-xl font-bold text-gray-800 mb-3">Terms of Service </h3>
            <p class="text-xs text-gray-600 mb-4 leading-relaxed">
                By creating an account on this portal, you agree to abide by
                our community guidelines, data protection policies, and security
                regulations. We keep your credentials strictly confidential
                and encrypted.
            </p>
            <div class="text-right">

                <button type="button"
                        class="btn-submit"
                        id="closeTermsModal"
                        style="padding: 8px 20px; font-size: 12px;">
                    Understood
                </button>
            </div>
        </div>
    </div>
    <!-- <script src="script.js"></script> -->
</body>
</html>