var STANDALONE_STEP_HEADINGS = {
  2: 'Which <span>model</span> is your phone',
  3: 'How much <span>storage</span> does it have',
  4: "What's the <span>condition</span> of your phone",
  5: 'Any <span>accessories</span> included?',
  6: "Here's your <span>estimate</span>"
};

function updateStandaloneStepHeading(step){
  var text = STANDALONE_STEP_HEADINGS[step];
  if (!text) return;
  var $appleHeading = $('#apple-step-heading');
  var $samsungHeading = $('#samsung-step-heading');
  if ($appleHeading.length) $appleHeading.html(text);
  if ($samsungHeading.length) $samsungHeading.html(text);
}
function setTrackerStep(step){
  document.querySelectorAll('.step-tab').forEach(function(tab){
    var s = parseInt(tab.getAttribute('data-step'), 10);
    tab.classList.remove('active', 'done');
    tab.disabled = true;
    if (s < step){
      tab.classList.add('done');
      tab.disabled = false;     
    } else if (s === step){
      tab.classList.add('active');
      tab.disabled = false;
    }
  });
   updateStandaloneStepHeading(step);
}


var TRACKER_TAB_MAP = {
    //apple
  'pills-home-tab': 2,        
  'pills-profile-tab': 3,    
  'pills-contact-tab': 4,    
  'pills-disabled-tab': 5,    
  'pills-estimate-tab': 6,    
  // Samsung
  'pills-models-tab': 2,      
  'pills-storage-tab': 3,     
  'pills-condition-tab': 4,   
  'pills-accessories-tab': 5, 
  'pills-estimate-tab-samsung': 6  
};
$(document).on('shown.bs.tab', '[data-bs-toggle="pill"]', function(e){
  var step = TRACKER_TAB_MAP[e.target.id];
  if (step) setTrackerStep(step);
});
document.querySelectorAll('.step-tab').forEach(function(tab){
  tab.addEventListener('click', function(){
    if (tab.disabled) return;
    var step = parseInt(tab.getAttribute('data-step'), 10);
    if (step === 1){
      goBack();
      return;
    }
    var brand = (typeof selection !== 'undefined') ? selection.brand : null;
    var tabId = null;
    if (brand === 'Samsung'){
      tabId = { 2:'pills-models-tab', 3:'pills-storage-tab', 4:'pills-condition-tab', 5:'pills-accessories-tab', 6:'pills-estimate-tab-samsung' }[step];
    } else if (brand === 'Apple'){
      tabId = { 2:'pills-home-tab', 3:'pills-profile-tab', 4:'pills-contact-tab', 5:'pills-disabled-tab', 6:'pills-estimate-tab' }[step];
    }
    if (tabId) {
      if (step === 6) {
        goToEstimateTab();
      } else {
        goToTab(tabId);
      }
    }
  });
});

function switchSection(targetId) {
    var brands = document.getElementById("brands-section");
    var apple = document.getElementById("apple-section");
    var samsung = document.getElementById("samsung-section");

    brands.classList.add("fade-out");

    setTimeout(function () {
        brands.style.display = "none";

        apple.style.display = "none";
        samsung.style.display = "none";
        apple.classList.remove("fade-in");
        samsung.classList.remove("fade-in");

        var target = document.getElementById(targetId);
        target.style.display = "block";

        void target.offsetWidth;
        target.classList.add("fade-in");
    }, 300);
}

function showApple() {
    switchSection("apple-section");
    goToTab('pills-home-tab');
    setTrackerStep(2);
}

