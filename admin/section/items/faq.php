<?php foreach ($s['items'] as $faq): ?>
    <div class="d-flex justify-content-between align-items-center border-bottom py-1">
        <span><?php echo htmlspecialchars($faq['question']); ?></span>
        <a href="?page_id=<?php echo $page_id; ?>&delete_faq=<?php echo $faq['id']; ?>" class="text-danger" onclick="return confirm('Delete?');"><i class="fa-solid fa-trash"></i></a>
    </div>
<?php endforeach; ?>
<form method="POST" class="mt-2 row g-2">
    <input type="hidden" name="section_id" value="<?php echo $s['id']; ?>">
    <div class="col-5"><input type="text" name="question" class="form-control form-control-sm" placeholder="Question" required></div>
    <div class="col-5"><input type="text" name="answer" class="form-control form-control-sm" placeholder="Answer"></div>
    <div class="col-2"><button type="submit" name="add_faq" class="btn btn-sm w-100" style="background:#0B1E3F;color:#fff;">Add</button></div>
</form>