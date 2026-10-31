<?php
require 'config.php'; require 'functions.php'; require_login();
if (($_SESSION['role'] ?? '')!=='student') { http_response_code(403); exit('Students only.'); }
if ($_SERVER['REQUEST_METHOD']!=='POST') { header('Location: rooms.php'); exit; }
verify_csrf();
$room_id=(int)($_POST['room_id'] ?? 0);
$s=$pdo->prepare("SELECT id FROM rooms WHERE id=? AND status='Available'"); $s->execute([$room_id]);
if (!$s->fetch()) { flash('That room is not currently available.','danger'); }
else {
    $s=$pdo->prepare("SELECT id FROM bookings WHERE user_id=? AND status IN ('Pending','Approved')"); $s->execute([current_user_id()]);
    if ($s->fetch()) flash('You already have a pending or approved room request.','warning');
    else { $s=$pdo->prepare("INSERT INTO bookings (user_id,room_id,status) VALUES (?,?, 'Pending')"); $s->execute([current_user_id(),$room_id]); flash('Room request submitted.'); }
}
header('Location: my_bookings.php'); exit;
