<?php
declare(strict_types=1);
$pageTitle = "Contact Us";
require_once __DIR__ . '/includes/header.php';
?>

<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row g-5">
            <div class="col-lg-5">
                <h2 class="text-primary font-weight-bold mb-4">Get In Touch</h2>
                <p class="text-secondary mb-4">Have questions about registration approvals, credentials verification, or system setups? Contact our HR administration support desk.</p>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-phone"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted">Phone Line</h6>
                        <strong>+977 1 4400000</strong>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted">Email Inbox</h6>
                        <strong>contact@sovryxtech.com.np</strong>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-7">
                <div class="p-4 bg-light rounded-4 shadow-sm border">
                    <h4 class="text-primary mb-3">Send Us a Message</h4>
                    <form id="contactForm" onsubmit="event.preventDefault(); Swal.fire('Thank you', 'Your message has been sent to HR support.', 'success');">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Subject</label>
                                <input type="text" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message Text</label>
                                <textarea class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100 py-2">Submit Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
