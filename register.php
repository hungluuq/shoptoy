<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $email = trim($_POST['email']);

    // Kiểm tra trùng lặp trùng
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        $error = "Tài khoản này đã tồn tại!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $password, $email])) {
            header("Location: login.php");
            exit;
        }
    }
}
include 'includes/header.php';
?>
<div class="auth-form">
    <h2>Đăng Ký Tài Khoản</h2>
    <?php if(isset($error)): ?> <p class="alert-danger"><?php echo $error; ?></p> <?php endif; ?>
    <form action="" method="POST">
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit" class="btn">Đăng ký</button>
    </form>
</div>
<?php include 'includes/footer.php'; ?>