<?php
$page_title = 'Sign Up';
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$error = $_SESSION['signup_error'] ?? '';
unset($_SESSION['signup_error']);
require 'includes/header.php';
?>

<div class="signup-page">
    <div class="form-card form-card-wide animate-in">
        <div class="auth-header">
            <div class="auth-icon"><i class="fas fa-star"></i></div>
            <h1 class="form-title">Create Account</h1>
            <p class="form-subtitle">Join thousands of Ethiopian shoppers on Habesha Market</p>
        </div>

        <?php if ($error): ?>
            <div class="alert-custom alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form id="signup-form" method="POST" action="actions/signup_action.php">
            <div class="grid-2">
                <div class="form-group-custom grid-full">
                    <label class="form-label-custom" for="full_name">
                        <i class="fas fa-user text-primary-light"></i> Full Name
                    </label>
                    <input type="text" id="full_name" name="full_name" class="form-control-custom"
                        placeholder="Abebe Girma" required>
                    <div class="field-msg" id="name-msg"></div>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom" for="email">
                        <i class="fas fa-envelope text-primary-light"></i> Email
                    </label>
                    <input type="email" id="email" name="email" class="form-control-custom"
                        placeholder="abebe@email.com" required autocomplete="email">
                    <div class="field-msg" id="email-msg"></div>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom" for="phone">
                        <i class="fas fa-phone text-primary-light"></i> Phone
                    </label>
                    <input type="tel" id="phone" name="phone" class="form-control-custom"
                        placeholder="+251 911 000 000" required>
                    <div class="field-msg" id="phone-msg"></div>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom" for="city">
                        <i class="fas fa-map-marker-alt text-primary-light"></i> City
                    </label>
                    <select name="city" id="city" class="form-control-custom">
                        <option value="Addis Ababa">Addis Ababa</option>
                        <option value="Hawassa">Hawassa</option>
                        <option value="Bahir Dar">Bahir Dar</option>
                        <option value="Mekelle">Mekelle</option>
                        <option value="Dire Dawa">Dire Dawa</option>
                        <option value="Gondar">Gondar</option>
                        <option value="Jimma">Jimma</option>
                        <option value="Adama">Adama (Nazret)</option>
                        <option value="Dessie">Dessie</option>
                        <option value="Shashamane">Shashamane</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group-custom grid-full">
                    <label class="form-label-custom" for="address">
                        <i class="fas fa-home text-primary-light"></i> Address
                    </label>
                    <input type="text" id="address" name="address" class="form-control-custom"
                        placeholder="Kebele, Sub-city, or Woreda">
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom" for="password">
                        <i class="fas fa-lock text-primary-light"></i> Password
                    </label>
                    <div class="password-toggle">
                        <input type="password" id="password" name="password" class="form-control-custom"
                            placeholder="Min. 8 characters" required>
                        <i class="fas fa-eye toggle-icon"></i>
                    </div>
                    <div class="field-msg" id="pass-msg"></div>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom" for="confirm_password">
                        <i class="fas fa-lock text-primary-light"></i> Confirm Password
                    </label>
                    <div class="password-toggle">
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control-custom"
                            placeholder="Repeat password" required>
                        <i class="fas fa-eye toggle-icon"></i>
                    </div>
                    <div class="field-msg" id="cpass-msg"></div>
                </div>
            </div>

            <div class="auth-checkbox-row">
                <input type="checkbox" id="terms" name="terms" required class="auth-checkbox">
                <label for="terms">I agree to the <a href="#" class="auth-terms-link">Terms of Service</a> and <a href="#" class="auth-terms-link">Privacy Policy</a>.</label>
            </div>

            <button type="submit" class="btn-primary-custom auth-submit-no-top">
                <span><i class="fas fa-user-plus"></i> Create My Account</span>
            </button>
        </form>

        <div class="form-divider"><span>already have an account?</span></div>

        <div class="text-center">
            <a href="login.php" class="btn-outline-custom auth-compact-link">
                <i class="fas fa-sign-in-alt"></i> Sign In Instead
            </a>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
