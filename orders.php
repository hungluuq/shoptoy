<?php
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy danh sách các đơn hàng của user đó
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<h2>Lịch sử đặt hàng của bạn</h2>
<?php if(count($orders) > 0): ?>
    <?php foreach($orders as $o): ?>
        <div class="order-box">
            <div class="order-header">
                <span><strong>Mã đơn:</strong> #<?php echo $o['id']; ?></span> | 
                <span><strong>Ngày đặt:</strong> <?php echo $o['created_at']; ?></span> |
                <span class="badge status"><?php echo $o['status']; ?></span>
            </div>
            
            <table class="order-items-table">
                <?php
                // Lấy chi tiết các món trong đơn này
                $stmt_items = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                $stmt_items->execute([$o['id']]);
                while($item = $stmt_items->fetch(PDO::FETCH_ASSOC)):
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td>x<?php echo $item['quantity']; ?></td>
                    <td align="right"><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> đ</td>
                </tr>
                <?php endwhile; ?>
                <tr class="total-row">
                    <td colspan="2" align="right">Tổng tiền:</td>
                    <td align="right"><strong><?php echo number_format($o['total_price'], 0, ',', '.'); ?> đ</strong></td>
                </tr>
            </table>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Bạn chưa đặt đơn hàng nào.</p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>