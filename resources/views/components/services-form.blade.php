<form id="serviceForm" class="p-3 text-start">
    <div class="mb-3">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
    </div>

    <div class="mb-3">
        <label for="company" class="form-label">Company Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="company" name="company" placeholder="Enter your company" required>
    </div>

    <div class="mb-3">
        <label for="contact" class="form-label">Contact Information <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="contact" name="contact" placeholder="Email or Phone" required>
    </div>

    <div class="mb-3">
        <label for="service1" class="form-label">Services <span class="text-danger">*</span></label>
        <div class="accordion" id="servicesAccordion">
            <div class="accordion-item p-0">
                <h2 class="accordion-header" id="servicesHeading">
                    <button class="accordion-button collapsed py-2 ps-3 lh-base" type="button" data-bs-toggle="collapse"
                        data-bs-target="#servicesCollapse" aria-expanded="false" aria-controls="servicesCollapse">
                        Select Services
                    </button>
                </h2>
                <div id="servicesCollapse" class="accordion-collapse collapse" aria-labelledby="servicesHeading"
                    data-bs-parent="#servicesAccordion">
                    <div class="accordion-body">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="services[]" value="Web Development"
                                id="service1">
                            <label class="form-check-label" for="service1">Web Development</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="services[]" value="SEO Optimization"
                                id="service2">
                            <label class="form-check-label" for="service2">SEO Optimization</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="services[]" value="Graphic Design"
                                id="service3">
                            <label class="form-check-label" for="service3">Graphic Design</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="services[]" value="Digital Marketing"
                                id="service4">
                            <label class="form-check-label" for="service4">Digital Marketing</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="services[]" value="App Development"
                                id="service5">
                            <label class="form-check-label" for="service5">App Development</label>
                        </div>
                        <div class="form-text text-muted">Please select at least one service.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <label for="message" class="form-label">Message</label>
        <textarea class="form-control" id="message" name="message" rows="5"
            placeholder="Have a message? Type here..."></textarea>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="consent" required>
            <label class="form-check-label" for="consent" data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-custom-class="custom-tooltip"
                data-bs-title="By checking this box and clicking Send, Gmail will open a new email draft with all your form details pre-filled for easy review and sending.">
                <span style="text-decoration: underline;  text-decoration-color: #0984e3 ;">
                    Send via Gmail
                </span>
            </label>
        </div>

        <button type="submit" class="btn view-more--secondary px-4">
            <i class="bi bi-send-fill me-1"></i> <span>Send</span>
        </button>
    </div>
</form>