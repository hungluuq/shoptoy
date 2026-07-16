<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cửa Hàng Đồ Chơi HuTa Shop</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <div class="navbar">
        <a href="index.php" class="logo">🧩 HuTa Shop</a>
        
        <!-- Thanh tìm kiếm -->
        <form action="index.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Tìm kiếm đồ chơi..." value="<?php echo $_GET['search'] ?? ''; ?>">
            <button type="submit">Tìm</button>
        </form>

        <nav>
            <a href="index.php">Sản phẩm</a>
            <a href="cart.php">Giỏ hàng (<?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>)</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="orders.php">Lịch sử đơn</a>
                <span>Chào, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="login.php?action=logout" class="btn-logout">Đăng xuất</a>
            <?php else: ?>
                <a href="login.php">Đăng nhập</a>
                <a href="register.php">Đăng ký</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">