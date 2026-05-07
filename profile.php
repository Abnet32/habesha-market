<?php
$page_title = 'My Profile';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once 'connection.php';
global $con;

$uid = (int)$_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM users WHERE id=$uid"));

$success = $_SESSION['profile_success'] ?? '';
$error = $_SESSION['profile_error'] ?? '';
unset($_SESSION['profile_success'], $_SESSION['profile_error']);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = mysqli_real_escape_string($con, trim($_POST['full_name']));
    $phone = mysqli_real_escape_string($con, trim($_POST['phone']));
    $city = mysqli_real_escape_string($con, trim($_POST['city']));
    $address = mysqli_real_escape_string($con, trim($_POST['address']));

    if (strlen($full_name) < 3) {
        $_SESSION['profile_error'] = 'Name must be at least 3 characters.';
    } else {
        mysqli_query($con, "UPDATE users SET full_name='$full_name', phone='$phone', city='$city', address='$address' WHERE id=$uid");
        $_SESSION['user_name'] = $full_name;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['profile_success'] = 'Profile updated successfully!';
    }
    header('Location: profile.php');
    exit;
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_new_password'];

    if (!password_verify($current, $user['password'])) {
        $_SESSION['profile_error'] = 'Current password is incorrect.';
    } elseif (strlen($new) < 8) {
        $_SESSION['profile_error'] = 'New password must be at least 8 characters.';
    } elseif ($new !== $confirm) {
        $_SESSION['profile_error'] = 'Passwords do not match.';
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        mysqli_query($con, "UPDATE users SET password='$hashed' WHERE id=$uid");
        $_SESSION['profile_success'] = 'Password changed successfully!';
    }
    header('Location: profile.php');
    exit;
}

// Stats
$order_count = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM orders WHERE user_id=$uid"))['c'];
$cart_count = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM cart_items WHERE user_id=$uid"))['c'];

require 'includes/header.php';
?>

<div class="page-hero">
    <div class="container-custom">
        <div class="section-badge"><i class="fas fa-user"></i> Account</div>
        <h1 class="page-hero-title">My Profile</h1>
        <div class="breadcrumb-custom">
            <a href="index.php">Home</a> <i class="fas fa-chevron-right fa-xs"></i>
            <span>Profile</span>
        </div>
    </div>
</div>

<div class="container-custom" style="padding-bottom:5rem">
    <div class="profile-layout">
        <!-- SIDEBAR -->
        <div class="profile-sidebar animate-in">
            <div class="profile-header-card">
                <div class="profile-avatar"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></div>
                <div style="font-weight:700; font-size:1rem; color:white;"><?= htmlspecialchars($user['full_name']) ?></div>
                <div style="font-size:0.8rem; color:white; margin-top:3px; "><?= htmlspecialchars($user['email']) ?></div>
                <div style="display:flex; justify-content:center; gap:1.5rem; margin-top:1rem;">
                    <div style="text-align:center;">
                        <div style="font-weight:800; font-size:1.2rem; color:var(--secondary);"><?= $order_count ?></div>
                        <div style="font-size:0.72rem; color:white;">Orders</div>
                    </div>
                    <div style="text-align:center;">
                        <div style="font-weight:800; font-size:1.2rem; color:var(--secondary);"><?= $cart_count ?></div>
                        <div style="font-size:0.72rem; color:white;">In Cart</div>
                    </div>
                </div>
            </div>
            <nav class="profile-nav">
                <a href="profile.php" class="profile-nav-item active">
                    <i class="fas fa-user"></i> My Profile
                </a>
                <a href="orders.php" class="profile-nav-item">
                    <i class="fas fa-box"></i> My Orders
                </a>
                <a href="cart.php" class="profile-nav-item">
                    <i class="fas fa-shopping-cart"></i> My Cart
                </a>
                <a href="logout.php" class="profile-nav-item" style="color:var(--accent);">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <!-- MAIN CONTENT -->
        <div>
            <?php if ($success): ?>
                <div class="alert-custom alert-success animate-in"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert-custom alert-error animate-in"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Profile Info -->
            <div class="form-card" style="max-width:none; margin-bottom:1.5rem;" >
                <h2 style="font-weight:700; font-size:1.1rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-user" style="color:var(--primary-light)"></i> Personal Information
                </h2>
                <form method="POST">
                    <input type="hidden" name="update_profile" value="1">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div class="form-group-custom" style="grid-column:1/-1">
                            <label class="form-label-custom">Full Name</label>
                            <input type="text" name="full_name" class="form-control-custom"
                                value="<?= htmlspecialchars($user['full_name']) ?>" required>
                        </div>
                        <div class="form-group-custom">
                            <label class="form-label-custom">Email Address</label>
                            <input type="email" class="form-control-custom"
                                value="<?= htmlspecialchars($user['email']) ?>" readonly style="opacity:0.6">
                            <div class="field-msg" style="color:var(--text-muted)">Email cannot be changed</div>
                        </div>
                        <div class="form-group-custom">
                            <label class="form-label-custom">Phone Number</label>
                            <input type="tel" name="phone" class="form-control-custom"
                                value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                placeholder="+251 911 000 000">
                        </div>
                        <div class="form-group-custom">
                            <label class="form-label-custom">City</label>
                            <select name="city" class="form-control-custom">
                                <?php foreach (['Addis Ababa','Hawassa','Bahir Dar','Mekelle','Dire Dawa','Gondar','Jimma','Adama','Dessie','Shashamane','Other'] as $c): ?>
                                    <option value="<?= $c ?>" <?= ($user['city'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group-custom" style="grid-column:1/-1">
                            <label class="form-label-custom">Address</label>
                            <textarea name="address" class="form-control-custom" rows="2"
                                placeholder="Kebele, Sub-city, Woreda..."><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary-custom" style="border:none; cursor:pointer; margin-top:0.5rem;">
                        <span><i class="fas fa-save"></i> Save Changes</span>
                    </button>
                </form>
            </div>

            <!-- Change Password -->
            <div class="form-card" style="max-width:none;">
                <h2 style="font-weight:700; font-size:1.1rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-lock" style="color:var(--primary-light)"></i> Change Password
                </h2>
                <form method="POST">
                    <input type="hidden" name="change_password" value="1">
                    <div class="form-group-custom">
                        <label class="form-label-custom">Current Password</label>
                        <div class="password-toggle">
                            <input type="password" name="current_password" class="form-control-custom"
                                placeholder="Your current password" required>
                            <i class="fas fa-eye toggle-icon"></i>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div class="form-group-custom">
                            <label class="form-label-custom">New Password</label>
                            <div class="password-toggle">
                                <input type="password" name="new_password" class="form-control-custom"
                                    placeholder="Min. 8 characters" required>
                                <i class="fas fa-eye toggle-icon"></i>
                            </div>
                        </div>
                        <div class="form-group-custom">
                            <label class="form-label-custom">Confirm New Password</label>
                            <div class="password-toggle">
                                <input type="password" name="confirm_new_password" class="form-control-custom"
                                    placeholder="Repeat new password" required>
                                <i class="fas fa-eye toggle-icon"></i>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary-custom" style="border:none; cursor:pointer; margin-top:0.5rem;">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
