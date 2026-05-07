<?php
$page_title = 'Add Product';
require_once 'includes/admin_header.php';
require_once __DIR__ . '/../connection.php';

// Ensure $con is available (some connection files use different variable names)
if (!isset($con)) {
    if (isset($conn)) $con = $conn;
    elseif (isset($mysqli)) $con = $mysqli;
}

if (!isset($con) || !$con) {
    die('Database connection not available.');
}

$categories = mysqli_query($con, "SELECT * FROM categories ORDER BY name");
$error   = $_GET['error']   ?? '';
$success = $_GET['success'] ?? '';

// Restore old input on validation failure
$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
?>

<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Add New Product</h1>
        <p style="color:var(--text-muted);font-size:0.88rem;margin-top:4px">Fill in the details below to add a product to the catalogue.</p>
    </div>
    <a href="products.php" class="btn-outline-custom" style="font-size:0.88rem;padding:0.55rem 1.3rem">
        <i class="fas fa-arrow-left"></i> Back to Products
    </a>
</div>

<?php if ($error):   ?><div class="alert-custom alert-error"   style="margin-bottom:1.5rem"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert-custom alert-success" style="margin-bottom:1.5rem"><i class="fas fa-check-circle"></i> Product added successfully! <a href="products.php" style="color:var(--primary-light)">View all products</a>.</div><?php endif; ?>

<div class="admin-form-wrap">
    <form method="POST" action="actions/save_product.php">
        <input type="hidden" name="action" value="add">

        <div class="admin-form-grid">
            <!-- Name -->
            <div class="form-group-custom" style="grid-column:span 2">
                <label class="form-label-custom">Product Name *</label>
                <input type="text" name="name" class="form-control-custom" required
                       placeholder="e.g. Premium Yirgacheffe Coffee Beans"
                       value="<?= htmlspecialchars($old['name'] ?? '') ?>">
            </div>

            <!-- Category -->
            <div class="form-group-custom">
                <label class="form-label-custom">Category *</label>
                <select name="category_id" class="form-control-custom" required>
                    <option value="">— Select Category —</option>
                    <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                    <option value="<?= $c['id'] ?>" <?= ($old['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
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
                    <option value="New"  <?= ($old['badge'] ?? '') === 'New'  ? 'selected' : '' ?>>New</option>
                    <option value="Hot"  <?= ($old['badge'] ?? '') === 'Hot'  ? 'selected' : '' ?>>Hot</option>
                    <option value="Sale" <?= ($old['badge'] ?? '') === 'Sale' ? 'selected' : '' ?>>Sale</option>
                </select>
            </div>

            <!-- Price -->
            <div class="form-group-custom">
                <label class="form-label-custom">Price (ETB) *</label>
                <input type="number" name="price" step="0.01" min="0" class="form-control-custom" required
                       placeholder="450.00"
                       value="<?= htmlspecialchars($old['price'] ?? '') ?>">
            </div>

            <!-- Original Price -->
            <div class="form-group-custom">
                <label class="form-label-custom">Original Price (ETB) <span style="color:var(--text-muted);font-weight:400">(optional, for strikethrough)</span></label>
                <input type="number" name="original_price" step="0.01" min="0" class="form-control-custom"
                       placeholder="600.00"
                       value="<?= htmlspecialchars($old['original_price'] ?? '') ?>">
            </div>

            <!-- Stock -->
            <div class="form-group-custom">
                <label class="form-label-custom">Stock Quantity *</label>
                <input type="number" name="stock" min="0" class="form-control-custom" required
                       placeholder="50"
                       value="<?= htmlspecialchars($old['stock'] ?? '') ?>">
            </div>

            <!-- Image URL -->
            <div class="form-group-custom" style="grid-column:span 2">
                <label class="form-label-custom">Image URL *</label>
                <input type="url" name="image_url" class="form-control-custom" required
                       placeholder="https://images.unsplash.com/..."
                       value="<?= htmlspecialchars($old['image_url'] ?? '') ?>">
                <div class="field-msg" style="color:var(--text-muted)">Use any direct image link (Unsplash, Pexels, etc.)</div>
            </div>

            <!-- Description -->
            <div class="form-group-custom" style="grid-column:span 2">
                <label class="form-label-custom">Description *</label>
                <textarea name="description" class="form-control-custom" rows="4" required
                          placeholder="Write a compelling product description..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Image Preview -->
        <div id="img-preview-wrap" style="margin-bottom:1.5rem;display:none">
            <label class="form-label-custom">Image Preview</label>
            <img id="img-preview" src="" alt="Preview"
                 style="max-width:220px;max-height:180px;border-radius:var(--radius-sm);border:1px solid var(--glass-border);object-fit:cover">
        </div>

        <div style="display:flex;gap:1rem;flex-wrap:wrap">
            <button type="submit" class="btn-primary-custom">
                <i class="fas fa-plus-circle"></i> <span>Add Product</span>
            </button>
            <a href="products.php" class="btn-outline-custom">Cancel</a>
        </div>
    </form>
</div>

<script>
const imgInput   = document.querySelector('input[name="image_url"]');
const preview    = document.getElementById('img-preview');
const previewWrap = document.getElementById('img-preview-wrap');

function updatePreview() {
    const url = imgInput.value.trim();
    if (url.startsWith('http')) {
        preview.src = url;
        previewWrap.style.display = 'block';
        preview.onerror = () => { previewWrap.style.display = 'none'; };
        preview.onload  = () => { previewWrap.style.display = 'block'; };
    } else {
        previewWrap.style.display = 'none';
    }
}

imgInput.addEventListener('input', updatePreview);
<?php if (!empty($old['image_url'])): ?>
updatePreview();
<?php endif; ?>
</script>

<?php require_once 'includes/admin_footer.php'; ?>
