<section class="samsung-section" id="samsung-section">
         <ul class="nav nav-pills mb-3 legacy-pill-nav" id="pills-tab-samsung" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="pills-models-tab" data-bs-toggle="pill" data-bs-target="#pills-models" type="button" role="tab" aria-controls="pills-models" aria-selected="true">Models</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="pills-storage-tab" data-bs-toggle="pill" data-bs-target="#pills-storage" type="button" role="tab" aria-controls="pills-storage" aria-selected="false">Storage</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="pills-condition-tab" data-bs-toggle="pill" data-bs-target="#pills-condition" type="button" role="tab" aria-controls="pills-condition" aria-selected="false">Condition</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="pills-accessories-tab" data-bs-toggle="pill" data-bs-target="#pills-accessories" type="button" role="tab" aria-controls="pills-accessories" aria-selected="false">Accessories</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="pills-estimate-tab-samsung" data-bs-toggle="pill" data-bs-target="#pills-estimate-samsung" type="button" role="tab" aria-controls="pills-estimate-samsung" aria-selected="false">Estimate</button>
  </li>
</ul>
<div class="tab-content" id="pills-tabContent-samsung">
  <div class="tab-pane fade show active" id="pills-models" role="tabpanel" aria-labelledby="pills-models-tab" tabindex="0">

  <?php
  require_once(__DIR__ . "/../admin/include/db-connect.php");
  $brand_filter = "Samsung";
  $stmt = mysqli_prepare($conn, "SELECT * FROM models WHERE brand = ? ORDER BY created_at DESC");
  mysqli_stmt_bind_param($stmt, "s", $brand_filter);
  mysqli_stmt_execute($stmt);
  $models_result = mysqli_stmt_get_result($stmt);
  ?>
<div class="model-grid row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3">

    <?php while ($m = mysqli_fetch_assoc($models_result)): ?>
    <div class="col">
        <button type="button" class="model-card"
            data-model-id="<?php echo (int) $m['id']; ?>"
            data-model-name="<?php echo htmlspecialchars($m['model_name']); ?>">
            <span class="model-card-img-wrap">
                <img src="<?php echo htmlspecialchars($m['image']); ?>" class="model-card-img" alt="<?php echo htmlspecialchars($m['image_alt'] ?: $m['model_name']); ?>" loading="lazy">
            </span>
            <span class="model-card-title"><?php echo htmlspecialchars($m['model_name']); ?></span>
        </button>
    </div>
    <?php endwhile; ?>

</div>

<div class="btn-group" role="group" aria-label="Basic example">
  <button type="button" class="btn-baxt" onclick="goBack()">BACK</button>
  <button type="button" class="btn-baxt" onclick="goToTab('pills-storage-tab')">NEXT</button>
</div>