function showSamsung() {
    switchSection("samsung-section");
    goToTab('pills-models-tab');
    setTrackerStep(2);
}
function scrollToValuationTop() {
    var el = document.getElementById('valuation-step');
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function goToTab(tabButtonId) {
    var btn = document.getElementById(tabButtonId);
    if (btn) {
        var tab = bootstrap.Tab.getOrCreateInstance(btn);
        tab.show();
    }
}

var selection = {
    model_id: null,
    brand: null,
    model_name: null,
    storage: null,
    storageLabel: null,
    condition: null,
    pricing: null
};

function formatDelta(value) {
    value = parseFloat(value) || 0;
    if (value === 0) return 'No extra charge';
    return (value > 0 ? '+' : '') + 'AED ' + value;
}

function renderStorageCards(options, sectionSelector) {
    var $container = $(sectionSelector + ' .storage-container');
    $container.empty();

    if (!options || !options.length) {
        $container.append('<p class="storage-loading-note" style="text-align:center; width:100%; color:#797979c5; font-size:13px;">No storage options configured for this model yet.</p>');
        return;
    }

    options.forEach(function (opt) {
        var $card = $('<div></div>').addClass('storage-card')
            .attr('data-key', opt.id)
            .attr('data-label', opt.label);

        $('<h2></h2>').addClass('h2store').text(opt.label).appendTo($card);

        $container.append($card);
    });
}

function loadModelPricing(modelId, sectionSelector) {
    $(sectionSelector + ' .price-tag').text('Loading...');
    $(sectionSelector + ' .storage-container').html('<p class="storage-loading-note" style="text-align:center; width:100%; color:#797979c5; font-size:13px;">Loading storage options...</p>');

    fetch('get-model-pricing.php?model_id=' + encodeURIComponent(modelId))
        .then(function (res) { return res.json(); })
        .then(function (data) {
            selection.pricing = data;
            $(sectionSelector + ' .price-tag').each(function () {
                var key = $(this).data('price-for');
                if (key && data.hasOwnProperty(key)) {
                    $(this).text(formatDelta(data[key]));
                }
            });
            renderStorageCards(data.storage_options, sectionSelector);
        })
        .catch(function () {
            $(sectionSelector + ' .price-tag').text('Price unavailable');
            $(sectionSelector + ' .storage-container').html('<p class="storage-loading-note" style="text-align:center; width:100%; color:#c0392b; font-size:13px;">Could not load storage options.</p>');
        });
}

var selectedDevicePhotos = [];
var devicePhotoIdCounter = 0;
var MAX_DEVICE_PHOTOS = 4;

function renderDevicePhotoPreviews() {
    var $preview = $('#devicePhotosPreview');
    $preview.empty();
    selectedDevicePhotos.forEach(function (item) {
        var $thumb = $('<div></div>').css({
            position: 'relative',
            width: '46px',
            height: '46px'
        });
        $('<img>').attr('src', item.url).css({
            width: '100%',
            height: '100%',
            objectFit: 'cover',
            borderRadius: '6px',
            border: '1px solid #dcdcdc'
        }).appendTo($thumb);
        $('<button type="button">&times;</button>').addClass('remove-device-photo')
            .attr('data-id', item.id)
            .attr('title', 'Remove photo')
            .css({
                position: 'absolute',
                top: '-6px',
                right: '-6px',
                width: '16px',
                height: '16px',
                lineHeight: '14px',
                padding: '0',
                borderRadius: '50%',
                border: 'none',
                background: '#dc3545',
                color: '#fff',
                fontSize: '11px',
                cursor: 'pointer'
            }).appendTo($thumb);
        $preview.append($thumb);
    });
}

function resetDevicePhotos() {
    selectedDevicePhotos.forEach(function (item) { URL.revokeObjectURL(item.url); });
    selectedDevicePhotos = [];
    renderDevicePhotoPreviews();
}

$(document).on('change', '#devicePhotosInput', function () {
    var newFiles = Array.prototype.slice.call(this.files);
    var remainingSlots = MAX_DEVICE_PHOTOS - selectedDevicePhotos.length;

    newFiles.slice(0, remainingSlots).forEach(function (file) {
        devicePhotoIdCounter++;
        selectedDevicePhotos.push({
            id: devicePhotoIdCounter,
            file: file,
            url: URL.createObjectURL(file)
        });
    });

    renderDevicePhotoPreviews();
    this.value = '';
});

$(document).on('click', '.remove-device-photo', function () {
    var id = parseInt($(this).attr('data-id'), 10);
    var item = selectedDevicePhotos.find(function (p) { return p.id === id; });
    if (item) {
        URL.revokeObjectURL(item.url);
    }
    selectedDevicePhotos = selectedDevicePhotos.filter(function (p) { return p.id !== id; });
    renderDevicePhotoPreviews();
});

$(document).ready(function () {

    $(document).on('click', '#pills-home .model-card', function () {
        $(this).closest('.row').find('.model-card').removeClass('selected-card');
        $(this).addClass('selected-card');
        selection.model_id = $(this).data('model-id');
        selection.model_name = $(this).data('model-name');
        selection.brand = 'Apple';
        selection.storage = null;
        selection.storageLabel = null;
        loadModelPricing(selection.model_id, '#apple-section');
        goToTab('pills-profile-tab');
        scrollToValuationTop();
    });

    $(document).on('click', '#pills-profile .storage-card', function () {
        $(this).siblings('.storage-card').removeClass('selected-card');
        $(this).addClass('selected-card');
        selection.storage = $(this).data('key');
        selection.storageLabel = $(this).data('label');
        goToTab('pills-contact-tab');
    });

    $(document).on('click', '#pills-contact .condi-card', function () {
        $(this).siblings('.condi-card').removeClass('selected-card');
        $(this).addClass('selected-card');
        selection.condition = $(this).data('key');
        goToTab('pills-disabled-tab');
    });

    $(document).on('click', '#pills-models .model-card', function () {
        $(this).closest('.row').find('.model-card').removeClass('selected-card');
        $(this).addClass('selected-card');
        selection.model_id = $(this).data('model-id');
        selection.model_name = $(this).data('model-name');
        selection.brand = 'Samsung';
        selection.storage = null;
        selection.storageLabel = null;
        loadModelPricing(selection.model_id, '#samsung-section');
        goToTab('pills-storage-tab');
        scrollToValuationTop();
    });

    $(document).on('click', '#pills-storage .storage-card', function () {
        $(this).siblings('.storage-card').removeClass('selected-card');
        $(this).addClass('selected-card');
        selection.storage = $(this).data('key');
        selection.storageLabel = $(this).data('label');
        goToTab('pills-condition-tab');
    });

    $(document).on('click', '#pills-condition .condi-card', function () {
        $(this).siblings('.condi-card').removeClass('selected-card');
        $(this).addClass('selected-card');
        selection.condition = $(this).data('key');
        goToTab('pills-accessories-tab');
    });


});

function goBack() {
    var brands = document.getElementById("brands-section");
    var apple = document.getElementById("apple-section");
    var samsung = document.getElementById("samsung-section");
    if (!brands) {
        return;
    }

    apple.classList.remove("fade-in");
    samsung.classList.remove("fade-in");

    setTimeout(function () {
        apple.style.display = "none";
        samsung.style.display = "none";

        brands.style.display = "block";
        void brands.offsetWidth;
        brands.classList.remove("fade-out");
        setTrackerStep(1);
    }, 300);
}
function goToEstimateTab() {
    var estimateTabId = selection.brand === 'Samsung' ? 'pills-estimate-tab-samsung' : 'pills-estimate-tab';
    goToTab(estimateTabId);
    calculateEstimate();
}

function calculateEstimate() {
    var sectionSelector = selection.brand === 'Samsung' ? '#samsung-section' : '#apple-section';
    var $section = $(sectionSelector);
    var $display = $section.find('#estimatePriceDisplay');
    var $note = $section.find('#estimateNote');
    var $summary = $section.find('#estimateSummary');

    if (!selection.model_id || !selection.storage || !selection.condition) {
        $display.text('--');
        $note.text('Please complete model, storage and condition first.');
        $summary.text('');
        return;
    }

    $display.text('Calculating...');
    $note.text('');

    var accessories = [];
    var accessoryLabels = [];
    $section.find('.custom-control-input:checked').each(function () {
        var key = $(this).data('key');
        if (key) {
            accessories.push(key);
            accessoryLabels.push($(this).siblings('label').find('h2').text().trim());
        }
    });

    var storageLabel = selection.storageLabel || '';
    var conditionLabel = selection.condition.replace('condition_', '');
    conditionLabel = conditionLabel.charAt(0).toUpperCase() + conditionLabel.slice(1);

    var summaryText = 'Based on: ' + selection.model_name + ' ' + storageLabel + ', ' + conditionLabel + ' condition';
    if (accessoryLabels.length) {
        summaryText += ' + ' + accessoryLabels.join(', ');
    }
    $summary.text(summaryText);

    var params = new URLSearchParams();
    params.append('model_id', selection.model_id);
    params.append('storage', selection.storage);
    params.append('condition', selection.condition);
    accessories.forEach(function (a) { params.append('accessories[]', a); });

    fetch('get-price.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString()
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        selection.price = data.price || 0;
        $display.text('AED ' + selection.price);
        $note.text(data.note || '');
    })
    .catch(function () {
        $display.text('--');
        $note.text('Could not calculate estimate. Please try again.');
    });
}

