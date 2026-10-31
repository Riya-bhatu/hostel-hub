<?php
require 'config.php'; require 'functions.php'; require_login();
if (($_SESSION['role'] ?? '')==='admin') { header('Location: admin_complaints.php'); exit; }
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf(); $subject=trim($_POST['subject'] ?? ''); $description=trim($_POST['description'] ?? '');
    if ($subject==='' || $description==='') flash('Please complete all complaint fields.','danger');
    else { $s=$pdo->prepare('INSERT INTO complaints (user_id,subject,description,status) VALUES (?,?,?,?)'); $s->execute([current_user_id(),$subject,$description,'Open']); flash('Complaint submitted.'); }
    header('Location: complaints.php'); exit;
}
$s=$pdo->prepare('SELECT * FROM complaints WHERE user_id=? ORDER BY created_at DESC'); $s->execute([current_user_id()]); $rows=$s->fetchAll();
$page_title='Complaints'; require 'header.php';
?>
<h2>Submit a Complaint</h2><div class="card mb-4"><div class="card-body"><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="mb-3"><label class="form-label">Subject</label><input class="form-control" name="subject" maxlength="150" required></div><div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3" required></textarea></div><button class="btn btn-primary">Submit Complaint</button></form></div></div>
<h3>My Complaints</h3><div class="table-responsive"><table class="table table-striped"><thead><tr><th>Subject</th><th>Description</th><th>Status</th><th>Date</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= e($r['subject']) ?></td><td><?= e($r['description']) ?></td><td><?= e($r['status']) ?></td><td><?= e($r['created_at']) ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php require 'footer.php'; ?>
