<?php
require_once 'includes/functions.php';
$page_title = 'Home';
include 'includes/header.php';
?>
<section class="hero">
  <div class="hero-copy">
    <span class="hero-badge"><span class="hero-badge-led" aria-hidden="true"></span>Campus community</span>
    <h1>Find it. Report it. Return it.</h1>
    <p>A simple campus platform for reporting lost belongings, sharing found items, and helping students reconnect with what matters most.</p>
    <div class="hero-actions">
      <a class="btn btn-light" href="<?= BASE_URL ?>lost_items.php">Browse lost items <span aria-hidden="true">→</span></a>
      <a class="btn btn-hero-outline" href="<?= BASE_URL ?>found_items.php">Browse found items</a>
      <?php if (!is_logged_in()): ?>
        <a class="btn hero-account" href="<?= BASE_URL ?>register.php">Create account</a>
      <?php endif; ?>
    </div>
  </div>
  <div class="hero-visual" aria-hidden="true">
    <div class="hero-visual-orbit hero-visual-orbit-one"></div>
    <div class="hero-visual-orbit hero-visual-orbit-two"></div>
    <div class="hero-visual-center">
      <span class="hero-visual-spark">✦</span>
      <span class="hero-visual-search">⌕</span>
      <span class="hero-visual-center-label">REUNITED</span>
    </div>
    <div class="hero-item hero-item-wallet">
      <span class="hero-item-icon hero-item-icon-purple">▣</span>
      <span><strong>Wallet</strong><small>Ready to find</small></span>
      <span class="hero-item-dot"></span>
    </div>
    <div class="hero-item hero-item-keys">
      <span class="hero-item-icon hero-item-icon-teal">⌘</span>
      <span><strong>Keys</strong><small>Back to owner</small></span>
      <span class="hero-item-check">✓</span>
    </div>
    <div class="hero-visual-caption"><span></span> Little things matter</div>
  </div>
</section>

<div class="home-flow" aria-label="How it works">
  <div class="home-flow-step"><span>01</span><strong>Report an item</strong></div>
  <span class="home-flow-connector" aria-hidden="true"></span>
  <div class="home-flow-step"><span>02</span><strong>Make a match</strong></div>
  <span class="home-flow-connector" aria-hidden="true"></span>
  <div class="home-flow-step"><span>03</span><strong>Bring it home</strong></div>
</div>

<section class="feature-grid">
  <article class="feature-card">
    <div class="feature-icon">📍</div>
    <h3>Fast item matching</h3>
    <p>Students can quickly post location details and connect with the right owner using a reliable campus-wide search.</p>
  </article>

  <article class="feature-card">
    <div class="feature-icon">🔒</div>
    <h3>Secure contact flow</h3>
    <p>Only active student accounts can access private contact information and contact the person who reported the item.</p>
  </article>

  <article class="feature-card">
    <div class="feature-icon">📸</div>
    <h3>Clear evidence</h3>
    <p>Upload photos, add notes, and share precise item details so lost belongings are easier to identify and recover.</p>
  </article>
</section>

<?php include 'includes/footer.php'; ?>