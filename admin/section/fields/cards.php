<?php include __DIR__ . '/_header_common.php'; ?>
<div class="mb-2"><label class="form-label">Cards per row</label>
    <select name="cards_per_row" class="form-control">
        <?php foreach ([2,3,4] as $n): ?>
            <option value="<?php echo $n; ?>" <?php echo (($s['content_decoded']['cards_per_row'] ?? 3) == $n) ? 'selected' : ''; ?>><?php echo $n; ?></option>
        <?php endforeach; ?>
    </select>
</div>