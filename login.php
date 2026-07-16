<?php
include 'config/db.php';

// Đăng xuất
if (($_GET['action'] ?? '') === 'logout') {
    unset($_SESSION['user_id']);
    unset($_SESSION['username']);
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Lưu Session đăng nhập
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // GIỎ HÀNG TỰ ĐỘNG ĐƯỢC GIỮ LẠI (Gộp tự động nhờ cơ chế Session chung)
        header("Location: cart.php");
        exit;
    } else {
        $error = "Sai tài khoản hoặc mật khẩu!";
    }
}
include 'includes/header.php';
?>

<div class="auth-form">
    <h2>Đăng Nhập</h2>
    <?php if(isset($error)): ?> <p class="alert-danger"><?php echo $error; ?></p> <?php endif; ?>
    <form action="" method="POST">
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit" class="btn">Đăng nhập</button>
    </form>
    <p>Chưa có tài khoản? <a href="register.php">Đăng ký</a></p>
</div>

<?php include 'includes/footer.php'; ?>