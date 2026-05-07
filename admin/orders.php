<?php
$page_title = 'Orders';
require_once 'includes/admin_header.php';
require_once '../connection.php';

// Ensure $con is available (some connection files use different variable names)
if (!isset($con)) {
    if (isset($conn)) {
        $con = $conn;
    } elseif (isset($mysqli)) {
        $con = $mysqli;
    }
}

if (!isset($con)) {
    die('Database connection not found.');
}

// Status update
if (isset($_GET['set_status']) && isset($_GET['id'])) {
    $oid    = (int)$_GET['id'];
    $status = mysqli_real_escape_string($con, $_GET['set_status']);
    $allowed = ['pending','processing','delivered','cancelled'];
    if (in_array($status, $allowed)) {
        mysqli_query($con, "UPDATE orders SET status='$status' WHERE id=$oid");
    }
    header('Location: orders.php?success=1'); exit;
}

$orders = mysqli_query($con,
    "SELECT o.*, u.full_name, u.email
     FROM orders o JOIN users u ON o.user_id=u.id
     ORDER BY o.created_at DESC"
);
$total = mysqli_num_rows($orders);
$success = $_GET['success'] ?? '';
?>

<?php if ($success): ?><div class="alert-custom alert-success" style="margin-bottom:1.5rem"><i class="fas fa-check-circle"></i> Order status updated.</div><?php endif; ?>

<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Orders</h1>
        <p style="color:var(--text-muted);font-size:0.88rem;margin-top:4px"><?= $total ?> total orders</p>
    </div>
</div>

<div class="admin-table">
    <table>
        <thead>
            <tr>
                <th>#ID</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Amount</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Date</th>
                <th>Update Status</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($o = mysqli_fetch_assoc($orders)): ?>
        <tr>
            <td><strong style="color:var(--primary-light)">#<?= $o['id'] ?></strong></td>
            <td><?= htmlspecialchars($o['full_name']) ?></td>
            <td style="font-size:0.8rem;color:var(--text-muted)"><?= htmlspecialchars($o['email']) ?></td>
            <td>ETB <?= number_format($o['total_amount'], 2) ?></td>
            <td style="font-size:0.82rem"><?= htmlspecialchars($o['payment_method'] ?? '—') ?></td>
            <td><span class="order-status status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
            <td style="font-size:0.8rem;color:var(--text-muted)"><?= date('M d, Y H:i', strtotime($o['created_at'])) ?></td>
            <td>
                <select onchange="window.location='orders.php?id=<?= $o['id'] ?>&set_status='+this.value"
                        class="filter-select" style="font-size:0.78rem;padding:0.3rem 0.8rem;min-width:130px">
                    <?php foreach (['pending','processing','delivered','cancelled'] as $s): ?>
                    <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php if ($total === 0): ?>
        <tr><td colspan="8" style="text-align:center;padding:2.5rem;color:var(--text-muted)">No orders yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
