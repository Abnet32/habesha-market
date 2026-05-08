<?php
$page_title = 'Dashboard';
require_once 'includes/admin_header.php';
require_once __DIR__ . '/../connection.php';

// Ensure $con is available (some connection files may use different variable names)
if (!isset($con)) {
    if (isset($conn)) {
        $con = $conn;
    } elseif (isset($connection)) {
        $con = $connection;
    }
}

// Stats
$stats = [];
foreach ([
    'products' => 'SELECT COUNT(*) FROM products WHERE is_active=1',
    'orders'   => 'SELECT COUNT(*) FROM orders',
    'users'    => 'SELECT COUNT(*) FROM users WHERE role="customer"',
    'revenue'  => 'SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status!="cancelled"',
] as $key => $sql) {
    $r = mysqli_query($con, $sql);
    $stats[$key] = mysqli_fetch_row($r)[0] ?? 0;
}

// Recent orders
$recent_orders = mysqli_query($con,
    "SELECT o.id, o.total_amount, o.status, o.created_at, u.full_name
     FROM orders o JOIN users u ON o.user_id=u.id
     ORDER BY o.created_at DESC LIMIT 8"
);

// Low stock
$low_stock = mysqli_query($con,
    "SELECT id, name, stock FROM products WHERE is_active=1 AND stock<=5 ORDER BY stock ASC LIMIT 6"
);
?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Dashboard Overview</h1>
    <a href="add_product.php" class="btn-primary-custom">
        <i class="fas fa-plus"></i> <span>Add Product</span>
    </a>
</div>

<!-- STAT CARDS -->
<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-card-icon blue"><i class="fas fa-box-open admin-stats-icon-blue"></i></div>
        <div>
            <div class="stat-card-value"><?= number_format($stats['products']) ?></div>
            <div class="stat-card-label">Active Products</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon cyan"><i class="fas fa-receipt admin-stats-icon-cyan"></i></div>
        <div>
            <div class="stat-card-value"><?= number_format($stats['orders']) ?></div>
            <div class="stat-card-label">Total Orders</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon purple"><i class="fas fa-users admin-stats-icon-purple"></i></div>
        <div>
            <div class="stat-card-value"><?= number_format($stats['users']) ?></div>
            <div class="stat-card-label">Customers</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon red"><i class="fas fa-coins admin-stats-icon-red"></i></div>
        <div>
            <div class="stat-card-value">ETB <?= number_format($stats['revenue']) ?></div>
            <div class="stat-card-label">Total Revenue</div>
        </div>
    </div>
</div>

<div class="admin-grid-two">

    <!-- RECENT ORDERS -->
    <div>
        <h2 class="admin-heading">Recent Orders</h2>
        <div class="admin-table">
            <table>
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($o = mysqli_fetch_assoc($recent_orders)): ?>
                    <tr>
                        <td><strong class="admin-link-inline font-semibold">#<?= $o['id'] ?></strong></td>
                        <td><?= htmlspecialchars($o['full_name']) ?></td>
                        <td>ETB <?= number_format($o['total_amount'], 2) ?></td>
                        <td>
                            <span class="order-status status-<?= $o['status'] ?>">
                                <?= ucfirst($o['status']) ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div class="admin-divider">
            <a href="orders.php" class="btn-outline-custom admin-filter-compact">
                View All Orders <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- LOW STOCK -->
    <div>
        <h2 class="admin-heading">
            <i class="fas fa-exclamation-triangle text-secondary mr-6"></i>Low Stock Alert
        </h2>
        <div class="admin-low-stock-list">
            <?php
            $has_low = false;
            while ($p = mysqli_fetch_assoc($low_stock)):
                $has_low = true;
            ?>
            <div class="admin-low-stock-card <?= $p['stock']==0 ? 'admin-low-stock-card-out stock-out' : 'admin-low-stock-card-warning stock-low' ?> flex-between-wrap">
                <span class="text-88 font-medium"><?= htmlspecialchars($p['name']) ?></span>
                <span class="text-80 admin-stock-state <?= $p['stock']==0 ? 'stock-out' : 'stock-low' ?>">
                    <?= $p['stock'] === '0' ? 'Out of stock' : $p['stock'] . ' left' ?>
                </span>
            </div>
            <?php endwhile; ?>
            <?php if (!$has_low): ?>
            <div class="admin-low-stock-card admin-low-stock-card-ok">
                <i class="fas fa-check-circle stock-good mr-6"></i>All products are well stocked
            </div>
            <?php endif; ?>
        </div>

        <div class="mt-1-5">
            <a href="products.php" class="btn-outline-custom admin-filter-compact w-full btn-inline-flex">
                <i class="fas fa-box-open"></i> Manage Products
            </a>
        </div>
    </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
