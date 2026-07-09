<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

// Handle product add/edit/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $products = readJSON(PRODUCTS_FILE);

    if ($action === 'add' || $action === 'edit') {
        // Handle image upload
        $image = $_POST['image'] ?? 'logo.jpg';
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
            $newName = 'product_' . time() . '.' . $ext;
            $dest = __DIR__ . '/../../' . $newName;
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $dest)) {
                $image = $newName;
            }
        }
        $product = [
            'id' => $action === 'add' ? (count($products) > 0 ? max(array_column($products, 'id')) + 1 : 1) : (int)$_POST['id'],
            'name' => $_POST['name'],
            'category' => $_POST['category'],
            'price' => (int)$_POST['price'],
            'old_price' => (int)$_POST['old_price'],
            'description' => $_POST['description'],
            'badge' => $_POST['badge'] ?? '',
            'image' => $image
        ];

        if ($action === 'add') {
            $products[] = $product;
        } else {
            foreach ($products as &$p) {
                if ($p['id'] == $product['id']) {
                    $p = $product;
                    break;
                }
            }
        }
        writeJSON(PRODUCTS_FILE, $products);
        $message = 'Product saved!';
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $products = array_values(array_filter($products, fn($p) => $p['id'] !== $id));
        writeJSON(PRODUCTS_FILE, $products);
        $message = 'Product deleted!';
    }
}

$products = readJSON(PRODUCTS_FILE);

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>WS Toys - Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
body { background: #faf8f5; }
.header { background: #1a1a1a; color: #fff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
.header h2 { font-size: 20px; }
.header h2 span { color: #2864B4; }
.header a { color: #fff; text-decoration: none; padding: 8px 20px; border-radius: 8px; background: #c0392b; font-size: 14px; }
.container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
.message { background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; }
.add-btn { display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #2864B4, #3a7bc8); color: #fff; border: none; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; transition: .3s; text-decoration: none; margin-bottom: 30px; }
.add-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(40,100,180,0.4); }
table { width: 100%; background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.08); }
th { background: #1a1a1a; color: #2864B4; padding: 15px; text-align: left; font-size: 14px; }
td { padding: 12px 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
tr:hover td { background: #faf8f5; }
.actions { display: flex; gap: 5px; }
.actions button, .actions a { padding: 6px 14px; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; text-decoration: none; color: #fff; }
.edit-btn { background: #2864B4; }
.del-btn { background: #c0392b; }
.view-btn { background: #1a1a1a; }
.empty { text-align: center; padding: 40px; color: #999; }
.product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; }
</style>
</head>
<body>
<div class="header">
    <h2>WS Toys <span>Admin Panel</span></h2>
    <div style="display:flex;gap:10px">
        <a href="generate.php" style="background:#2864B4;color:#fff;padding:8px 20px;border-radius:8px;font-size:14px;text-decoration:none"><i class="fas fa-globe"></i> Update Website</a>
        <a href="?logout=1" style="background:#c0392b;color:#fff;padding:8px 20px;border-radius:8px;font-size:14px;text-decoration:none"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>
<div class="container">
    <?php if (isset($message)): ?><div class="message"><?= $message ?></div><?php endif; ?>
    <a href="product-form.php" class="add-btn"><i class="fas fa-plus"></i> Add New Product</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Badge</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr><td colspan="7" class="empty">No products yet. Click "Add New Product" to start!</td></tr>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><img src="../../<?= $p['image'] ?: 'logo.jpg' ?>" class="product-img"></td>
                    <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                    <td><?= htmlspecialchars($p['category']) ?></td>
                    <td>Rs. <?= number_format($p['price']) ?></td>
                    <td><?= $p['badge'] ? '<span style="background:#2864B4;color:#fff;padding:3px 10px;border-radius:20px;font-size:12px">'.$p['badge'].'</span>' : '-' ?></td>
                    <td class="actions">
                        <a href="product-form.php?id=<?= $p['id'] ?>" class="edit-btn"><i class="fas fa-edit"></i></a>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Delete this product?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button type="submit" class="del-btn"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>

