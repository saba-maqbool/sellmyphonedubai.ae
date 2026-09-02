<div class="row g-2 mb-3 pb-3" style="border-bottom:1px dashed #e3e6ec;">
    <div class="col-md-4">
        <label class="form-label">Kicker <small class="text-muted">(optional)</small></label>
        <input type="text" name="kicker" class="form-control" placeholder="e.g. Why Choose Us" value="<?php echo htmlspecialchars($s['content_decoded']['kicker'] ?? ''); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Heading <small class="text-muted">(optional)</small></label>
        <input type="text" name="heading" class="form-control" placeholder="Section heading" value="<?php echo htmlspecialchars($s['content_decoded']['heading'] ?? ''); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Subtitle <small class="text-muted">(optional)</small></label>
        <input type="text" name="subtitle" class="form-control" placeholder="Short supporting line" value="<?php echo htmlspecialchars($s['content_decoded']['subtitle'] ?? ($s['content_decoded']['subtext'] ?? '')); ?>">
    </div>
</div>