function showFormModal() {
   
    var modal = new bootstrap.Modal(document.getElementById('myFormModal'));
    modal.show();
}

function submitFormAndProceed() {
     
    var form = document.getElementById('finalForm');
    var name = form.elements['name'].value.trim();
    var phone = form.elements['phone'].value.trim();
    var email = form.elements['email'].value.trim();
    var address = form.elements['address'].value.trim();
    var alertBox = document.getElementById('finalFormAlert');
    var submitBtn = document.getElementById('finalFormSubmitBtn');

    alertBox.style.display = 'none';

    if (!selection.model_id || !selection.storage || !selection.condition) {
        alertBox.className = 'alert alert-danger';
        alertBox.textContent = 'Please pick a model, storage and condition before submitting.';
        alertBox.style.display = 'block';
        return;
    }
    if (!name || !phone) {
        alertBox.className = 'alert alert-danger';
        alertBox.textContent = 'Please enter your name and phone number.';
        alertBox.style.display = 'block';
        return;
    }

    var accessories = [];
    $('#pills-disabled .custom-control-input:checked, #pills-accessories .custom-control-input:checked').each(function () {
        accessories.push($(this).data('key'));
    });

    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';

    var priceParams = new URLSearchParams();
    priceParams.append('model_id', selection.model_id);
    priceParams.append('storage', selection.storage);
    priceParams.append('condition', selection.condition);
    accessories.forEach(function (a) { priceParams.append('accessories[]', a); });

    fetch('get-price.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: priceParams.toString()
    })
    .then(function (res) { return res.json(); })
    .then(function (priceData) {
        var price = priceData.price || 0;

        // Switched to FormData (instead of URLSearchParams) so uploaded device photos ride along with the request
        var leadFormData = new FormData();
        leadFormData.append('model_id', selection.model_id);
        leadFormData.append('brand', selection.brand);
        leadFormData.append('model_name', selection.model_name);
        leadFormData.append('storage', selection.storageLabel || selection.storage);
        leadFormData.append('condition', selection.condition);
        leadFormData.append('accessories', accessories.join(', '));
        leadFormData.append('price', price);
        leadFormData.append('name', name);
        leadFormData.append('phone', phone);
        leadFormData.append('email', email);
        leadFormData.append('address', address);

        var photoInput = form.querySelector('input[name="device_photos[]"]');
        if (photoInput) {
            selectedDevicePhotos.forEach(function (item) {
                leadFormData.append('device_photos[]', item.file);
            });
        }

        console.log(leadFormData);
        return fetch('save-leads.php', {
            method: 'POST',
            body: leadFormData
        }).then(function (res) { return res.json(); });
    })
    .then(function (data) {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit';

        alertBox.className = data.success ? 'alert alert-success' : 'alert alert-danger';
        alertBox.textContent = data.success
            ? 'Thanks! Your estimated offer is AED ' + data.price + '. We will contact you shortly.'
            : data.message;
        alertBox.style.display = 'block';

        if (data.success) {
            setTimeout(function () {
                var modalEl = document.getElementById('myFormModal');
                var modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();
                form.reset();
                resetDevicePhotos();
                alertBox.style.display = 'none';
            }, 2200);
        }
    })
    .catch(function () {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit';
        alertBox.className = 'alert alert-danger';
        alertBox.textContent = 'Network error. Please try again.';
        alertBox.style.display = 'block';
    });
}
function submitPickupForm(event) {
    event.preventDefault();
 
    var form = document.getElementById('pickupForm');
    var alertBox = document.getElementById('pickupFormAlert');
    var submitBtn = document.getElementById('pickupSubmitBtn');
    var name = form.elements['name'].value.trim();
    var phone = form.elements['phone'].value.trim();
 
    alertBox.style.display = 'none';
 
    if (!name || !phone) {
        alertBox.style.background = '#fdeaea';
        alertBox.style.color = '#c0392b';
        alertBox.textContent = 'Please enter your name and phone number.';
        alertBox.style.display = 'block';
        return false;
    }
 
    submitBtn.disabled = true;
    submitBtn.textContent = 'Booking...';
 
    var params = new URLSearchParams();
    params.append('name', name);
    params.append('phone', phone);
 
    fetch('book-pickup.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString()
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Book Free Pickup';
 
        alertBox.style.background = data.success ? '#e9f9ee' : '#fdeaea';
        alertBox.style.color = data.success ? '#1e7e34' : '#c0392b';
        alertBox.textContent = data.message;
        alertBox.style.display = 'block';
 
        if (data.success) {
            form.reset();
        }
          setTimeout(function () {
            alertBox.style.display = 'none';
        }, 3000);
    })
    .catch(function () {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Book Free Pickup';
        alertBox.style.background = '#fdeaea';
        alertBox.style.color = '#c0392b';
        alertBox.textContent = 'Network error. Please try again.';
        alertBox.style.display = 'block';
    });
 
    return false;
}
function submitContactForm(event) {
    event.preventDefault();

    var form = document.getElementById('contactForm');
    var alertBox = document.getElementById('contactFormAlert');
    var submitBtn = document.getElementById('contactSubmitBtn');
    var name = form.elements['name'].value.trim();
    var email = form.elements['email'].value.trim();
    var message = form.elements['message'].value.trim();

    alertBox.style.display = 'none';

    if (!name || !email || !message) {
        alertBox.style.background = '#fdeaea';
        alertBox.style.color = '#c0392b';
        alertBox.textContent = 'Please fill in your name, email and message.';
        alertBox.style.display = 'block';
        return false;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Sending...';

    var params = new URLSearchParams();
    params.append('name', name);
    params.append('email', email);
    params.append('phone', form.elements['phone'].value.trim());
    params.append('subject', form.elements['subject'].value.trim());
    params.append('message', message);

    fetch('save-contact.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString()
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Send Message <i class="fa-solid fa-paper-plane"></i>';

        alertBox.style.background = data.success ? '#e9f9ee' : '#fdeaea';
        alertBox.style.color = data.success ? '#1e7e34' : '#c0392b';
        alertBox.textContent = data.message;
        alertBox.style.display = 'block';

        if (data.success) {
            form.reset();
        }
        setTimeout(function () {
            alertBox.style.display = 'none';
        }, 4000);
    })
    .catch(function () {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Send Message <i class="fa-solid fa-paper-plane"></i>';
        alertBox.style.background = '#fdeaea';
        alertBox.style.color = '#c0392b';
        alertBox.textContent = 'Network error. Please try again.';
        alertBox.style.display = 'block';
    });

    return false;
}

function showAppleCatalog(e){
    if (e) e.preventDefault();
    var catalogWrap = document.getElementById('apple-catalog-wrap');
    if (!catalogWrap) return;

    catalogWrap.classList.add('is-active');
    catalogWrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function filterAppleSeries(seriesPrefix){
    var catalogWrap = document.getElementById('apple-catalog-wrap');
    if (!catalogWrap) return;

    catalogWrap.classList.add('is-active');

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
function showSamsungCatalog(e){
    if (e) e.preventDefault();
    var catalogWrap = document.getElementById('samsung-catalog-wrap');
    if (!catalogWrap) return;

    catalogWrap.classList.add('is-active');
    catalogWrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
}