<?php
declare(strict_types=1);
$pageTitle = "Online Registration Wizard";
require_once __DIR__ . '/includes/header.php';
?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card-custom p-4 p-md-5">
                    <div class="text-center mb-5">
                        <h2 class="text-primary font-weight-bold">Employee Self-Registration Portal</h2>
                        <p class="text-secondary">Please fill in the 5-step wizard to register your profile.</p>
                    </div>

                    <!-- Wizard Progress Header -->
                    <div class="wizard-steps mb-5">
                        <div class="wizard-step-node active" data-step="1" data-bs-toggle="tooltip" title="Personal Info">1</div>
                        <div class="wizard-step-node" data-step="2" data-bs-toggle="tooltip" title="Contact Details">2</div>
                        <div class="wizard-step-node" data-step="3" data-bs-toggle="tooltip" title="Background">3</div>
                        <div class="wizard-step-node" data-step="4" data-bs-toggle="tooltip" title="Documents Upload">4</div>
                        <div class="wizard-step-node" data-step="5" data-bs-toggle="tooltip" title="Account Setup">5</div>
                    </div>

                    <!-- Multi-step Form -->
                    <form id="registrationForm" action="<?= get_base_url() ?>/api/register_submit.php" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <!-- Step 1: Personal Info -->
                        <div class="wizard-step-content" data-step="1">
                            <h4 class="mb-4 text-primary"><i class="fa-solid fa-user me-2"></i>Step 1: Personal Information</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="form-control" placeholder="John Doe" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="dob" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select name="gender" class="form-select" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Blood Group</label>
                                    <input type="text" name="blood_group" class="form-control" placeholder="O+">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nationality <span class="text-danger">*</span></label>
                                    <input type="text" name="nationality" class="form-control" placeholder="Nepali" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Marital Status</label>
                                    <select name="marital_status" class="form-select">
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Divorced">Divorced</option>
                                        <option value="Widowed">Widowed</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Contact Info -->
                        <div class="wizard-step-content d-none" data-step="2">
                            <h4 class="mb-4 text-primary"><i class="fa-solid fa-address-book me-2"></i>Step 2: Contact & Emergency Details</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" class="form-control" placeholder="+977-9800000000" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" placeholder="john.doe@example.com" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Residential Address <span class="text-danger">*</span></label>
                                    <input type="text" name="address" class="form-control" placeholder="Baneshwor, Kathmandu" required>
                                </div>
                                
                                <h5 class="mt-4 mb-2 text-secondary">Emergency Contact Details</h5>
                                <div class="col-md-4">
                                    <label class="form-label">Contact Name <span class="text-danger">*</span></label>
                                    <input type="text" name="emergency_name" class="form-control" placeholder="Jane Doe" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Relationship <span class="text-danger">*</span></label>
                                    <input type="text" name="emergency_relation" class="form-control" placeholder="Sister" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Emergency Phone <span class="text-danger">*</span></label>
                                    <input type="tel" name="emergency_phone" class="form-control" placeholder="+977-9800000001" required>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Academic / Experience Background -->
                        <div class="wizard-step-content d-none" data-step="3">
                            <h4 class="mb-4 text-primary"><i class="fa-solid fa-graduation-cap me-2"></i>Step 3: Background Details</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Highest Academic Degree</label>
                                    <input type="text" name="highest_degree" class="form-control" placeholder="Bachelor in Computer Science">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Academic Institution</label>
                                    <input type="text" name="institution" class="form-control" placeholder="Tribhuvan University">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Professional Experience (Summary)</label>
                                    <textarea name="experience_summary" class="form-control" rows="4" placeholder="Briefly highlight past roles, technologies used, and project experience..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Documents Upload -->
                        <div class="wizard-step-content d-none" data-step="4">
                            <h4 class="mb-4 text-primary"><i class="fa-solid fa-folder-open me-2"></i>Step 4: Document Uploads</h4>
                            <p class="text-muted mb-4 small">Allowed formats: PDF, DOCX, JPG, PNG. Max size: 5MB per file.</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Passport Size Photo <span class="text-danger">*</span></label>
                                    <input type="file" name="profile_photo" class="form-control" accept="image/png, image/jpeg" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Citizenship / National ID <span class="text-danger">*</span></label>
                                    <input type="file" name="citizenship" class="form-control" accept="application/pdf, image/png, image/jpeg" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">CV / Resume <span class="text-danger">*</span></label>
                                    <input type="file" name="cv" class="form-control" accept="application/pdf, application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Academic Certificates <span class="text-danger">*</span></label>
                                    <input type="file" name="certificates" class="form-control" accept="application/pdf" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Police Clearance Certificate <span class="text-danger">*</span></label>
                                    <input type="file" name="police_clearance" class="form-control" accept="application/pdf, image/png, image/jpeg" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">PAN Card (Optional)</label>
                                    <input type="file" name="pan_card" class="form-control" accept="application/pdf, image/png, image/jpeg">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Appointment Letter (Optional)</label>
                                    <input type="file" name="appointment_letter" class="form-control" accept="application/pdf">
                                </div>
                            </div>
                        </div>

                        <!-- Step 5: Credentials & Captcha -->
                        <div class="wizard-step-content d-none" data-step="5">
                            <h4 class="mb-4 text-primary"><i class="fa-solid fa-key me-2"></i>Step 5: Account Credentials & CAPTCHA</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" class="form-control" placeholder="john.doe" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                                </div>
                                
                                <div class="col-12 mt-4 captcha-container">
                                    <label class="form-label">Security Check (CAPTCHA) <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= get_base_url() ?>/api/auth_captcha.php" alt="CAPTCHA" class="captcha-img rounded-3 border">
                                        <button type="button" class="btn btn-outline-secondary refresh-captcha"><i class="fa-solid fa-rotate"></i> Refresh</button>
                                        <input type="text" name="captcha_phrase" class="form-control w-50" placeholder="Enter code" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                            <button type="button" class="btn btn-outline-secondary btn-prev d-none"><i class="fa-solid fa-arrow-left me-2"></i>Previous</button>
                            <button type="button" class="btn btn-primary btn-next ms-auto">Next<i class="fa-solid fa-arrow-right ms-2"></i></button>
                            <button type="submit" class="btn btn-secondary-custom btn-submit ms-auto d-none"><i class="fa-solid fa-check-double me-2"></i>Submit Application</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Initialize Wizard Javascript -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    initRegistrationWizard('#registrationForm');
    
    // Setup form submit via AJAX
    setupAjaxForm('#registrationForm', function(response) {
        // Redirect to index page on success
        window.location.href = '<?= get_base_url() ?>/index.php';
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
