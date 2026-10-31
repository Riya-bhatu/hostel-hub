<?php
require 'config.php'; require 'functions.php'; require_login();
$uid=current_user_id();
if (($_SESSION['role'] ?? '') === 'admin') {
    $students=(int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
    $rooms=(int)$pdo->query("SELECT COUNT(*) FROM rooms")->fetchColumn();
    $pending=(int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status='Pending'")->fetchColumn();
    $complaints=(int)$pdo->query("SELECT COUNT(*) FROM complaints WHERE status<>'Resolved'")->fetchColumn();
} else {
    $stmt=$pdo->prepare('SELECT COUNT(*) FROM bookings WHERE user_id=?'); $stmt->execute([$uid]); $bookings=(int)$stmt->fetchColumn();
    $stmt=$pdo->prepare('SELECT COUNT(*) FROM complaints WHERE user_id=?'); $stmt->execute([$uid]); $complaints=(int)$stmt->fetchColumn();
}
$page_title='Dashboard'; require 'header.php';
?>
<h2 class="mb-1">Welcome, <?= e($_SESSION['name']) ?>!</h2><p class="text-muted">Your HostelHub dashboard</p>
<?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
<div class="row g-3">
<?php foreach (['Registered students'=>$students,'Total rooms'=>$rooms,'Pending bookings'=>$pending,'Open complaints'=>$complaints] as $label=>$number): ?>
<div class="col-sm-6 col-lg-3"><div class="stat-card"><div class="text-muted"><?= e($label) ?></div><div class="display-6 fw-bold"><?= $number ?></div></div></div>
<?php endforeach; ?>
</div>
<div class="mt-4 d-flex flex-wrap gap-2"><a class="btn btn-primary" href="rooms.php">Manage Rooms</a><a class="btn btn-outline-primary" href="admin_bookings.php">Manage Bookings</a><a class="btn btn-outline-primary" href="admin_complaints.php">Manage Complaints</a></div>
<?php else: ?>
<div class="row g-3">
<div class="col-md-6"><div class="stat-card"><div class="text-muted">My room requests</div><div class="display-6 fw-bold"><?= $bookings ?></div><a href="my_bookings.php">View bookings</a></div></div>
<div class="col-md-6"><div class="stat-card"><div class="text-muted">My complaints</div><div class="display-6 fw-bold"><?= $complaints ?></div><a href="complaints.php">View complaints</a></div></div>
</div>
<div class="mt-4 d-flex flex-wrap gap-2"><a class="btn btn-primary" href="rooms.php">Browse Rooms</a><a class="btn btn-outline-primary" href="complaints.php">Submit a Complaint</a></div>
<?php endif; ?>
<?php require 'footer.php'; ?>
