<?php
$page_title = 'Edit Product';
require_once 'includes/admin_header.php';
require_once __DIR__ . '/../connection.php';

// Ensure $con (mysqli connection) is available
if (!isset($con) || !$con) {
    header('Location: products.php?error=Database+connection+failed');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: products.php'); exit; }

$res = mysqli_query($con, "SELECT * FROM products WHERE id=$id AND is_active=1 LIMIT 1");
if (!$res || mysqli_num_rows($res) === 0) { header('Location: products.php?error=Product+not+found'); exit; }
$p = mysqli_fetch_assoc($res);

$categories = mysqli_query($con, "SELECT * FROM categories ORDER BY name");
$error   = $_GET['error']   ?? '';
?>

<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Edit Product</h1>
        <p class="admin-page-note">
            Editing: <strong class="admin-link-inline"><?= htmlspecialchars($p['name']) ?></strong>
        </p>
    </div>
    <a href="products.php" class="btn-outline-custom admin-form-tight-btn">
        <i class="fas fa-arrow-left"></i> Back to Products
    </a>
</div>

<?php if ($error): ?><div class="alert-custom alert-error admin-alert-spacing"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="admin-form-wrap">
    <form method="POST" action="actions/save_product.php">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id"     value="<?= $p['id'] ?>">

        <div class="admin-form-grid">
            <!-- Name -->
            <div class="form-group-custom admin-form-fullspan">
                <label class="form-label-custom">Product Name *</label>
                <input type="text" name="name" class="form-control-custom" required
                       value="<?= htmlspecialchars($p['name']) ?>">
            </div>

            <!-- Category -->
            <div class="form-group-custom">
                <label class="form-label-custom">Category *</label>
                <select name="category_id" class="form-control-custom" required>
                    <option value="">— Select Category —</option>
                    <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                    <option value="<?= $c['id'] ?>" <?= (int)$p['category_id'] === (int)$c['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Badge -->
            <div class="form-group-custom">
                <label class="form-label-custom">Badge</label>
                <select name="badge" class="form-control-custom">
                    <option value="">None</option>
                    <?php foreach (['New', 'Hot', 'Sale'] as $b): ?>
                    <option value="<?= $b ?>" <?= strtolower($p['badge'] ?? '') === strtolower($b) ? 'selected' : '' ?>>
                        <?= $b ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Price -->
            <div class="form-group-custom">
                <label class="form-label-custom">Price (ETB) *</label>
                <input type="number" name="price" step="0.01" min="0" class="form-control-custom" required
                       value="<?= htmlspecialchars($p['price']) ?>">
            </div>

            <!-- Original Price -->
            <div class="form-group-custom">
                <label class="form-label-custom">Original Price (ETB)</label>
                <input type="number" name="original_price" step="0.01" min="0" class="form-control-custom"
                       value="<?= htmlspecialchars($p['original_price'] ?? '') ?>">
            </div>

            <!-- Stock -->
            <div class="form-group-custom">
                <label class="form-label-custom">Stock Quantity *</label>
                <input type="number" name="stock" min="0" class="form-control-custom" required
                       value="<?= htmlspecialchars($p['stock']) ?>">
            </div>

            <!-- Image URL -->
            <div class="form-group-custom admin-form-fullspan">
                <label class="form-label-custom">Image URL *</label>
                <input type="url" name="image_url" class="form-control-custom" required
                       id="image_url_input"
                       value="<?= htmlspecialchars($p['image_url'] ?? '') ?>">
            </div>

            <!-- Image Preview -->
            <div class="admin-preview-wrap" id="img-preview-wrap">
                <label class="form-label-custom">Current Image Preview</label>
                <img id="img-preview" src="<?= htmlspecialchars($p['image_url'] ?? '') ?>" alt="Preview"
                     class="admin-preview-img"
                     onerror="this.hidden=true">
            </div>

            <!-- Description -->
            <div class="form-group-custom admin-form-fullspan">
                <label class="form-label-custom">Description *</label>
                <textarea name="description" class="form-control-custom" rows="4" required><?= htmlspecialchars($p['description'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="admin-btn-row">
            <button type="submit" class="btn-primary-custom">
                <i class="fas fa-save"></i> <span>Save Changes</span>
            </button>
            <a href="products.php" class="btn-outline-custom">Cancel</a>
            <a href="actions/delete_product.php?id=<?= $p['id'] ?>"
               class="btn-outline-custom btn-danger-outline"
               onclick="return confirm('Delete this product permanently?')">
                <i class="fas fa-trash"></i> Delete
            </a>
        </div>
    </form>
</div>

<script>
const imgInput = document.getElementById('image_url_input');
const preview  = document.getElementById('img-preview');
imgInput.addEventListener('input', function () {
    const url = this.value.trim();
    if (url.startsWith('http')) {
        preview.src   = url;
        preview.hidden = false;
    } else {
        preview.hidden = true;
    }
});
</script>

<?php require_once 'includes/admin_footer.php'; ?>