</div>

    <div class="tab-pane fade" id="pills-storage" role="tabpanel" aria-labelledby="pills-storage-tab" tabindex="0">
         <div class="storage-container" id="samsung-storage-container">
            <p class="text-muted">Select a model first to see storage options.</p>
        </div>
        <div class="btn-group-button" role="group" aria-label="Basic example">
            <button type="button" class="btn-baxt" onclick="goToTab('pills-models-tab')">BACK</button>
            <button type="button" class="btn-baxt" onclick="goToTab('pills-condition-tab')">NEXT</button>
         </div>

  </div>
  <div class="tab-pane fade" id="pills-condition" role="tabpanel" aria-labelledby="pills-condition-tab" tabindex="0">
     <div class="condi-container">
            <div class="condi-card" data-key="condition_flawless">
                <span class="h2con">Flawless</span>
                <p>Looks brand new, no scratches or dents.</p>
            </div>

            <div class="condi-card" data-key="condition_good">
                <span class="h2con">Good</span>
                 <p>Light signs of wear, fully working.</p>
            </div>

            <div class="condi-card" data-key="condition_fair">
                <span class="h2con">Fair/Cracked</span>
                 <p>Heavy wear, cracked screen but functional.</p>
            </div>
        </div>
        <div class="btn-group-button" role="group" aria-label="Basic example" style="text-align:center;">
            <button type="button" class="btn-baxt" onclick="goToTab('pills-storage-tab')">BACK</button>
            <button type="button" class="btn-baxt" onclick="goToTab('pills-accessories-tab')">NEXT</button>
        </div>

  </div>
  <div class="tab-pane fade" id="pills-accessories" role="tabpanel" aria-labelledby="pills-accessories-tab" tabindex="0">
    <div class="row">
    <div class="col-md-3">
        <div class="custom-control custom-checkbox image-checkbox">
            <input type="checkbox" class="custom-control-input" id="cs1a" data-key="acc_charger">
            <label class="custom-control-label" for="cs1a">
                 <span class="h2con">Charger</span>
                 <span class="price-tag" data-price-for="acc_charger">Loading...</span>
            </label>
        </div>
    </div>
    <div class="col-md-3">
        <div class="custom-control custom-checkbox image-checkbox">
            <input type="checkbox" class="custom-control-input" id="cs1b" data-key="acc_box">
            <label class="custom-control-label" for="cs1b">
                <span class="h2con">Box</span>
                <span class="price-tag" data-price-for="acc_box">Loading...</span>
            </label>
        </div>
    </div>
    <div class="col-md-3">
        <div class="custom-control custom-checkbox image-checkbox">
            <input type="checkbox" class="custom-control-input" id="cs1c" data-key="acc_earbuds">
            <label class="custom-control-label" for="cs1c">
                <span class="h2con">Earbuds</span>
                <span class="price-tag" data-price-for="acc_earbuds">Loading...</span>
            </label>
        </div>
    </div>
    <div class="col-md-3">
        <div class="custom-control custom-checkbox image-checkbox">
            <input type="checkbox" class="custom-control-input" id="cs1d" data-key="acc_warranty">
            <label class="custom-control-label" for="cs1d">
                 <span class="h2con">Warrenty Card</span>
                 <span class="price-tag" data-price-for="acc_warranty">Loading...</span>
            </label>
        </div>
    </div>
     <div class="btn-group-checkbutton" role="group" aria-label="Basic example" style="text-align:center ; align-items:center">
            <button type="button" class="btn-baxt" onclick="goToTab('pills-condition-tab')">BACK</button>
            <button type="button" class="btn-baxt" onclick="goToEstimateTab()">See Estimate</button>
        </div>
    </div>
    </div>

    <div class="tab-pane fade" id="pills-estimate-samsung" role="tabpanel" aria-labelledby="pills-estimate-tab-samsung" tabindex="0">
        <div class="row justify-content-center text-center estimate-card-row" style="padding:30px 0; border:2px solid #0B1E3F; border-radius:30px; margin: 2% 20% 2% 20%; ">
            <div class="col-md-6">
                <h2 class="h2con" style="margin-bottom:10px;">Sell Now Upto</h2>
                <i class="fa-solid fa-sack-dollar"></i><h2  class="est-price fw-bold mt-4" id="estimatePriceDisplay">Calculating...</h2>
                <p class="text-muted fw-bold mt-4" id="estimateSummary" style="color:#0B1E3F;"></p>
                <p id="estimateNote" style="color:#797979; font-size:13px;"></p>
                <h6 style="margin-bottom:10px; color:red; font-weight:200;">Note: Your "Sell Now" price is determined by our experts upon device assessment.</h6>

            </div>
        </div>
         <div class="btn-group-checkbutton estimate-btn-group" role="group" aria-label="Basic example" style="text-align:center ; align-items:center">
          <a href="<?php echo htmlspecialchars($whatsapp_link); ?>" target="_blank" rel="noopener" class="btn-baxt btn-baxt-whatsapp"><i class="fab fa-whatsapp"></i> WhatsApp</a>
            <button type="button" class="btn-baxt btn-baxt-pickup" onclick="showFormModal()"><i class="fa-solid fa-truck-fast"></i> Book Free Pickup</button>
        </div>
    </div>
</div>
</section>