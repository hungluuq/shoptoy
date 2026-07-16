<?php
include 'config/db.php';
include 'includes/header.php';

// 1. Tìm kiếm và trang hiện tại
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$limit = 12; // Số lượng sản phẩm hiển thị trên mỗi trang
$offset = ($page - 1) * $limit;

// 2. Tính tổng số sản phẩm phù hợp (để tính tổng số trang)
if ($search !== '') {
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name LIKE ?");
    $count_stmt->execute(["%$search%"]);
} else {
    $count_stmt = $pdo->query("SELECT COUNT(*) FROM products");
}
$total_products = $count_stmt->fetchColumn();
$total_pages = ceil($total_products / $limit);

// Chốt chặn an toàn: Nếu số trang người dùng nhập lớn hơn thực tế
if ($page > $total_pages && $total_pages > 0) {
    $page = $total_pages;
    $offset = ($page - 1) * $limit;
}

// 3. Lấy dữ liệu sản phẩm giới hạn theo LIMIT và OFFSET
if ($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE :search LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
} else {
    $stmt = $pdo->prepare("SELECT * FROM products LIMIT :limit OFFSET :offset");
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Danh sách Đồ chơi</h2>
<div class="product-grid">
    <?php if(count($products) > 0): ?>
        <?php foreach($products as $p): ?>
            <div class="product-card">
                <img src="img/<?php echo $p['image']; ?>" alt="<?php echo $p['name']; ?>" onerror="this.src='https://placehold.co/150'">
                <h3><?php echo htmlspecialchars($p['name']); ?></h3>
                <p class="price"><?php echo number_format($p['price'], 0, ',', '.'); ?> đ</p>
                
                <!-- Kiểm tra số lượng kho -->
                <?php if($p['stock'] <= 0): ?>
                    <span class="btn btn-out-of-stock">Hết hàng</span>
                <?php else: ?>
                    <p class="stock-info">Còn lại: <?php echo $p['stock']; ?> sản phẩm</p>
                    <a href="product.php?id=<?php echo $p['id']; ?>" class="btn">Xem chi tiết</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Không tìm thấy sản phẩm nào phù hợp.</p>
    <?php endif; ?>
</div>

<!-- PHÂN TRANG (PAGINATION) -->
<?php if ($total_pages > 1): ?>
    <div class="pagination">
        <!-- Nút quay lại trang trước (Prev) -->
        <?php if ($page > 1): ?>
            <a href="index.php?search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>" class="page-link">&laquo; Trước</a>
        <?php endif; ?>

        <!-- Danh sách các số trang -->
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <!-- Giữ lại từ khóa search bằng cách truyền urlencode($search) -->
            <a href="index.php?search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>" 
               class="page-link <?php echo ($i === $page) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <!-- Nút đi tới trang sau (Next) -->
        <?php if ($page < $total_pages): ?>
            <a href="index.php?search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>" class="page-link">Sau &raquo;</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>