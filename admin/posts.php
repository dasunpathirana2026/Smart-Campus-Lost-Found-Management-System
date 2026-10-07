<?php
require_once '../includes/functions.php'; require_admin();
if($_SERVER['REQUEST_METHOD']==='POST'){
  $id=filter_var($_POST['post_id']??null,FILTER_VALIDATE_INT);
  $action=post_text('action');
  if(!valid_csrf_token() || $id===false || $id===null || $id<1 || !in_array($action,['delete','close'],true)){
    flash('error','The request could not be verified. Please try again.');
    redirect(BASE_URL.'admin/posts.php');
  }
  if($action==='delete'){
    $stmt=db()->prepare("DELETE FROM posts WHERE id=?");
    $stmt->execute([$id]);
    $changed=$stmt->rowCount()>0;
    flash($changed?'success':'error',$changed?'Post deleted.':'That post could not be found.');
  }else{
    $stmt=db()->prepare("UPDATE posts SET status='returned' WHERE id=? AND status='open'");
    $stmt->execute([$id]);
    $changed=$stmt->rowCount()>0;
    flash($changed?'success':'error',$changed?'Post marked as returned.':'That post could not be updated.');
  }
  redirect(BASE_URL.'admin/posts.php');
}
$posts=db()->query("SELECT p.*,u.name owner_name FROM posts p JOIN users u ON u.id=p.user_id ORDER BY p.created_at DESC")->fetchAll();
$page_title='Manage Posts'; include '../includes/header.php';
?>
<div class="section-head"><div><h2>Manage Posts</h2><p class="muted">Review and moderate all lost & found reports.</p></div></div>
<div class="table-wrap"><table class="table"><tr><th>Item</th><th>Type</th><th>Student</th><th>Location</th><th>Status</th><th>Actions</th></tr><?php foreach($posts as $p):?><tr><td><?=e($p['title'])?></td><td><?=e($p['type'])?></td><td><?=e($p['owner_name'])?></td><td><?=e($p['location'])?></td><td><?=e($p['status'])?></td><td><a class="btn btn-outline" href="<?=BASE_URL?>item_details.php?id=<?=(int)$p['id']?>">View</a> <?php if($p['status']==='open'):?><form class="inline-form" method="post"><?=csrf_field()?><input type="hidden" name="post_id" value="<?=(int)$p['id']?>"><input type="hidden" name="action" value="close"><button class="btn btn-success" type="submit">Close</button></form><?php endif;?> <form class="inline-form" method="post" onsubmit="return confirm('Delete this post?')"><?=csrf_field()?><input type="hidden" name="post_id" value="<?=(int)$p['id']?>"><input type="hidden" name="action" value="delete"><button class="btn btn-danger" type="submit">Delete</button></form></td></tr><?php endforeach;?></table></div>
<?php include '../includes/footer.php';?>