<div class="mb-2"><label class="form-label">Kicker</label><input type="text" name="kicker" class="form-control" value="<?php echo htmlspecialchars($s['content_decoded']['kicker'] ?? ''); ?>"></div>
<div class="mb-2"><label class="form-label">Heading</label><input type="text" name="heading" class="form-control" value="<?php echo htmlspecialchars($s['content_decoded']['heading'] ?? ''); ?>"></div>
<div class="mb-2"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($s['content_decoded']['description'] ?? ''); ?></textarea></div>
<div class="row">
    <div class="col-6"><label class="form-label">Button Text</label><input type="text" name="button_text" class="form-control" value="<?php echo htmlspecialchars($s['content_decoded']['button_text'] ?? ''); ?>"></div>
    <div class="col-6"><label class="form-label">Button Link</label><input type="text" name="button_link" class="form-control" value="<?php echo htmlspecialchars($s['content_decoded']['button_link'] ?? ''); ?>"></div>
</div>
<div class="mb-2 mt-2"><label class="form-label">Image Position</label>
    <select name="image_position" class="form-control">
        <option value="right" <?php echo (($s['content_decoded']['image_position'] ?? 'right') === 'right') ? 'selected' : ''; ?>>Right</option>
        <option value="left" <?php echo (($s['content_decoded']['image_position'] ?? '') === 'left') ? 'selected' : ''; ?>>Left</option>
    </select>
</div>
<div class="mb-2"><label class="form-label">Image</label><input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
    <?php if (!empty($s['content_decoded']['image'])): ?><img src="../<?php echo htmlspecialchars($s['content_decoded']['image']); ?>" style="max-width:100px; margin-top:8px;"><?php endif; ?>
</div>