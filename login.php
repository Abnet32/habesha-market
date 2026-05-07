<?php
$page_title = 'Login';
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
require 'includes/header.php';
?>

<div style="min-height:100vh; display:flex; align-items:center; justify-content:center; padding: 100px 1rem 3rem;">
    <div class="form-card animate-in">
        <div style="text-align:center; margin-bottom:0.5rem;">
            <div style="font-size:3rem; margin-bottom:0.5rem;">🏪</div>
            <h1 class="form-title">Welcome Back</h1>
            <p class="form-subtitle">Sign in to your Habesha Market account</p>
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
                    <i class="fas fa-envelope" style="color:var(--primary-light); margin-right:5px;"></i> Email Address
                </label>
                <input type="email" id="login-email" name="email" class="form-control-custom"
                    placeholder="your@email.com" required autocomplete="email">
                <div class="field-msg" id="login-email-msg"></div>
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom" for="login-password">
                    <i class="fas fa-lock" style="color:var(--primary-light); margin-right:5px;"></i> Password
                </label>
                <div class="password-toggle">
                    <input type="password" id="login-password" name="password" class="form-control-custom"
                        placeholder="Enter your password" required autocomplete="current-password">
                    <i class="fas fa-eye toggle-icon"></i>
                </div>
                <div class="field-msg" id="login-pass-msg"></div>
            </div>

            <button type="submit" class="btn-primary-custom" style="width:100%; justify-content:center; border:none; cursor:pointer; margin-top:0.5rem;">
                <span><i class="fas fa-sign-in-alt"></i> Sign In</span>
            </button>
        </form>

        <div class="form-divider"><span>or</span></div>

        <div style="text-align:center; font-size:0.88rem; color:var(--text-muted);">
            Don't have an account?
            <a href="signup.php" style="color:var(--primary-light); font-weight:600; text-decoration:none;"> Create one free</a>
        </div>

        <div style="margin-top:1.5rem; padding:1rem; background:rgba(252,221,9,0.06); border:1px solid rgba(252,221,9,0.15); border-radius:var(--radius-sm); font-size:0.8rem; color:var(--text-muted);">
            <strong style="color:var(--secondary);">Demo accounts:</strong><br>
            Email: <code style="color:var(--primary-light)">abebe@example.com</code> &nbsp;|&nbsp; Password: <code style="color:var(--primary-light)">password</code>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
