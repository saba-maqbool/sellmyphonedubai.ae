<section class="apple-section" id="apple-section">
       <div class="navbar-pills">
         <ul class="nav nav-pills mb-3 legacy-pill-nav" id="pills-tab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Models</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Storage</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Condition</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="pills-disabled-tab" data-bs-toggle="pill" data-bs-target="#pills-disabled" type="button" role="tab" aria-controls="pills-disabled" aria-selected="false">Accessories</button>
  </li>
  <li class="nav-item" role="presentation" style="display:none;">
    <button class="nav-link" id="pills-estimate-tab" data-bs-toggle="pill" data-bs-target="#pills-estimate" type="button" role="tab" aria-controls="pills-estimate" aria-selected="false">Estimate</button>
  </li>
</ul>
<div class="tab-content" id="pills-tabContent">
  <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">

  <?php
  require_once(__DIR__ . "/../admin/include/db-connect.php");
  $brand_filter = "Apple";
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
                <img src="<?php echo htmlspecialchars($m['image']); ?>" class="model-card-img" alt="<?php echo htmlspecialchars($m['model_name']); ?>" loading="lazy">
            </span>
            <span class="model-card-title"><?php echo htmlspecialchars($m['model_name']); ?></span>
        </button>
    </div>
    <?php endwhile; ?>

</div>

<div class="btn-group" role="group" aria-label="Basic example">
  <button type="button" class="btn-baxt" onclick="goBack()">BACK</button>
  <button type="button" class="btn-baxt" onclick="goToTab('pills-profile-tab')">NEXT</button>
</div>

</div>

          <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
         <div class="storage-container" id="apple-storage-container">
            <p class="text-muted">Select a model first to see storage options.</p>
        </div>
        <div class="btn-group-button" role="group" aria-label="Basic example">
            <button type="button" class="btn-baxt" onclick="goToTab('pills-home-tab')">BACK</button>
            <button type="button" class="btn-baxt" onclick="goToTab('pills-contact-tab')">NEXT</button>
         </div>

  </div>
  <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">
     <div class="condi-container">
            <div class="condi-card" data-key="condition_flawless">
                <h2 class="h2con">Flawless</h2>
                <p>Looks brand new, no scratches or dents.</p>
            </div>

            <div class="condi-card" data-key="condition_good">
                <h2 class="h2con">Good</h2>
                 <p>Light signs of wear, fully working.</p>
            </div>

            <div class="condi-card" data-key="condition_fair">
                <h2 class="h2con">Fair/Cracked</h2>
                 <p>Heavy wear, cracked screen but functional.</p>
            </div>
        </div>
        <div class="btn-group-button" role="group" aria-label="Basic example" style="text-align:center ; align-items:center">
            <button type="button" class="btn-baxt" onclick="goToTab('pills-profile-tab')">BACK</button>
            <button type="button" class="btn-baxt" onclick="goToTab('pills-disabled-tab')">NEXT</button>
        </div>

  </div>
  <div class="tab-pane fade" id="pills-disabled" role="tabpanel" aria-labelledby="pills-disabled-tab" tabindex="0">
    <div class="row">
   <div class="col-md-3">
        <div class="custom-control custom-checkbox image-checkbox">
            <input type="checkbox" class="custom-control-input" id="ck1a" data-key="acc_charger">
            <label class="custom-control-label" for="ck1a">
                 <h2 class="h2con">Charger</h2>
                 <span class="price-tag" data-price-for="acc_charger">Loading...</span>
            </label>
        </div>
    </div>
    <div class="col-md-3">
        <div class="custom-control custom-checkbox image-checkbox">
            <input type="checkbox" class="custom-control-input" id="ck1b" data-key="acc_box">
            <label class="custom-control-label" for="ck1b">
                <h2 class="h2con">Box</h2>
                <span class="price-tag" data-price-for="acc_box">Loading...</span>
            </label>
        </div>
    </div>
    <div class="col-md-3">
        <div class="custom-control custom-checkbox image-checkbox">
            <input type="checkbox" class="custom-control-input" id="ck1c" data-key="acc_earbuds">
            <label class="custom-control-label" for="ck1c">
                <h2 class="h2con">Earbuds</h2>
                <span class="price-tag" data-price-for="acc_earbuds">Loading...</span>
            </label>
        </div>
    </div>
    <div class="col-md-3">
        <div class="custom-control custom-checkbox image-checkbox">
            <input type="checkbox" class="custom-control-input" id="ck1d" data-key="acc_warranty">
            <label class="custom-control-label" for="ck1d">
                 <h2 class="h2con">Warrenty Card</h2>
                 <span class="price-tag" data-price-for="acc_warranty">Loading...</span>
            </label>
        </div>
    </div>
     <div class="btn-group-checkbutton" role="group" aria-label="Basic example" style="text-align:center ; align-items:center">
            <button type="button" class="btn-baxt" onclick="goToTab('pills-profile-tab')">BACK</button>
            <button type="button" class="btn-baxt" onclick="goToEstimateTab()">See Estimate</button>
        </div>
    </div>
    </div>
    <div class="tab-pane fade" id="pills-estimate" role="tabpanel" aria-labelledby="pills-estimate-tab" tabindex="0">
        <div class="row justify-content-center text-center estimate-card-row" style="padding:30px 0; border:2px solid #0B1E3F; border-radius:30px; margin: 2% 20% 2% 20%; ">
            <div class="col-md-6">
                <h2 class="h2con" style="margin-bottom:10px;">Sell Now Upto</h2>
                <i class="fa-solid fa-sack-dollar"></i><h1  class="est-price fw-bold mt-4" id="estimatePriceDisplay">Calculating...</h1>
                <p class="text-muted fw-bold mt-4" id="estimateSummary"></p>
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