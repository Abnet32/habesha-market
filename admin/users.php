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
        <p class="admin-page-note"><?= $total ?> registered accounts</p>
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
            <td class="table-cell-muted"><?= $u['id'] ?></td>
            <td>
                <div class="admin-avatar-row">
                    <div class="admin-avatar-34">
                        <?= mb_strtoupper(mb_substr($u['full_name'], 0, 1)) ?>
                    </div>
                    <strong><?= htmlspecialchars($u['full_name']) ?></strong>
                </div>
            </td>
            <td class="text-85 table-cell-muted"><?= htmlspecialchars($u['email']) ?></td>
            <td class="text-85"><?= htmlspecialchars($u['phone'] ?? '—') ?></td>
            <td>
                <?php if ($u['role'] === 'admin'): ?>
                    <span class="tbl-badge new">Admin</span>
                <?php else: ?>
                    <span class="tbl-badge none">Customer</span>
                <?php endif; ?>
            </td>
            <td class="table-cell-center"><?= (int)$u['order_count'] ?></td>
            <td>ETB <?= number_format($u['total_spent'], 2) ?></td>
            <td class="text-80 table-cell-muted"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
        </tr>
        <?php endwhile; ?>
        <?php if ($total === 0): ?>
        <tr><td colspan="8" class="table-empty-cell">No users found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
