<?php
include 'config/db.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$p) {
    die("Sản phẩm không tồn tại!");
}

// Xử lý thêm vào giỏ hàng (Chưa đăng nhập vẫn thêm được bằng Session)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $qty = (int)$_POST['quantity'];
    
    // Kiểm tra số lượng kho trước khi cho vào giỏ
    if ($p['stock'] <= 0 || $qty > $p['stock']) {
        $error = "Số lượng trong kho không đủ!";
    } else {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        // Nếu đã có sản phẩm đó, tăng số lượng nhưng không vượt quá kho
        $current_qty = $_SESSION['cart'][$id] ?? 0;
        if (($current_qty + $qty) <= $p['stock']) {
            $_SESSION['cart'][$id] = $current_qty + $qty;
            header("Location: cart.php");
            exit;
        } else {
            $error = "Số lượng trong giỏ vượt quá hàng trong kho!";
        }
    }
}

include 'includes/header.php';
?>

<div class="product-detail">
    <div class="detail-img">
        <img src="img/<?php echo $p['image']; ?>" alt="<?php echo $p['name']; ?>" onerror="this.src='https://placehold.co/300'">
    </div>
    <div class="detail-info">
        <h2><?php echo htmlspecialchars($p['name']); ?></h2>
        <p class="price"><?php echo number_format($p['price'], 0, ',', '.'); ?> đ</p>
        <p class="desc"><?php echo nl2br(htmlspecialchars($p['description'])); ?></p>
        
        <?php if(isset($error)): ?> <p class="alert-danger"><?php echo $error; ?></p> <?php endif; ?>

        <?php if($p['stock'] <= 0): ?>
            <span class="badge out-of-stock">TẠM HẾT HÀNG</span>
        <?php else: ?>
            <p>Kho còn: <strong><?php echo $p['stock']; ?></strong> cái</p>
            <form action="" method="POST">
                <input type="number" name="quantity" value="1" min="1" max="<?php echo $p['stock']; ?>" required>
                <button type="submit" name="add_to_cart" class="btn-buy">Thêm vào giỏ hàng</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>