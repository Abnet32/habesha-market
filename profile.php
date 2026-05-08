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

<div class="container-custom page-pad-bottom-5">
    <div class="profile-layout">
        <!-- SIDEBAR -->
        <div class="profile-sidebar animate-in">
            <div class="profile-header-card">
                <div class="profile-avatar"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></div>
                <div class="profile-header-name"><?= htmlspecialchars($user['full_name']) ?></div>
                <div class="profile-header-email"><?= htmlspecialchars($user['email']) ?></div>
                <div class="profile-meta-row">
                    <div class="profile-meta-block">
                        <div class="profile-meta-value"><?= $order_count ?></div>
                        <div class="profile-meta-label">Orders</div>
                    </div>
                    <div class="profile-meta-block">
                        <div class="profile-meta-value"><?= $cart_count ?></div>
                        <div class="profile-meta-label">In Cart</div>
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
                <a href="logout.php" class="profile-nav-item profile-nav-item-danger">
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
            <div class="form-card max-w-none mb-1-5" >
                <h2 class="section-heading-inline">
                    <i class="fas fa-user text-primary-light"></i> Personal Information
                </h2>
                <form method="POST">
                    <input type="hidden" name="update_profile" value="1">
                    <div class="grid-2">
                        <div class="form-group-custom grid-full">
                            <label class="form-label-custom">Full Name</label>
                            <input type="text" name="full_name" class="form-control-custom"
                                value="<?= htmlspecialchars($user['full_name']) ?>" required>
                        </div>
                        <div class="form-group-custom">
                            <label class="form-label-custom">Email Address</label>
                            <input type="email" class="form-control-custom opacity-60"
                                value="<?= htmlspecialchars($user['email']) ?>" readonly>
                            <div class="field-msg text-muted">Email cannot be changed</div>
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
                        <div class="form-group-custom grid-full">
                            <label class="form-label-custom">Address</label>
                            <textarea name="address" class="form-control-custom" rows="2"
                                placeholder="Kebele, Sub-city, Woreda..."><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary-custom btn-clean mt-0-5">
                        <span><i class="fas fa-save"></i> Save Changes</span>
                    </button>
                </form>
            </div>

            <!-- Change Password -->
            <div class="form-card max-w-none">
                <h2 class="section-heading-inline">
                    <i class="fas fa-lock text-primary-light"></i> Change Password
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
                    <div class="grid-2">
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
                    <button type="submit" class="btn-primary-custom btn-clean mt-0-5">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
