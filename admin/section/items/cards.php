<?php foreach ($s['items'] as $card): ?>
    <div class="d-flex justify-content-between align-items-center border-bottom py-1">
        <span>
            <?php if (!empty($card['icon'])): ?><i class="<?php echo htmlspecialchars($card['icon']); ?>" style="margin-right:6px; color:#0f2565;"></i><?php endif; ?>
            <?php echo htmlspecialchars($card['title']); ?>
        </span>
        <a href="?page_id=<?php echo $page_id; ?>&delete_card=<?php echo $card['id']; ?>" class="text-danger" onclick="return confirm('Delete?');"><i class="fa-solid fa-trash"></i></a>
    </div>
<?php endforeach; ?>
<form method="POST" enctype="multipart/form-data" class="mt-2 row g-2">
    <input type="hidden" name="section_id" value="<?php echo $s['id']; ?>">
    <div class="col-3"><input type="text" name="title" class="form-control form-control-sm" placeholder="Title" required></div>
    <div class="col-3"><input type="text" name="description" class="form-control form-control-sm" placeholder="Description"></div>
    <div class="col-2"><input type="text" name="icon" class="form-control form-control-sm" placeholder="Icon (fa-solid fa-mobile)" title="Font Awesome class, e.g. fa-solid fa-mobile-screen"></div>
    <div class="col-2"><input type="file" name="image" class="form-control form-control-sm" title="Image (optional — used instead of icon if both are set)"></div>
    <div class="col-2"><button type="submit" name="add_card" class="btn btn-sm w-100" style="background:#0B1E3F;color:#fff;">Add</button></div>
</form>
<small class="text-muted d-block mt-1">Add either an Icon (Font Awesome class) or an Image — if both are set, the Image is shown. Icon list: <a href="https://fontawesome.com/search?ip=classic&s=solid" target="_blank">fontawesome.com</a></small>