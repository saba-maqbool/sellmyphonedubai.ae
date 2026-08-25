<div class="modal fade" id="myFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enter details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="finalFormAlert" class="alert" style="display:none; border-radius:10px;"></div>
                <form id="finalForm">
                    <div class="d-flex gap-3">
                        <div class="mb-3 w-50 ">
                        <label class="form-label"><i class="fa-solid fa-user"></i><b>Full Name</b></label>
                        <input type="text" class="form-control" name="name" placeholder="John Doe">
                    </div>     
                    <div class="mb-3 w-50">
                        <label class="form-label"><i class="fa-brands fa-whatsapp"></i><b>Whatsapp Number</b></label>
                        <input type="text" class="form-control" name="phone" placeholder="+971 50 555 6779">
                    </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><i class="fa-solid fa-envelope"></i><b>Email</b></label>
                        <input type="email" class="form-control" name="email" placeholder="john@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa-solid fa-location-dot"></i><b>Address</b></label>
                        <input type="text" class="form-control" name="address" placeholder="Shop 22, Deira City Centre, Deira, Dubai">
                    </div>
                    <div class="mb-3">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Photos of your phone (optional, up to 4)</label>
                    <input type="file" id="devicePhotosInput" name="device_photos[]" class="form-control" accept="image/*" multiple>
                    <div id="devicePhotosPreview" style="display:flex; flex-wrap:wrap; gap:6px; margin-top:8px;"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-baxt" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-baxt" id="finalFormSubmitBtn" onclick="submitFormAndProceed()">Submit</button>
            </div>
        </div>
    </div>
</div>
