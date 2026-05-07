<?php
$page_title = 'Users';
require_once '../connection.php';
require_once 'includes/admin_header.php';

// Ensure $con is available. Some connection files may use $conn instead.
if (!isset($con)) {
    if (isset($conn)) {
        $con = $conn;
    } else {
        die('Database connection not found.');
    }
}

$users = mysqli_query($con,
    "SELECT u.*, COUNT(o.id) AS order_count, COALESCE(SUM(o.total_amount),0) AS total_spent
     FROM users u
     LEFT JOIN orders o ON o.user_id=u.id
     GROUP BY u.id ORDER BY u.created_at DESC"
);
$total = mysqli_num_rows($users);
?>

<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Users</h1>
        <p style="color:var(--text-muted);font-size:0.88rem;margin-top:4px"><?= $total ?> registered accounts</p>
    </div>
</div>

<div class="admin-table">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Orders</th>
                <th>Total Spent</th>
                <th>Registered</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($u = mysqli_fetch_assoc($users)): ?>
        <tr>
            <td style="color:var(--text-muted)"><?= $u['id'] ?></td>
            <td>
                <div style="display:flex;align-items:center;gap:10px">
                    <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--primary-dark),var(--secondary));display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;color:white;flex-shrink:0">
                        <?= mb_strtoupper(mb_substr($u['full_name'], 0, 1)) ?>
                    </div>
                    <strong><?= htmlspecialchars($u['full_name']) ?></strong>
                </div>
            </td>
            <td style="font-size:0.85rem;color:var(--text-muted)"><?= htmlspecialchars($u['email']) ?></td>
            <td style="font-size:0.85rem"><?= htmlspecialchars($u['phone'] ?? '—') ?></td>
            <td>
                <?php if ($u['role'] === 'admin'): ?>
                    <span class="tbl-badge new">Admin</span>
                <?php else: ?>
                    <span class="tbl-badge none">Customer</span>
                <?php endif; ?>
            </td>
            <td style="text-align:center"><?= (int)$u['order_count'] ?></td>
            <td>ETB <?= number_format($u['total_spent'], 2) ?></td>
            <td style="font-size:0.8rem;color:var(--text-muted)"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
        </tr>
        <?php endwhile; ?>
        <?php if ($total === 0): ?>
        <tr><td colspan="8" style="text-align:center;padding:2.5rem;color:var(--text-muted)">No users found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
