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
    <!-- List of Current Documents -->
    <div class="col-lg-7">
        <div class="card-custom p-4 bg-white">
            <h5 class="text-primary mb-4"><i class="fa-solid fa-file-shield me-2"></i>My Uploaded Documents</h5>
            
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Document Type</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($documents)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No documents found in vault.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($documents as $doc): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($doc['document_type']) ?></strong>
                                    </td>
                                    <td>
                                        <?php if ($doc['status'] === 'Verified'): ?>
                                            <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Verified</span>
                                        <?php elseif ($doc['status'] === 'Pending'): ?>
                                            <span class="badge bg-warning text-dark"><i class="fa-solid fa-hourglass-start me-1"></i>Pending Review</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="fa-solid fa-circle-xmark me-1"></i>Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= get_base_url() . '/' . $doc['file_path'] ?>" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-file-arrow-down me-1"></i>Download</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Upload Additional Document Form -->
    <div class="col-lg-5">
        <div class="card-custom p-4 bg-white">
            <h5 class="text-primary mb-4"><i class="fa-solid fa-cloud-arrow-up me-2"></i>Upload Additional Record</h5>
            
            <form id="docUploadForm" action="<?= get_base_url() ?>/api/employee_actions.php" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="upload_additional_document">

                <div class="mb-3">
                    <label class="form-label font-weight-bold">Document Type</label>
                    <select name="document_type" class="form-select" required>
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
                    <label class="form-label font-weight-bold">Select File</label>
                    <input type="file" name="document_file" class="form-control" accept="application/pdf, image/png, image/jpeg" required>
                    <div class="form-text small">Accepted formats: PDF, JPG, PNG. Max: 5MB</div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 font-weight-bold"><i class="fa-solid fa-cloud-arrow-up me-2"></i>Upload File to Vault</button>
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
