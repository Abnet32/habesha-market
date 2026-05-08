<?php
$page_title = 'Products';
require_once 'includes/admin_header.php';
require_once '../connection.php';

if (!isset($con) || !$con) {
    die('Database connection failed');
}

$success = $_GET['success'] ?? '';
$error   = $_GET['error']   ?? '';

// Build filter query
$where = 'WHERE p.is_active=1';
$cat_filter = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
if ($cat_filter > 0) $where .= " AND p.category_id=$cat_filter";

$products = mysqli_query($con,
    "SELECT p.*, c.name AS cat_name
     FROM products p LEFT JOIN categories c ON p.category_id=c.id
     $where ORDER BY p.id DESC"
);
$categories = mysqli_query($con, "SELECT * FROM categories ORDER BY name");
$total = mysqli_num_rows($products);
?>

<?php if ($success === 'added'):   ?><div class="alert-custom alert-success admin-alert-spacing"><i class="fas fa-check-circle"></i> Product added successfully.</div><?php endif; ?>
<?php if ($success === 'updated'): ?><div class="alert-custom alert-success admin-alert-spacing"><i class="fas fa-check-circle"></i> Product updated successfully.</div><?php endif; ?>
<?php if ($success === 'deleted'): ?><div class="alert-custom alert-success admin-alert-spacing"><i class="fas fa-check-circle"></i> Product deleted.</div><?php endif; ?>
<?php if ($error):                 ?><div class="alert-custom alert-error admin-alert-spacing"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Products</h1>
        <p class="admin-page-note"><?= $total ?> product<?= $total !== 1 ? 's' : '' ?> found</p>
    </div>
    <a href="add_product.php" class="btn-primary-custom">
        <i class="fas fa-plus"></i> <span>Add Product</span>
    </a>
</div>

<!-- FILTER BAR -->
<div class="search-section admin-search-bar">
    <form method="GET" class="admin-search-form">
        <select name="cat" class="filter-select" onchange="this.form.submit()">
            <option value="0">All Categories</option>
            <?php
            mysqli_data_seek($categories, 0);
            while ($c = mysqli_fetch_assoc($categories)):
            ?>
            <option value="<?= $c['id'] ?>" <?= $cat_filter === (int)$c['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?>
            </option>
            <?php endwhile; ?>
        </select>
        <?php if ($cat_filter > 0): ?>
            <a href="products.php" class="btn-outline-custom admin-filter-compact">
                <i class="fas fa-times"></i> Clear
            </a>
        <?php endif; ?>
    </form>
</div>

<!-- TABLE -->
<div class="admin-table">
    <table>
        <thead>
            <tr>
                <th width="50">#</th>
                <th width="56">Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price (ETB)</th>
                <th>Stock</th>
                <th>Badge</th>
                <th>Rating</th>
                <th width="130">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php mysqli_data_seek($products, 0); while ($p = mysqli_fetch_assoc($products)): ?>
        <tr>
            <td class="table-cell-muted"><?= $p['id'] ?></td>
            <td>
                <img src="<?= htmlspecialchars($p['image_url'] ?? '') ?>"
                     alt="<?= htmlspecialchars($p['name']) ?>"
                     onerror="this.src='data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'48\' height=\'48\' viewBox=\'0 0 48 48\'><rect fill=\'%230b1829\' width=\'48\' height=\'48\'/><text y=\'32\' x=\'12\' font-size=\'24\'>📦</text></svg>'">
            </td>
            <td>
                <strong><?= htmlspecialchars($p['name']) ?></strong>
                <div class="text-75 table-cell-muted mt-0-5">
                    <?= htmlspecialchars(mb_substr($p['description'] ?? '', 0, 55)) ?>…
                </div>
            </td>
            <td class="text-85 text-primary-light"><?= htmlspecialchars($p['cat_name'] ?? '—') ?></td>
            <td>
                <strong><?= number_format($p['price'], 2) ?></strong>
                <?php if ($p['original_price'] && $p['original_price'] > $p['price']): ?>
                    <div class="text-75 table-cell-muted"> <?= number_format($p['original_price'], 2) ?></div>
                <?php endif; ?>
            </td>
            <td>
                <span class="admin-stock-row <?= (int)$p['stock'] === 0 ? 'stock-out' : ((int)$p['stock'] <= 5 ? 'stock-low' : 'stock-good') ?>">
                    <?= (int)$p['stock'] ?>
                </span>
            </td>
            <td>
                <?php $b = strtolower($p['badge'] ?? ''); ?>
                <span class="tbl-badge <?= $b ?: 'none' ?>"><?= $b ?: 'none' ?></span>
            </td>
            <td>
                <span class="text-secondary">★</span>
                <?= number_format((float)($p['rating'] ?? 0), 1) ?>
                <span class="text-75 table-cell-muted">(<?= (int)($p['review_count'] ?? 0) ?>)</span>
            </td>
            <td>
                <div class="action-btns">
                    <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn-sm edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="actions/delete_product.php?id=<?= $p['id'] ?>"
                       class="btn-sm delete"
                       onclick="return confirm('Delete \'<?= addslashes($p['name']) ?>\'? This cannot be undone.')">
                        <i class="fas fa-trash"> </i> Delete
                    </a>
                </div>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php if ($total === 0): ?>
        <tr>
            <td colspan="9" class="table-empty-cell">
                <i class="fas fa-box-open text-88 block opacity-50"></i>
                No products found.
            </td>
        </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
