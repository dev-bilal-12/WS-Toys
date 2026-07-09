<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$product = [
    'id' => '',
    'name' => '',
    'category' => 'boys',
    'price' => '',
    'old_price' => '',
    'description' => '',
    'badge' => '',
    'image' => 'logo.jpg'
];
$is_edit = false;

if (isset($_GET['id'])) {
    $products = readJSON(PRODUCTS_FILE);
    foreach ($products as $p) {
        if ($p['id'] == $_GET['id']) {
            $product = $p;
            $is_edit = true;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $is_edit ? 'Edit' : 'Add' ?> Product - WS Toys Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
body { background: #faf8f5; }
.header { background: #1a1a1a; color: #fff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
.header h2 span { color: #2864B4; }
.header a { color: #fff; text-decoration: none; }
.container { max-width: 700px; margin: 50px auto; padding: 0 20px; }
.form-card { background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); }
.form-card h1 { font-size: 24px; margin-bottom: 5px; }
.form-card .sub { color: #888; font-size: 14px; margin-bottom: 30px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; font-weight: 600; margin-bottom: 6px; font-size: 14px; color: #333; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px 16px; border: 2px solid #eee; border-radius: 10px; font-size: 15px; outline: none; transition: .3s; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #2864B4; }
.form-group textarea { height: 100px; resize: vertical; }
.row { display: flex; gap: 20px; }
.row .form-group { flex: 1; }
.actions { display: flex; gap: 15px; margin-top: 30px; }
.btn-primary { padding: 14px 40px; background: linear-gradient(135deg, #2864B4, #3a7bc8); color: #fff; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: .3s; }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(40,100,180,0.4); }
.btn-secondary { padding: 14px 40px; background: #1a1a1a; color: #fff; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none; text-align: center; transition: .3s; }
.btn-secondary:hover { transform: translateY(-2px); }
</style>
</head>
<body>
<div class="header">
    <h2>WS Toys <span>Admin Panel</span></h2>
    <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="container">
    <div class="form-card">
        <h1><?= $is_edit ? 'Edit' : 'Add New' ?> Product</h1>
        <p class="sub">Fill in the details below to <?= $is_edit ? 'update' : 'add' ?> a product</p>
        <form method="POST" action="dashboard.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?= $is_edit ? 'edit' : 'add' ?>">
            <input type="hidden" name="id" value="<?= $product['id'] ?>">

            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>

            <div class="row">
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category">
                        <option value="boys" <?= $product['category']==='boys'?'selected':'' ?>>Boys</option>
                        <option value="girls" <?= $product['category']==='girls'?'selected':'' ?>>Girls</option>
                        <option value="babies" <?= $product['category']==='babies'?'selected':'' ?>>Babies</option>
                        <option value="action" <?= $product['category']==='action'?'selected':'' ?>>Action Figures</option>
                        <option value="educational" <?= $product['category']==='educational'?'selected':'' ?>>Educational</option>
                        <option value="remote" <?= $product['category']==='remote'?'selected':'' ?>>Remote Control</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Badge (e.g. Sale, New)</label>
                    <input type="text" name="badge" value="<?= htmlspecialchars($product['badge']) ?>" placeholder="e.g. Sale!">
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <label>Selling Price (Rs.) *</label>
                    <input type="number" name="price" value="<?= $product['price'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Old Price (Rs.)</label>
                    <input type="number" name="old_price" value="<?= $product['old_price'] ?>" placeholder="0 = no discount">
                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Brief product description..."><?= htmlspecialchars($product['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="product_image" accept="image/*" style="padding:10px;border:2px dashed #ddd;border-radius:10px;width:100%;background:#faf8f5;">
                <input type="hidden" name="image" value="<?= htmlspecialchars($product['image']) ?>">
                <?php if ($product['image'] && $product['image'] !== 'logo.jpg'): ?>
                <div style="margin-top:8px"><img src="../../<?= $product['image'] ?>" style="height:60px;border-radius:8px;"></div>
                <?php endif; ?>
                <small style="color:#888;display:block;margin-top:5px">Upload new image or leave empty to keep current</small>
            </div>

            <div class="actions">
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> <?= $is_edit ? 'Update' : 'Save' ?> Product</button>
                <a href="dashboard.php" class="btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>

