<?php
declare(strict_types=1);

$pageTitle = "Registration Requests Approval System";
require_once __DIR__ . '/../../includes/dashboard_header.php';
require_once __DIR__ . '/../../includes/utils.php';

$pdo = get_db_connection();

// Fetch pending registrations
$registrationsStmt = $pdo->query("SELECT * FROM registration_requests WHERE status = 'Pending' ORDER BY id DESC");
$registrations = $registrationsStmt->fetchAll();

// Fetch organizational lists for the modal mapping dropdowns
$companies = $pdo->query("SELECT id, name FROM companies")->fetchAll();
$branches = $pdo->query("SELECT id, name FROM branches")->fetchAll();
$departments = $pdo->query("SELECT id, name FROM departments")->fetchAll();
$designations = $pdo->query("SELECT id, title FROM designations")->fetchAll();
$roles = $pdo->query("SELECT id, name FROM roles")->fetchAll();

// Prefill next sequential Employee ID
$nextEmployeeId = generate_next_employee_id();

// Check if a specific registration request was selected via GET query
$selectedReq = null;
$selectedId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($selectedId > 0) {
    $selStmt = $pdo->prepare("SELECT * FROM registration_requests WHERE id = :id");
    $selStmt->execute(['id' => $selectedId]);
    $selectedReq = $selStmt->fetch();
}
?>

