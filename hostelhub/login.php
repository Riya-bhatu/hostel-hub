<?php
require 'config.php'; require 'functions.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $stmt = $pdo->prepare('SELECT id,name,email,password_hash,role FROM users WHERE email=?');
    $stmt->execute([$email]); $user = $stmt->fetch();
    if ($user && password_verify($_POST['password'] ?? '', $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        flash('Welcome, ' . $user['name'] . '!');
        header('Location: dashboard.php'); exit;
    }
    flash('Invalid email or password.', 'danger');
}
$page_title='Login'; require 'header.php';
?>
<div class="form-card mx-auto"><h2 class="mb-3">Login</h2>
<form method="post">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="mb-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required></div>
<div class="mb-3"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
<button class="btn btn-primary w-100">Login</button>
</form><p class="mt-3 mb-0">New student? <a href="register.php">Register here</a></p></div>
<?php require 'footer.php'; ?>
