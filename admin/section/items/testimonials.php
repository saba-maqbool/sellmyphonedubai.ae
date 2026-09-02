<?php foreach ($s['items'] as $t): ?>
    <div class="d-flex justify-content-between align-items-center border-bottom py-1">
        <span><?php echo htmlspecialchars($t['name']); ?></span>
        <a href="?page_id=<?php echo $page_id; ?>&delete_testimonial=<?php echo $t['id']; ?>" class="text-danger" onclick="return confirm('Delete?');"><i class="fa-solid fa-trash"></i></a>
    </div>
<?php endforeach; ?>
<form method="POST" enctype="multipart/form-data" class="mt-2 row g-2">
    <input type="hidden" name="section_id" value="<?php echo $s['id']; ?>">
    <div class="col-3"><input type="text" name="name" class="form-control form-control-sm" placeholder="Name" required></div>
    <div class="col-3"><input type="text" name="review" class="form-control form-control-sm" placeholder="Review"></div>
    <div class="col-2"><input type="number" name="rating" min="1" max="5" value="5" class="form-control form-control-sm"></div>
    <div class="col-2"><input type="file" name="image" class="form-control form-control-sm"></div>
    <div class="col-2"><button type="submit" name="add_testimonial" class="btn btn-sm w-100" style="background:#0B1E3F;color:#fff;">Add</button></div>
</form>