<?php
require 'config.php'; require 'functions.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        flash('Enter a name, valid email and password of at least 8 characters.', 'danger');
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (name,email,phone,password_hash,role) VALUES (?,?,?,?,?)');
            $stmt->execute([$name,$email,$phone,password_hash($password, PASSWORD_DEFAULT),'student']);
            flash('Registration successful. Please log in.');
            header('Location: login.php'); exit;
        } catch (PDOException $ex) {
            flash('This email may already be registered. Try another email.', 'danger');
        }
    }
}
$page_title='Student Registration'; require 'header.php';
?>
<div class="form-card mx-auto"><h2 class="mb-3">Student Registration</h2>
<form method="post">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="mb-3"><label class="form-label">Full name</label><input class="form-control" name="name" required></div>
<div class="mb-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required></div>
<div class="mb-3"><label class="form-label">Phone</label><input class="form-control" name="phone"></div>
<div class="mb-3"><label class="form-label">Password (minimum 8 characters)</label><input class="form-control" type="password" name="password" minlength="8" required></div>
<button class="btn btn-primary w-100">Create Account</button>
</form><p class="mt-3 mb-0">Already registered? <a href="login.php">Login</a></p></div>
<?php require 'footer.php'; ?>
