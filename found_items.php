<?php
require_once 'includes/functions.php';
$items=db()->query("SELECT p.*,u.name owner_name FROM posts p JOIN users u ON u.id=p.user_id WHERE p.type='found' AND p.status='open' ORDER BY p.created_at DESC")->fetchAll();
$page_title='Found Items'; include 'includes/header.php';
?>
<div class="section-head"><div><h2>Found Items</h2><p class="muted">Items reported as found by students.</p></div><?php if(is_logged_in() && in_array(current_user()['role'], ['student', 'admin'], true)):?><a class="btn btn-found-action" href="<?=BASE_URL?>add_found_item.php">Report Found Item</a><?php endif;?></div>
<div class="grid"><?php foreach($items as $p):?><article class="card"><?php if($p['image']):?><img class="item-img" src="<?=BASE_URL.e($p['image'])?>" alt="<?=e($p['title'])?>"><?php else:?><div class="item-placeholder" aria-hidden="true">✓</div><?php endif;?><div class="card-body"><span class="badge badge-found">Found</span><h3><?=e($p['title'])?></h3><div class="meta">📍 <?=e($p['location'])?><br>📅 Found <?=e(date('d M Y',strtotime($p['item_date'])))?></div><br><a class="btn btn-primary" href="<?=BASE_URL?>item_details.php?id=<?=(int)$p['id']?>">View details</a></div></article><?php endforeach;?></div>
<?php if(!$items):?><div class="empty">No found-item reports yet.</div><?php endif;?><?php include 'includes/footer.php';?>