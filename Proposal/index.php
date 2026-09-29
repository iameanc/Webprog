<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

$categories = db()->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$items = db()->query("SELECT i.*, c.name AS category_name FROM items i JOIN categories c ON c.id = i.category_id WHERE i.status = 'open' ORDER BY i.created_at DESC LIMIT 48")->fetchAll();
render_header('Lost & found');
?>
<section class="intro-band">
    <div><p class="eyebrow">THE CAMPUS LOST & FOUND</p><h1>Lost something?<br><em>Start here.</em></h1><p class="intro-copy">A shared campus board for lost belongings and the people trying to return them.</p></div>
    <a class="button" href="<?= current_user() ? 'post-item.php' : 'login.php' ?>">Post an item <span aria-hidden="true">↗</span></a>
    <div class="intro-note"><span class="live-dot"></span> Listings are updated as they come in</div>
</section>
<section class="browse-section" aria-labelledby="browse-heading">
    <div class="section-heading"><div><p class="eyebrow">CAMPUS BOARD</p><h2 id="browse-heading">Recent listings</h2></div><span id="result-count" class="result-count"><?= count($items) ?> open items</span></div>
    <form id="search-form" class="filter-bar" role="search">
        <label class="search-field"><span class="sr-only">Search listings</span><span class="search-icon" aria-hidden="true">⌕</span><input type="search" name="q" placeholder="Search items, places, details" autocomplete="off"></label>
        <label><span class="sr-only">Category</span><select name="category"><option value="">All categories</option><?php foreach ($categories as $category): ?><option value="<?= (int)$category['id'] ?>"><?= e($category['name']) ?></option><?php endforeach; ?></select></label>
        <label><span class="sr-only">Listing type</span><select name="type"><option value="">Lost or found</option><option value="lost">Lost</option><option value="found">Found</option></select></label>
        <label><span class="sr-only">Status</span><select name="status"><option value="open">Open</option><option value="returned">Returned</option><option value="">Any status</option></select></label>
        <label class="date-filter"><span class="sr-only">From date</span><input type="date" name="date_from" aria-label="From date"></label>
        <label class="date-filter"><span class="sr-only">To date</span><input type="date" name="date_to" aria-label="To date"></label>
    </form>
    <div id="listing-results" class="item-grid" aria-live="polite">
        <?php foreach ($items as $item): include __DIR__ . '/includes/item-card.php'; endforeach; ?>
        <?php if (!$items): ?><p class="empty-state">No open listings yet. Check back soon or post the first one.</p><?php endif; ?>
    </div>
    <noscript><p class="empty-state">Live filtering needs JavaScript. The newest open listings are shown above.</p></noscript>
</section>
<?php render_footer(); ?>
