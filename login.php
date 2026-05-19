<?php
$page_title = 'Login';
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
require 'includes/header.php';
?>

<div class="login-page">
    <div class="form-card animate-in">
        <div class="auth-header">
            <div class="auth-icon"><i class="fas fa-store"></i></div>
            <h1 class="form-title">Welcome Back</h1>
            <p class="form-subtitle">Sign in to your Habesha Mark</p>
        </div>

        <?php if ($error): ?>
            <div class="alert-custom alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['signup_success'])): ?>
            <div class="alert-custom alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['signup_success']) ?>
            </div>
            <?php unset($_SESSION['signup_success']); ?>
        <?php endif; ?>

        <form id="login-form" method="POST" action="actions/login_action.php">
            <div class="form-group-custom">
                <label class="form-label-custom" for="login-email">
                    <i class="fas fa-envelope text-primary-light"></i> Email Address
                </label>
                <input type="email" id="login-email" name="email" class="form-control-custom"
                    placeholder="your@email.com" required autocomplete="email">
                <div class="field-msg" id="login-email-msg"></div>
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom" for="login-password">
                    <i class="fas fa-lock text-primary-light"></i> Password
                </label>
                <div class="password-toggle">
                    <input type="password" id="login-password" name="password" class="form-control-custom"
                        placeholder="Enter your password" required autocomplete="current-password">
                    <i class="fas fa-eye toggle-icon"></i>
                </div>
                <div class="field-msg" id="login-pass-msg"></div>
            </div>

            <button type="submit" class="btn-primary-custom auth-submit">
                <span><i class="fas fa-sign-in-alt"></i> Sign In</span>
            </button>
        </form>

        <div class="form-divider"><span>or</span></div>

        <div class="auth-footer">
            Don't have an account?
            <a href="signup.php" class="auth-footer-link"> Create one free</a>
        </div>

        <div class="auth-note">
            <strong class="auth-note-title">Demo accounts:</strong><br>
            Email: <code class="auth-code">abebe@gmail.com</code> &nbsp;|&nbsp; Password: <code class="auth-code">abebe123@#$</code>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
