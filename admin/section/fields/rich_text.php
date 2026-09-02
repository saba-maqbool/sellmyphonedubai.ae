<?php include __DIR__ . '/_header_common.php'; ?>
<?php $field_uid = 'html_' . ($s['id'] ?? ('new_' . ($key ?? uniqid()))); ?>
<label class="form-label">HTML Content</label>
<textarea name="html" id="<?php echo $field_uid; ?>" class="form-control html-editor-field" rows="10"><?php echo htmlspecialchars($s['content_decoded']['html'] ?? ''); ?></textarea>