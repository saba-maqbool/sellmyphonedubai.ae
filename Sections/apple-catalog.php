<section class="series-catalog-section" id="series-catalog-section">
    <div class="section-header">
        <span class="section-tag"><i class="fa-brands fa-apple"></i> BROWSE BY SERIES</span>
        <h2 class="section-title">Shop by <span>iPhone Series</span></h2>
        <p class="section-subtitle">Pick your series to jump straight to matching models and get an instant price</p>
    </div>

    <div class="series-grid">
        <div class="series-card">
            <div class="series-card-img-wrap">
                <img src="imgs/iphone 17 series.webp" alt="iPhone 17 Series" class="series-card-img">
            </div>
            <h3 class="series-card-title">iPhone 17 Series</h3>
            <button type="button" class="series-view-all-btn" onclick="filterAppleSeries('iphone 17')">
                View All <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>

        <div class="series-card">
            <div class="series-card-img-wrap">
                <img src="imgs/model_6a73126893f51.webp" alt="iPhone 16 Series" class="series-card-img">
            </div>
            <h3 class="series-card-title">iPhone 16 Series</h3>
            <button type="button" class="series-view-all-btn" onclick="filterAppleSeries('iphone 16')">
                View All <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
        
        <div class="series-card">
            <div class="series-card-img-wrap">
                <img src="imgs/model_6a73126893f51.webp" alt="iPhone 15 Series" class="series-card-img">
            </div>
            <h3 class="series-card-title">iPhone 15 Series</h3>
            <button type="button" class="series-view-all-btn" onclick="filterAppleSeries('iphone 16')">
                View All <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
        
        <div class="series-card">
            <div class="series-card-img-wrap">
                <img src="imgs/model_6a73126893f51.webp" alt="iPhone 14 Series" class="series-card-img">
            </div>
            <h3 class="series-card-title">iPhone 14 Series</h3>
            <button type="button" class="series-view-all-btn" onclick="filterAppleSeries('iphone 16')">
                View All <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
         <div class="series-card">
            <div class="series-card-img-wrap">
                <img src="imgs/model_6a73126893f51.webp" alt="iPhone 13 Series" class="series-card-img">
            </div>
            <h3 class="series-card-title">iPhone 13 Series</h3>
            <button type="button" class="series-view-all-btn" onclick="filterAppleSeries('iphone 16')">
                View All <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>
</section>

<script>
function filterAppleSeries(seriesPrefix){
    var catalogWrap = document.getElementById('apple-catalog-wrap');
    if (!catalogWrap) return;

    catalogWrap.style.display = 'block';

    var cards = document.querySelectorAll('#pills-home .model-card');
    cards.forEach(function(card){
        var name = (card.getAttribute('data-model-name') || '').toLowerCase();
        var col = card.closest('.col');
        if (name.indexOf(seriesPrefix.toLowerCase()) === 0) {
            if (col) col.style.display = '';
        } else {
            if (col) col.style.display = 'none';
        }
    });

    catalogWrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
</script>