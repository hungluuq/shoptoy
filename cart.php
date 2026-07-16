<?php
include 'config/db.php';

// Xử lý xóa sản phẩm khỏi giỏ
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    unset($_SESSION['cart'][$remove_id]);
    header("Location: cart.php");
    exit;
}

include 'includes/header.php';
?>

<h2>Giỏ hàng của bạn</h2>
<?php if (!empty($_SESSION['cart'])): ?>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Tổng số tiền</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_cart = 0;
            // Lấy ID các sản phẩm hiện có trong giỏ
            $ids = implode(',', array_keys($_SESSION['cart']));
            $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
            while($p = $stmt->fetch(PDO::FETCH_ASSOC)):
                $qty = $_SESSION['cart'][$p['id']];
                $subtotal = $p['price'] * $qty;
                $total_cart += $subtotal;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($p['name']); ?></td>
                <td><?php echo number_format($p['price'], 0, ',', '.'); ?> đ</td>
                <td><?php echo $qty; ?></td>
                <td><?php echo number_format($subtotal, 0, ',', '.'); ?> đ</td>
                <td><a href="cart.php?remove=<?php echo $p['id']; ?>" class="text-danger">Xóa</a></td>
            </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="3" align="right"><strong>Tổng cộng đơn hàng:</strong></td>
                <td colspan="2"><strong><?php echo number_format($total_cart, 0, ',', '.'); ?> đ</strong></td>
            </tr>
        </tbody>
    </table>
    
    <div class="checkout-actions">
        <!-- Nút Thanh Toán -->
        <a href="checkout.php" class="btn-buy">Tiến hành thanh toán</a>
    </div>
<?php else: ?>
    <p>Giỏ hàng của bạn đang trống. <a href="index.php">Quay lại mua sắm!</a></p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>