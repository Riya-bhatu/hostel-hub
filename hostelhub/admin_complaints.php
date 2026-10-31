<?php
require 'config.php'; require 'functions.php'; require_admin();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf(); $id=(int)$_POST['id']; $action=$_POST['action'] ?? '';
    if ($action==='update') {
        $status=$_POST['status'] ?? '';
        if (in_array($status,['Open','In Progress','Resolved'],true)) { $s=$pdo->prepare('UPDATE complaints SET status=? WHERE id=?'); $s->execute([$status,$id]); flash('Complaint status updated.'); }
        else flash('Invalid status.','danger');
    } elseif ($action==='delete') {
        $s=$pdo->prepare('DELETE FROM complaints WHERE id=?'); $s->execute([$id]); flash('Complaint deleted.','info');
    }
    header('Location: admin_complaints.php'); exit;
}
$rows=$pdo->query('SELECT c.*,u.name,u.email FROM complaints c JOIN users u ON u.id=c.user_id ORDER BY c.created_at DESC')->fetchAll();
$page_title='Manage Complaints'; require 'header.php';
?>
<h2 class="mb-3">Manage Student Complaints</h2><div class="table-responsive"><table class="table table-striped align-middle"><thead><tr><th>Student</th><th>Subject / Description</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?= e($r['name']) ?><br><small><?= e($r['email']) ?></small></td><td><strong><?= e($r['subject']) ?></strong><br><?= e($r['description']) ?></td><td><?= e($r['created_at']) ?></td><td><?= e($r['status']) ?></td><td><form method="post" class="d-flex gap-2 mb-2"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= e($r['id']) ?>"><input type="hidden" name="action" value="update"><select class="form-select form-select-sm" name="status"><?php foreach(['Open','In Progress','Resolved'] as $st): ?><option <?= $r['status']===$st?'selected':'' ?>><?= e($st) ?></option><?php endforeach; ?></select><button class="btn btn-sm btn-primary">Save</button></form><form method="post" onsubmit="return confirm('Delete this complaint?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= e($r['id']) ?>"><input type="hidden" name="action" value="delete"><button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr><?php endforeach; ?>
</tbody></table></div><?php if(!$rows): ?><p>No complaints have been submitted.</p><?php endif; ?>
<?php require 'footer.php'; ?>
