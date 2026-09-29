<article class="item-card">
    <div class="item-photo-wrap">
        <img class="item-photo" src="<?= e($item['photo_path']) ?>" alt="Photo of <?= e($item['title']) ?>" loading="lazy">
        <span class="badge badge-<?= e($item['item_type']) ?>"><?= e(ucfirst($item['item_type'])) ?></span>
    </div>
    <div class="item-card-body">
        <div class="item-card-topline"><span><?= e($item['category_name']) ?></span><span class="badge badge-<?= e($item['status']) ?>"><?= e(ucfirst($item['status'])) ?></span></div>
        <h2><?= e($item['title']) ?></h2>
        <p class="item-description"><?= e($item['description']) ?></p>
        <dl class="item-meta"><div><dt>Location</dt><dd><?= e($item['location']) ?></dd></div><div><dt>Date</dt><dd><?= e(date('M j, Y', strtotime($item['item_date']))) ?></dd></div></dl>
        <?php if (current_user() && $item['item_type'] === 'found' && $item['status'] === 'open' && (int)$item['user_id'] !== (int)current_user()['id']): ?>
            <form class="claim-form" method="post" action="claim.php">
                <?= csrf_field() ?><input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">
                <label for="proof-<?= (int)$item['id'] ?>">How can you identify this item?</label>
                <textarea id="proof-<?= (int)$item['id'] ?>" name="proof" rows="2" maxlength="1000" required placeholder="Describe a detail only the owner would know"></textarea>
                <button class="button button-small" type="submit">Submit claim</button>
            </form>
        <?php endif; ?>
    </div>
</article>
