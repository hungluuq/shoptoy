<?php
include 'config/db.php';

// 1. Phải đăng nhập mới được mua hàng
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = 'checkout.php'; // Lưu lại để quay lại sau
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Bắt đầu Transaction để tránh lỗi đồng bộ dữ liệu kho
        $pdo->beginTransaction();

        $total_price = 0;
        $items_to_buy = [];

        // Duyệt qua để tính tổng tiền và check kho chốt chặn cuối
        foreach ($_SESSION['cart'] as $p_id => $qty) {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? FOR UPDATE");
            $stmt->execute([$p_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product['stock'] < $qty) {
                throw new Exception("Sản phẩm '" . $product['name'] . "' không đủ hàng trong kho (Hiện còn: " . $product['stock'] . "). Vui lòng cập nhật lại giỏ hàng!");
            }

            $total_price += $product['price'] * $qty;
            $items_to_buy[] = [
                'id' => $p_id,
                'qty' => $qty,
                'price' => $product['price']
            ];
        }

        // Tạo Đơn hàng mới
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
        $stmt->execute([$user_id, $total_price]);
        $order_id = $pdo->lastInsertId();

        // Thêm chi tiết đơn hàng & TRỪ SỐ LƯỢNG KHO
        foreach ($items_to_buy as $item) {
            // Lưu chi tiết
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $item['id'], $item['qty'], $item['price']]);

            // Cập nhật giảm kho
            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$item['qty'], $item['id']]);
        }

        // Hoàn tất mua
        $pdo->commit();
        unset($_SESSION['cart']); // Xóa sạch giỏ hàng
        $success = true;

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}

include 'includes/header.php';
?>

<h2>Thanh Toán Đơn Hàng</h2>
<?php if(isset($success)): ?>
    <div class="alert-success">
        <p>🎉 Đặt hàng thành công! Đơn hàng của bạn đang được xử lý.</p>
        <p><a href="orders.php">Xem lại lịch sử đơn hàng tại đây.</a></p>
    </div>
<?php else: ?>
    <?php if(!empty($error)): ?> <p class="alert-danger"><?php echo $error; ?></p> <?php endif; ?>
    <p>Bạn đang đặt đơn với tổng giá trị thanh toán.</p>
    <form action="" method="POST">
        <button type="submit" class="btn-buy" style="width: 100%;">Xác nhận Mua Ngay (Thanh toán khi nhận hàng COD)</button>
    </form>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>