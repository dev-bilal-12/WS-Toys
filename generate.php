<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$products = readJSON(PRODUCTS_FILE);

function renderProductCard($p) {
    $badge = $p['badge'] ? "<div class=\"badge\">{$p['badge']}</div>" : '';
    $oldPrice = $p['old_price'] ? "<span>Rs." . number_format($p['old_price']) . "</span>" : '';
    $img = $p['image'] ?: 'logo.jpg';
    return <<<HTML
<div class="product-card" data-id="{$p['id']}" data-price="{$p['price']}" data-category="{$p['category']}">
    <div class="img-box">
        $badge
        <img src="$img" alt="{$p['name']}">
    </div>
    <h3>{$p['name']}</h3>
    <p>{$p['description']}</p>
    <div class="price">
        $oldPrice
        <strong>Rs.{$p['price']}</strong>
    </div>
    <button>Add To Cart</button>
</div>
HTML;
}

function replaceBetween(&$content, $startMarker, $endMarker, $replacement) {
    $startPos = strpos($content, $startMarker);
    if ($startPos === false) return false;
    $endPos = strpos($content, $endMarker, $startPos + strlen($startMarker));
    if ($endPos === false) return false;
    $before = substr($content, 0, $startPos + strlen($startMarker));
    $after = substr($content, $endPos);
    $content = $before . "\n" . $replacement . "\n" . $after;
    return true;
}

$message = '';
$type = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_sample') {
        $sampleProducts = [
            ['id' => 1, 'name' => 'Labubu Keychain', 'category' => 'girls', 'price' => 1995, 'old_price' => 4895, 'description' => 'Blind Box (Random Character)', 'badge' => 'Sale!', 'image' => 'logo.jpg'],
            ['id' => 2, 'name' => 'Premium Action Toy', 'category' => 'boys', 'price' => 5316, 'old_price' => 12000, 'description' => 'High-quality durable toy.', 'badge' => '', 'image' => 'logo.jpg'],
            ['id' => 3, 'name' => 'Minar-E-Pakistan Souvenir', 'category' => 'boys', 'price' => 2450, 'old_price' => 3850, 'description' => 'Detailed 9.5" Monument Model', 'badge' => 'Best Seller', 'image' => 'logo.jpg'],
            ['id' => 4, 'name' => 'RC Drift Racer 1:16', 'category' => 'remote', 'price' => 8500, 'old_price' => 12000, 'description' => '4WD Remote Control Car with LED Lights', 'badge' => 'Hot', 'image' => 'logo.jpg'],
        ];
        writeJSON(PRODUCTS_FILE, $sampleProducts);
        $products = $sampleProducts;
        $message = 'Sample products added to JSON! Now click "Update Website Pages".';
    }

    if ($action === 'generate') {
        // Generate product cards HTML
        $allCards = '';
        foreach ($products as $p) {
            $allCards .= renderProductCard($p) . "\n";
        }

        // Only generate for collections and best-sellers (keep index.html simple)
        $files = [
            __DIR__ . '/../../collections.html',
            __DIR__ . '/../../best-sellers.html',
        ];

        $generated = 0;
        foreach ($files as $file) {
            if (!file_exists($file)) continue;
            $content = file_get_contents($file);
            
            // Find the products-grid section and replace contents
            $pattern = '/(<div class="products-grid"[^>]*>)(.*?)(<\/div>\s*<\/section>)/s';
            $replacement = '$1' . "\n" . $allCards . "\n" . '$3';
            $newContent = preg_replace($pattern, $replacement, $content, 1, $count);
            
            if ($count > 0) {
                file_put_contents($file, $newContent);
                $generated++;
            }
        }

        $message = "Updated $generated page(s) with " . count($products) . " products!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Generate Website - WS Toys Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
body { background: #faf8f5; }
.header { background: #1a1a1a; color: #fff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
.header h2 span { color: #2864B4; }
.header a { color: #fff; text-decoration: none; padding: 8px 20px; border-radius: 8px; background: #2864B4; font-size: 14px; }
.container { max-width: 800px; margin: 50px auto; padding: 0 20px; }
.card { background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); margin-bottom: 30px; }
.card h1 { font-size: 24px; margin-bottom: 10px; }
.card p { color: #888; margin-bottom: 25px; }
.message { padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; }
.success { background: #d4edda; color: #155724; }
.info { background: #cce5ff; color: #004085; }
.btn { display: inline-block; padding: 14px 35px; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: .3s; text-decoration: none; color: #fff; margin-right: 15px; margin-bottom: 10px; }
.btn-gold { background: linear-gradient(135deg, #2864B4, #3a7bc8); }
.btn-gold:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(40,100,180,0.4); }
.btn-dark { background: #1a1a1a; }
.btn-dark:hover { transform: translateY(-2px); }
.steps { list-style: none; padding: 0; }
.steps li { padding: 12px 0; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; gap: 15px; }
.steps li .num { width: 30px; height: 30px; background: #2864B4; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; flex-shrink: 0; }
.product-count { font-size: 14px; color: #666; margin-top: 10px; }
</style>
</head>
<body>
<div class="header">
    <h2>WS Toys <span>Generate Website</span></h2>
    <div>
        <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Dashboard</a>
    </div>
</div>
<div class="container">
    <?php if ($message): ?>
    <div class="message <?= $type ?>"><?= $message ?></div>
    <?php endif; ?>

    <div class="card">
        <h1>How to Add Products</h1>
        <ol class="steps">
            <li><span class="num">1</span> Add your product images to the <code>ws-toys/</code> folder</li>
            <li><span class="num">2</span> Go to <strong>Dashboard</strong> → click "Add New Product"</li>
            <li><span class="num">3</span> Enter product details (name, price, category, image filename)</li>
            <li><span class="num">4</span> Come back here and click <strong>"Update Website Pages"</strong></li>
            <li><span class="num">5</span> Open your website — products will be updated automatically!</li>
        </ol>
    </div>

    <div class="card">
        <h1>Products in JSON</h1>
        <p>Currently <strong><?= count($products) ?></strong> product(s) saved.</p>
        <div class="product-count">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $p): ?>
                    <div>• <?= htmlspecialchars($p['name']) ?> — Rs.<?= $p['price'] ?></div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="color:#c0392b">No products yet! Add some first.</div>
            <?php endif; ?>
        </div>

        <form method="POST" style="margin-top: 25px;">
            <button type="submit" name="action" value="generate" class="btn btn-gold" <?= empty($products) ? 'disabled style="opacity:0.5"' : '' ?>>
                <i class="fas fa-sync"></i> Update Website Pages
            </button>
            <?php if (empty($products)): ?>
                <button type="submit" name="action" value="add_sample" class="btn btn-dark">
                    <i class="fas fa-flask"></i> Add Sample Products First
                </button>
            <?php endif; ?>
        </form>
    </div>
</div>
</body>
</html>

