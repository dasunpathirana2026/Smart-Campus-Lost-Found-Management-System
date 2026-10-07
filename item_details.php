<?php
require_once 'includes/functions.php';
$id=(int)($_GET['id']??0);
$stmt=db()->prepare("SELECT p.*,u.name owner_name,u.email owner_email FROM posts p JOIN users u ON u.id=p.user_id WHERE p.id=?");
$stmt->execute([$id]); $p=$stmt->fetch();
if(!$p){http_response_code(404);exit('Item not found.');}
$page_title=$p['title']; include 'includes/header.php';
?>
<div class="section-head"><div><h2><?=e($p['title'])?></h2><p class="muted"><?=e(ucfirst($p['type']))?> item report</p></div></div>
<div class="detail"><div><?php if($p['image']):?><img class="detail-img" src="<?=BASE_URL.e($p['image'])?>" alt="Item"><?php else:?><div class="item-placeholder" style="height:420px;border-radius:18px">No photo</div><?php endif;?></div>
<div class="info-card"><span class="badge badge-<?=$p['type']?>"><?=e($p['type'])?></span><h2><?=e($p['title'])?></h2><p><?=nl2br(e($p['description']))?></p><div class="info-row"><strong>Location</strong><br><?=e($p['location'])?></div><div class="info-row"><strong><?= $p['type']==='lost'?'Date Lost':'Date Found' ?></strong><br><?=e(date('d M Y',strtotime($p['item_date'])))?></div><div class="info-row"><strong>Posted</strong><br><?=e(date('d M Y, h:i A',strtotime($p['created_at'])))?></div>
<?php if(is_logged_in() && current_user()['role']==='student' && current_user()['id']!=$p['user_id']):?><div class="info-row"><strong>Contact owner / reporter</strong><br><a style="color:#4f46e5;font-weight:700" href="tel:<?=e($p['phone'])?>">📞 <?=e($p['phone'])?></a><br><a style="color:#4f46e5" href="mailto:<?=e($p['owner_email'])?>">✉ <?=e($p['owner_email'])?></a></div><?php elseif(!is_logged_in()):?><div class="alert" style="background:#eef2ff;color:#3730a3">Login as a student to view contact details.</div><?php endif;?>
</div></div>
<?php include 'includes/footer.php';?>