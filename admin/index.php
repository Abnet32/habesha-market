<?php
$page_title = 'Dashboard';
require_once 'includes/admin_header.php';
require_once '../connection.php';

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
        <div class="stat-card-icon blue"><i class="fas fa-box-open" style="color:#38bdf8"></i></div>
        <div>
            <div class="stat-card-value"><?= number_format($stats['products']) ?></div>
            <div class="stat-card-label">Active Products</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon cyan"><i class="fas fa-receipt" style="color:#67e8f9"></i></div>
        <div>
            <div class="stat-card-value"><?= number_format($stats['orders']) ?></div>
            <div class="stat-card-label">Total Orders</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon purple"><i class="fas fa-users" style="color:#c4b5fd"></i></div>
        <div>
            <div class="stat-card-value"><?= number_format($stats['users']) ?></div>
            <div class="stat-card-label">Customers</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon red"><i class="fas fa-coins" style="color:#fda4af"></i></div>
        <div>
            <div class="stat-card-value">ETB <?= number_format($stats['revenue']) ?></div>
            <div class="stat-card-label">Total Revenue</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start">

    <!-- RECENT ORDERS -->
    <div>
        <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem">Recent Orders</h2>
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
                        <td><strong style="color:var(--primary-light)">#<?= $o['id'] ?></strong></td>
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
        <div style="margin-top:1rem">
            <a href="orders.php" class="btn-outline-custom" style="font-size:0.85rem;padding:0.5rem 1.2rem">
                View All Orders <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- LOW STOCK -->
    <div>
        <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem">
            <i class="fas fa-exclamation-triangle" style="color:#fbbf24;margin-right:6px"></i>Low Stock Alert
        </h2>
        <div style="display:flex;flex-direction:column;gap:0.6rem">
            <?php
            $has_low = false;
            while ($p = mysqli_fetch_assoc($low_stock)):
                $has_low = true;
            ?>
            <div style="background:var(--glass);border:1px solid <?= $p['stock']==0 ? 'rgba(225,29,72,0.4)' : 'rgba(251,191,36,0.3)' ?>;border-radius:var(--radius-sm);padding:0.8rem 1rem;display:flex;justify-content:space-between;align-items:center">
                <span style="font-size:0.88rem;font-weight:600"><?= htmlspecialchars($p['name']) ?></span>
                <span style="font-size:0.8rem;font-weight:700;color:<?= $p['stock']==0 ? '#fb7185' : '#fbbf24' ?>">
                    <?= $p['stock'] === '0' ? 'Out of stock' : $p['stock'] . ' left' ?>
                </span>
            </div>
            <?php endwhile; ?>
            <?php if (!$has_low): ?>
            <div style="background:var(--glass);border:1px solid var(--glass-border);border-radius:var(--radius-sm);padding:1rem;text-align:center;color:var(--text-muted);font-size:0.88rem">
                <i class="fas fa-check-circle" style="color:#6ee7b7;margin-right:6px"></i>All products are well stocked
            </div>
            <?php endif; ?>
        </div>

        <div style="margin-top:1.5rem">
            <a href="products.php" class="btn-outline-custom" style="font-size:0.85rem;padding:0.5rem 1.2rem;width:100%;justify-content:center">
                <i class="fas fa-box-open"></i> Manage Products
            </a>
        </div>
    </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
