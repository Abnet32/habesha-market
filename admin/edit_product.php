<?php
$page_title = 'Edit Product';
require_once 'includes/admin_header.php';
require_once '../connection.php';

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
        <p style="color:var(--text-muted);font-size:0.88rem;margin-top:4px">
            Editing: <strong style="color:var(--primary-light)"><?= htmlspecialchars($p['name']) ?></strong>
        </p>
    </div>
    <a href="products.php" class="btn-outline-custom" style="font-size:0.88rem;padding:0.55rem 1.3rem">
        <i class="fas fa-arrow-left"></i> Back to Products
    </a>
</div>

<?php if ($error): ?><div class="alert-custom alert-error" style="margin-bottom:1.5rem"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="admin-form-wrap">
    <form method="POST" action="actions/save_product.php">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id"     value="<?= $p['id'] ?>">

        <div class="admin-form-grid">
            <!-- Name -->
            <div class="form-group-custom" style="grid-column:span 2">
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
            <div class="form-group-custom" style="grid-column:span 2">
                <label class="form-label-custom">Image URL *</label>
                <input type="url" name="image_url" class="form-control-custom" required
                       id="image_url_input"
                       value="<?= htmlspecialchars($p['image_url'] ?? '') ?>">
            </div>

            <!-- Image Preview -->
            <div style="grid-column:span 2;margin-bottom:0.5rem" id="img-preview-wrap">
                <label class="form-label-custom">Current Image Preview</label>
                <img id="img-preview" src="<?= htmlspecialchars($p['image_url'] ?? '') ?>" alt="Preview"
                     style="max-width:220px;max-height:180px;border-radius:var(--radius-sm);border:1px solid var(--glass-border);object-fit:cover"
                     onerror="this.style.display='none'">
            </div>

            <!-- Description -->
            <div class="form-group-custom" style="grid-column:span 2">
                <label class="form-label-custom">Description *</label>
                <textarea name="description" class="form-control-custom" rows="4" required><?= htmlspecialchars($p['description'] ?? '') ?></textarea>
            </div>
        </div>

        <div style="display:flex;gap:1rem;flex-wrap:wrap">
            <button type="submit" class="btn-primary-custom">
                <i class="fas fa-save"></i> <span>Save Changes</span>
            </button>
            <a href="products.php" class="btn-outline-custom">Cancel</a>
            <a href="actions/delete_product.php?id=<?= $p['id'] ?>"
               class="btn-outline-custom" style="border-color:rgba(225,29,72,0.4);color:#fb7185"
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
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
});
</script>

<?php require_once 'includes/admin_footer.php'; ?>
