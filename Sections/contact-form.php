<div class="row-lg-6 contect-form">
    <div class="div-form">

        <h1 class="fw-bold mb-3 h1f" style="color:#0B1E3B;">
            Send Us a Message
        </h1>

        <p class="text-secondary mb-5 p-f">
            Fill out the form below and we'll get back to you within 24 hours.
        </p>

        <div id="contactFormAlert" style="display:none; font-size:13px; padding:10px 14px; border-radius:8px; margin-bottom:16px;"></div>

        <form id="contactForm" onsubmit="return submitContactForm(event);">
            <div class="row">
                <div class="col-md-6 mb-4 button-form">
                    <label class="form-label fw-semibold" style="color:#0B1E3B;">
                        <i class="fa-solid fa-user"></i>
                        Full Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control form-control-lg form-input"
                        placeholder="John Doe" required>
                </div>

                <div class="col-md-6 mb-4 button-form">
                    <label class="form-label fw-semibold"  style="color:#0B1E3B;">
                        <i class="fa-solid fa-envelope"></i>
                        Email Address
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control form-control-lg form-input"
                        placeholder="john@example.com" required>
                </div>

                <div class="col-md-6 mb-4 button-form">
                    <label class="form-label fw-semibold"  style="color:#0B1E3B;">
                        <i class="fa-solid fa-phone"></i>
                        Phone Number
                    </label>

                    <input
                        type="text"
                        name="phone"
                        class="form-control form-control-lg form-input"
                        placeholder="+971 50 555 6779">
                </div>

                <div class="col-md-6 mb-4 button-form">
                    <label class="form-label fw-semibold" style="color:#0B1E3B;">
                        <i class="fa-solid fa-tag"></i>
                        Subject
                    </label>
                    <input
                        type="text"
                        name="subject"
                        class="form-control form-control-lg form-input"
                        placeholder="How can we help?">
                </div>

                <div class="col-12 mb-4 button-form">

                    <label class="form-label fw-semibold" style="color:#0B1E3B;">
                        <i class="fa-solid fa-comment-dots"></i>
                        Your Message
                    </label>
                    <textarea
                        name="message"
                        class="form-control"
                        rows="6"
                        placeholder="Tell us more about your inquiry..." required></textarea>
                </div>

                <div class="col-12 mt-3 button-form">

                    <button type="submit" class="btn btn-lg w-100" id="contactSubmitBtn">
                        Send Message
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>