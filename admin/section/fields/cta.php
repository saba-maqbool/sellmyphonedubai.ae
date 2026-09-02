<?php include __DIR__ . '/_header_common.php'; ?>
<div class="row">
    <div class="col-6"><label class="form-label">Button Text</label><input type="text" name="button_text" class="form-control" value="<?php echo htmlspecialchars($s['content_decoded']['button_text'] ?? ''); ?>"></div>
    <div class="col-6"><label class="form-label">Button Link</label><input type="text" name="button_link" class="form-control" value="<?php echo htmlspecialchars($s['content_decoded']['button_link'] ?? ''); ?>"></div>
</div>