<div class="row g-4">
    <!-- Registration Requests Grid List -->
    <div class="col-lg-6">
        <div class="card-custom p-4 bg-white">
            <h5 class="text-primary mb-4"><i class="fa-solid fa-list-check me-2"></i>Pending Applications</h5>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($registrations)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No pending registration requests found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($registrations as $reg): 
                                $formData = json_decode($reg['form_data_json'], true);
                                $isCurrent = ($selectedId === (int)$reg['id']);
                            ?>
                                <tr class="<?= $isCurrent ? 'table-primary' : '' ?>">
                                    <td>
                                        <strong><?= htmlspecialchars($formData['full_name']) ?></strong><br>
                                        <span class="text-muted small"><?= htmlspecialchars($formData['email']) ?></span>
                                    </td>
                                    <td><?= date('Y-m-d', strtotime($reg['created_at'])) ?></td>
                                    <td class="text-end">
                                        <a href="registrations.php?id=<?= $reg['id'] ?>" class="btn btn-primary btn-sm"><i class="fa-solid fa-eye me-1"></i>Review</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Applicant Review Panel -->
    <div class="col-lg-6">
        <div class="card-custom p-4 bg-white">
            <h5 class="text-primary mb-4"><i class="fa-solid fa-user-gear me-2"></i>Application Details</h5>

            <?php if (!$selectedReq): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-arrow-left-long d-block mb-3" style="font-size: 3rem;"></i>
                    Select an application from the left pane to review details, inspect files, and execute approvals or rejections.
                </div>
            <?php else: 
                $formData = json_decode($selectedReq['form_data_json'], true);
                $filePaths = json_decode($selectedReq['file_paths_json'], true);
            ?>
                <!-- Applicant Personal Detail Summary -->
                <div class="d-flex align-items-center mb-4">
                    <?php if (!empty($filePaths['profile_photo'])): ?>
                        <img src="<?= get_base_url() . '/' . $filePaths['profile_photo'] ?>" alt="Photo" class="rounded-circle me-3 border" style="width: 70px; height: 70px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 70px; height: 70px; font-size: 1.8rem;">
                            <?= strtoupper(substr($formData['full_name'], 0, 2)) ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h4 class="text-primary mb-1"><?= htmlspecialchars($formData['full_name']) ?></h4>
                        <span class="badge bg-warning px-3 py-1 rounded-pill">Pending HR Review</span>
                    </div>
                </div>

                <!-- Accordion Information groups -->
                <div class="accordion" id="applicantDetailsAccordion">
                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button px-0 py-2 border-0 bg-transparent text-primary font-weight-bold" type="button" data-bs-toggle="collapse" data-bs-target="#personalCollapse">
                                Personal & Contact Details
                            </button>
                        </h2>
                        <div id="personalCollapse" class="accordion-collapse collapse show">
                            <div class="py-3 text-secondary" style="font-size: 0.85rem;">
                                <div class="row mb-1">
                                    <div class="col-sm-4 text-muted">Date of Birth:</div>
                                    <div class="col-sm-8"><?= htmlspecialchars($formData['dob'] ?? 'N/A') ?></div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-sm-4 text-muted">Gender:</div>
                                    <div class="col-sm-8"><?= htmlspecialchars($formData['gender'] ?? 'N/A') ?></div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-sm-4 text-muted">Blood Group:</div>
                                    <div class="col-sm-8"><?= htmlspecialchars($formData['blood_group'] ?? 'N/A') ?></div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-sm-4 text-muted">Phone:</div>
                                    <div class="col-sm-8"><?= htmlspecialchars($formData['phone'] ?? 'N/A') ?>
                                        <?php if (!empty($formData['alternate_phone'])): ?>
                                            <br><small class="text-muted">Alt: <?= htmlspecialchars($formData['alternate_phone']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-sm-4 text-muted">Address:</div>
                                    <div class="col-sm-8">
                                        <?php 
                                            $addressData = $formData['address'] ?? 'N/A';
                                            if (is_string($addressData) && strpos($addressData, '{') === 0) {
                                                $decodedAddr = json_decode($addressData, true);
                                                if ($decodedAddr) {
                                                    echo '<strong>Current:</strong> ' . htmlspecialchars($decodedAddr['current'] ?? 'N/A') . '<br>';
                                                    echo '<strong>Permanent:</strong> ' . htmlspecialchars($decodedAddr['permanent'] ?? 'N/A');
                                                } else {
                                                    echo htmlspecialchars($addressData);
                                                }
                                            } else {
                                                echo htmlspecialchars($addressData);
                                            }
                                        ?>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-sm-4 text-muted">Emergency Contact:</div>
                                    <div class="col-sm-8">
                                        <?= htmlspecialchars($formData['emergency_name'] ?? 'N/A') ?> 
                                        (<?= htmlspecialchars($formData['emergency_relation'] ?? '') ?>) - 
                                        <?= htmlspecialchars($formData['emergency_phone'] ?? '') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed px-0 py-2 border-0 bg-transparent text-primary font-weight-bold" type="button" data-bs-toggle="collapse" data-bs-target="#backgroundCollapse">
                                Academic & Experience Summary
                            </button>
                        </h2>
                        <div id="backgroundCollapse" class="accordion-collapse collapse">
                            <div class="py-3 text-secondary" style="font-size: 0.85rem;">
                                <div class="row mb-1">
                                    <div class="col-sm-4 text-muted">Degree/Major:</div>
                                    <div class="col-sm-8"><?= htmlspecialchars($formData['highest_degree'] ?? 'N/A') ?></div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-sm-4 text-muted">Institution:</div>
                                    <div class="col-sm-8"><?= htmlspecialchars($formData['institution'] ?? 'N/A') ?></div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-12 text-muted">Experience Summary:</div>
                                    <div class="col-12 mt-1 bg-light p-2 rounded text-secondary" style="white-space: pre-wrap; font-size:0.8rem;"><?= htmlspecialchars($formData['experience_summary'] ?? 'N/A') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 border-bottom mb-4">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed px-0 py-2 border-0 bg-transparent text-primary font-weight-bold" type="button" data-bs-toggle="collapse" data-bs-target="#documentsCollapse">
                                Submitted Credentials & Documents
                            </button>
                        </h2>
                        <div id="documentsCollapse" class="accordion-collapse collapse">
                            <div class="py-3">
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($filePaths as $type => $path): 
                                        if ($type === 'profile_photo') continue;
                                    ?>
                                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                            <span><i class="fa-solid fa-file-pdf text-danger me-2"></i><?= ucwords(str_replace('_', ' ', $type)) ?></span>
                                            <a href="<?= get_base_url() . '/' . $path ?>" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download me-1"></i>View/Download</a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Executable CTA triggers -->
                <div class="d-flex gap-2">
                    <button class="btn btn-success flex-grow-1 py-2 font-weight-bold" data-bs-toggle="modal" data-bs-target="#approveModal"><i class="fa-solid fa-user-check me-2"></i>Approve Applicant</button>
                    <button class="btn btn-danger py-2 font-weight-bold px-4" data-bs-toggle="modal" data-bs-target="#rejectModal"><i class="fa-solid fa-circle-xmark me-2"></i>Reject</button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($selectedReq): ?>
<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white border-0">
                <h5 class="modal-title font-weight-bold" id="approveModalLabel"><i class="fa-solid fa-id-card-clip me-2"></i>Approve Applicant & Configure Employee Record</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="approveForm" action="<?= get_base_url() ?>/api/admin_actions.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="approve_registration">
                <input type="hidden" name="request_id" value="<?= $selectedReq['id'] ?>">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Assigned Official Employee ID <span class="text-danger">*</span></label>
                            <input type="text" name="employee_custom_id" class="form-control" value="<?= htmlspecialchars($nextEmployeeId) ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Company Mapping</label>
                            <select name="company_id" class="form-select" required>
                                <?php foreach ($companies as $comp): 
                                    $selected = (isset($formData['company_id']) && $formData['company_id'] == $comp['id']) ? 'selected' : '';
                                ?>
                                    <option value="<?= $comp['id'] ?>" <?= $selected ?>><?= htmlspecialchars($comp['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Office Branch <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-select" required>
                                <option value="">Select Branch</option>
                                <?php foreach ($branches as $br): 
                                    $selected = (isset($formData['branch_id']) && $formData['branch_id'] == $br['id']) ? 'selected' : '';
                                ?>
                                    <option value="<?= $br['id'] ?>" <?= $selected ?>><?= htmlspecialchars($br['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Corporate Department <span class="text-danger">*</span></label>
                            <select name="department_id" class="form-select" required>
                                <option value="">Select Department</option>
                                <?php foreach ($departments as $dept): 
                                    $selected = (isset($formData['department_id']) && $formData['department_id'] == $dept['id']) ? 'selected' : '';
                                ?>
                                    <option value="<?= $dept['id'] ?>" <?= $selected ?>><?= htmlspecialchars($dept['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Job Designation <span class="text-danger">*</span></label>
                            <select name="designation_id" class="form-select" required>
                                <option value="">Select Designation</option>
                                <?php foreach ($designations as $desig): 
                                    $selected = (isset($formData['designation_id']) && $formData['designation_id'] == $desig['id']) ? 'selected' : '';
                                ?>
                                    <option value="<?= $desig['id'] ?>" <?= $selected ?>><?= htmlspecialchars($desig['title']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Employment Designation (Role)</label>
                            <select name="role_id" class="form-select" required>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>" <?= $role['name'] == 'Employee' ? 'selected' : '' ?>><?= htmlspecialchars($role['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Employment Type</label>
                            <select name="employment_type" class="form-select" required>
                                <?php $prefType = $formData['employment_type'] ?? ''; ?>
                                <option value="Full-time" <?= $prefType == 'Full-time' ? 'selected' : '' ?>>Full-time</option>
                                <option value="Part-time" <?= $prefType == 'Part-time' ? 'selected' : '' ?>>Part-time</option>
                                <option value="Contract" <?= $prefType == 'Contract' ? 'selected' : '' ?>>Contract</option>
                                <option value="Intern" <?= $prefType == 'Intern' ? 'selected' : '' ?>>Intern</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Official Joining Date <span class="text-danger">*</span></label>
                            <input type="date" name="joining_date" class="form-control" value="<?= htmlspecialchars($formData['joining_date'] ?? date('Y-m-d')) ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success px-4 font-weight-bold"><i class="fa-solid fa-circle-check me-2"></i>Approve & Deploy</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title font-weight-bold" id="rejectModalLabel"><i class="fa-solid fa-triangle-exclamation me-2"></i>Reject Application</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="rejectForm" action="<?= get_base_url() ?>/api/admin_actions.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="reject_registration">
                <input type="hidden" name="request_id" value="<?= $selectedReq['id'] ?>">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-danger">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="5" placeholder="Specify detail rejection feedback..." required></textarea>
                    </div>
                </div>
                
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger px-4 font-weight-bold"><i class="fa-solid fa-circle-xmark me-2"></i>Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Setup AJAX modal submit workflows -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    <?php if ($selectedReq): ?>
        // Approve form submit handler
        setupAjaxForm('#approveForm', function(res) {
            window.location.href = 'registrations.php';
        });

        // Reject form submit handler
        setupAjaxForm('#rejectForm', function(res) {
            window.location.href = 'registrations.php';
        });
    <?php endif; ?>
});
</script>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
