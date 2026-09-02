<?php $per_row = (int)($content['cards_per_row'] ?? 3); if ($per_row < 2 || $per_row > 4) $per_row = 3; ?>
<section class="dynamic-page-section builder-cards" style="padding:40px 20px;">
    <div class="dynamic-page-container" style="max-width:1200px; margin:0 auto;">
        <?php if (!empty($content['kicker']) || !empty($content['heading']) || !empty($content['subtitle'])): ?>
        <div style="text-align:center; margin-bottom:24px;">
            <?php if (!empty($content['kicker'])): ?>
                <span class="sec-span"><?php echo htmlspecialchars($content['kicker']); ?></span>
            <?php endif; ?>
            <?php if (!empty($content['heading'])): ?>
                <h2 class="section-title"><?php echo htmlspecialchars($content['heading']); ?></h2>
            <?php endif; ?>
            <?php if (!empty($content['subtitle'])): ?>
                <p class="section-subtitle"><?php echo htmlspecialchars($content['subtitle']); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <div class="builder-cards-grid" style="display:grid; grid-template-columns:repeat(<?php echo $per_row; ?>, 1fr); gap:24px; margin-top:24px;">
            <?php foreach ($items as $card): ?>
               <div class="quick-link-card" style="text-align:center; border:2px solid #e3e6ec;">
                    <?php if (!empty($card['image'])): ?>
                        <img src="<?php echo htmlspecialchars($card['image']); ?>" alt="<?php echo htmlspecialchars($card['title']); ?>" style="max-width:64px; margin:0 auto 16px; display:block;">
                    <?php elseif (!empty($card['icon'])): ?>
                        <div style="width:64px; height:64px; margin:0 auto 16px; display:flex; align-items:center; justify-content:center; border-radius:50%; background:#eef1f5;">
                            <i class="<?php echo htmlspecialchars($card['icon']); ?>" style="font-size:26px; color:#0f2565;"></i>
                        </div>
                    <?php endif; ?>
                    <h3 class="card-title"><?php echo htmlspecialchars($card['title']); ?></h3>
                    <p class="card-text"><?php echo htmlspecialchars($card['description']); ?></p>
                    <?php if (!empty($card['link'])): ?>
                        <a href="<?php echo htmlspecialchars($card['link']); ?>" style="color:#0f2565; font-weight:700; text-decoration:none;">Learn more &rarr;</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<style>
@media(max-width:768px){ .builder-cards-grid{ grid-template-columns:repeat(2,1fr) !important; } }
@media(max-width:480px){ .builder-cards-grid{ grid-template-columns:1fr !important; } }
</style>