<?php
declare(strict_types=1);

$pageTitle = "Document Vault";
require_once __DIR__ . '/../../includes/dashboard_header.php';
require_once __DIR__ . '/../../src/Controller/EmployeeController.php';

$currentUser = auth_user();
$empController = new \Src\Controller\EmployeeController();
$documents = $empController->getDocuments((int)$currentUser['employee_id']);
?>

<div class="row g-4">
    <!-- List of Current Documents Grid -->
    <div class="col-lg-8">
        <h5 class="text-primary mb-4 fw-bold" style="font-family: 'Poppins', sans-serif;">
            <i class="fa-solid fa-file-shield me-2"></i>My Uploaded Documents
        </h5>
        
        <div class="row g-3">
            <?php if (empty($documents)): ?>
                <div class="col-12">
                    <div class="card-modern border-0 text-center p-5 bg-white shadow-sm">
                        <i class="fa-solid fa-folder-open text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                        <h5 class="text-secondary fw-bold">No documents found</h5>
                        <p class="text-muted small">Your document vault is currently empty.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($documents as $doc): ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="card-modern shadow-sm border-0 h-100 d-flex flex-column bg-white">
                            <div class="p-4 flex-grow-1 text-center position-relative">
                                <?php if ($doc['status'] === 'Verified'): ?>
                                    <span class="badge bg-success position-absolute top-0 end-0 m-3 shadow-sm rounded-pill"><i class="fa-solid fa-check"></i></span>
                                <?php elseif ($doc['status'] === 'Pending'): ?>
                                    <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-3 shadow-sm rounded-pill"><i class="fa-solid fa-clock"></i></span>
                                <?php else: ?>
                                    <span class="badge bg-danger position-absolute top-0 end-0 m-3 shadow-sm rounded-pill"><i class="fa-solid fa-xmark"></i></span>
                                <?php endif; ?>
                                
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <?php
                                    $icon = 'fa-file';
                                    if (strpos(strtolower($doc['document_type']), 'certificate') !== false) $icon = 'fa-certificate';
                                    if (strpos(strtolower($doc['document_type']), 'resume') !== false) $icon = 'fa-file-lines';
                                    if (strpos(strtolower($doc['document_type']), 'citizenship') !== false) $icon = 'fa-id-card';
                                    ?>
                                    <i class="fa-solid <?= $icon ?>" style="font-size: 1.8rem;"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1" style="font-family: 'Inter', sans-serif;"><?= htmlspecialchars($doc['document_type']) ?></h6>
                                <p class="text-muted small mb-0 text-truncate" style="font-size: 0.75rem;"><?= htmlspecialchars(basename($doc['file_path'])) ?></p>
                            </div>
                            
                            <div class="bg-light p-2 border-top d-flex justify-content-between align-items-center" style="border-radius: 0 0 20px 20px;">
                                <a href="<?= get_base_url() . '/' . $doc['file_path'] ?>" target="_blank" class="btn btn-sm btn-link text-decoration-none text-primary fw-bold w-50 border-end">
                                    <i class="fa-solid fa-eye me-1"></i> View
                                </a>
                                <a href="<?= get_base_url() . '/' . $doc['file_path'] ?>" download class="btn btn-sm btn-link text-decoration-none text-dark fw-bold w-50">
                                    <i class="fa-solid fa-download me-1"></i> Save
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Upload Additional Document Form -->
    <div class="col-lg-4">
        <div class="card-modern shadow-lg border-0 p-4 bg-white sticky-top" style="top: 20px;">
            <h5 class="text-dark mb-4 fw-bold" style="font-family: 'Poppins', sans-serif;"><i class="fa-solid fa-cloud-arrow-up text-primary me-2"></i>Upload Record</h5>
            
            <form id="docUploadForm" action="<?= get_base_url() ?>/api/employee_actions.php" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="upload_additional_document">

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem; text-transform: uppercase;">Document Type</label>
                    <select name="document_type" class="form-select bg-light border-0 py-2" required>
                        <option value="">Select Document Type</option>
                        <option value="Academic Certificate">Academic Certificate</option>
                        <option value="CV / Resume">CV / Resume</option>
                        <option value="Citizenship Copy">Citizenship Copy</option>
                        <option value="Police Clearance">Police Clearance</option>
                        <option value="PAN Card">PAN Card</option>
                        <option value="Experience Letter">Experience Letter</option>
                        <option value="Other Certification">Other Certification</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem; text-transform: uppercase;">Select File</label>
                    <input type="file" name="document_file" class="form-control bg-light border-0 py-2" accept="application/pdf, image/png, image/jpeg" required>
                    <div class="form-text small mt-2"><i class="fa-solid fa-circle-info me-1"></i>Accepted formats: PDF, JPG, PNG. Max: 5MB</div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm rounded-pill"><i class="fa-solid fa-cloud-arrow-up me-2"></i>Upload to Vault</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    setupAjaxForm('#docUploadForm', function(res) {
        window.location.reload();
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
