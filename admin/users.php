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

// Pagination setup
$per_page = 10;
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($current_page - 1) * $per_page;

// Get total count for pagination
$count_result = mysqli_query($con,
    "SELECT COUNT(*) as total FROM users u"
);
$count_row = mysqli_fetch_assoc($count_result);
$total = $count_row['total'];
$total_pages = ceil($total / $per_page);

// Get users for current page
$users = mysqli_query($con,
    "SELECT u.*, COUNT(o.id) AS order_count, COALESCE(SUM(o.total_amount),0) AS total_spent
     FROM users u
     LEFT JOIN orders o ON o.user_id=u.id
     GROUP BY u.id ORDER BY u.created_at DESC
     LIMIT $per_page OFFSET $offset"
);
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

<!-- PAGINATION -->
<?php if ($total_pages > 1): ?>
<div class="pagination-container">
    <div class="pagination-info">
        Showing <?= ($offset + 1) ?> to <?= min($offset + $per_page, $total) ?> of <?= $total ?> users
    </div>
    <div class="pagination-nav">
        <?php if ($current_page > 1): ?>
            <a href="users.php?page=<?= $current_page - 1 ?>" class="btn-pagination">
                <i class="fas fa-chevron-left"></i> Previous
            </a>
        <?php endif; ?>

        <div class="pagination-pages">
            <?php
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);

            if ($start_page > 1):
                echo '<a href="users.php?page=1" class="btn-page">1</a>';
                if ($start_page > 2):
                    echo '<span class="pagination-dots">...</span>';
                endif;
            endif;

            for ($p = $start_page; $p <= $end_page; $p++):
                if ($p === $current_page):
                    echo '<span class="btn-page active">' . $p . '</span>';
                else:
                    echo '<a href="users.php?page=' . $p . '" class="btn-page">' . $p . '</a>';
                endif;
            endfor;

            if ($end_page < $total_pages):
                if ($end_page < $total_pages - 1):
                    echo '<span class="pagination-dots">...</span>';
                endif;
                echo '<a href="users.php?page=' . $total_pages . '" class="btn-page">' . $total_pages . '</a>';
            endif;
            ?>
        </div>

        <?php if ($current_page < $total_pages): ?>
            <a href="users.php?page=<?= $current_page + 1 ?>" class="btn-pagination">
                Next <i class="fas fa-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/admin_footer.php'; ?>
