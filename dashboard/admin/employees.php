<?php
declare(strict_types=1);

// Handle PHP AJAX Endpoint first before sending any HTML output
if (isset($_GET['ajax_profile_details']) && isset($_GET['employee_id'])) {
    require_once __DIR__ . '/../../includes/auth.php';
    require_once __DIR__ . '/../../includes/db_connection.php';
    require_once __DIR__ . '/../../includes/utils.php';
    require_once __DIR__ . '/../../src/Controller/EmployeeController.php';

    $empId = (int)$_GET['employee_id'];
    $empController = new \Src\Controller\EmployeeController();
    $details = $empController->getDetails($empId);
    $documents = $empController->getDocuments($empId);
    
    if (!$details):
    ?>
        <div class="alert alert-danger">Employee directory record not found.</div>
    <?php
    else:
        $emergency = json_decode($details['emergency_contact'] ?? '{}', true);
    ?>
        <!-- Modal Layout -->
        <div class="row g-4">
            <!-- Left Pane photo & Profile Overview -->
            <div class="col-md-4 text-center border-end-md pb-3 pb-md-0">
                <div class="position-relative d-inline-block mb-3">
                    <?php if (!empty($details['profile_photo'])): ?>
                        <img src="<?= get_base_url() . '/' . $details['profile_photo'] ?>" alt="Profile Photo" class="rounded-4 img-fluid shadow-sm border" style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-primary text-white rounded-4 d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 150px; height: 150px; font-size: 4rem; font-weight: 700;">
                            <?= strtoupper(substr($details['full_name'], 0, 2)) ?>
                        </div>
                    <?php endif; ?>
                    <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-success border border-white border-3 px-2 py-1" style="transform: translate(-10%, -10%);">Active</span>
                </div>
                <h4 class="text-primary font-weight-bold mb-1"><?= htmlspecialchars($details['full_name']) ?></h4>
                <p class="text-secondary font-weight-bold small mb-2"><i class="fa-solid fa-id-badge me-1"></i><?= htmlspecialchars($details['designation_title']) ?></p>
                <span class="badge bg-secondary-custom text-primary px-3 py-2 rounded-3 small font-weight-bold"><?= htmlspecialchars($details['employee_custom_id']) ?></span>
                
                <div class="mt-4 text-start bg-light p-3 rounded-3">
                    <h6 class="text-muted font-weight-bold text-uppercase small mb-2">Emergency Contact</h6>
                    <p class="mb-1 text-dark" style="font-size: 0.85rem;"><strong><?= htmlspecialchars($emergency['name'] ?? 'N/A') ?></strong> (<?= htmlspecialchars($emergency['relation'] ?? '') ?>)</p>
                    <p class="mb-0 text-secondary" style="font-size: 0.85rem;"><i class="fa-solid fa-phone me-1 text-muted"></i><?= htmlspecialchars($emergency['phone'] ?? '') ?></p>
                </div>
            </div>
            
            <!-- Right Pane Details Grid -->
            <div class="col-md-8">
                <h6 class="text-primary font-weight-bold mb-3 border-bottom pb-2"><i class="fa-solid fa-user me-2"></i>Personal & Job Details</h6>
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="p-2 border-bottom">
                            <span class="text-muted small d-block">Corporate Email</span>
                            <span class="font-weight-bold text-dark" style="font-size: 0.9rem;"><?= htmlspecialchars($details['email']) ?></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-2 border-bottom">
                            <span class="text-muted small d-block">Phone Contact</span>
                            <span class="text-dark font-weight-bold" style="font-size: 0.9rem;"><?= htmlspecialchars($details['phone'] ?? 'N/A') ?></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-2 border-bottom">
                            <span class="text-muted small d-block">Birth Date / Gender</span>
                            <span class="text-dark font-weight-bold" style="font-size: 0.9rem;"><?= htmlspecialchars($details['dob'] ?? 'N/A') ?> &bull; <?= htmlspecialchars($details['gender'] ?? 'N/A') ?></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-2 border-bottom">
                            <span class="text-muted small d-block">Blood Group / Nationality</span>
                            <span class="text-dark font-weight-bold" style="font-size: 0.9rem;"><?= htmlspecialchars($details['blood_group'] ?? 'N/A') ?> &bull; <?= htmlspecialchars($details['nationality'] ?? 'N/A') ?></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-2 border-bottom">
                            <span class="text-muted small d-block">Department & Branch</span>
                            <span class="text-dark font-weight-bold" style="font-size: 0.9rem;"><?= htmlspecialchars($details['department_name']) ?> (<?= htmlspecialchars($details['branch_name']) ?>)</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-2 border-bottom">
                            <span class="text-muted small d-block">Joining Date / Type</span>
                            <span class="text-dark font-weight-bold" style="font-size: 0.9rem;"><?= htmlspecialchars($details['joining_date']) ?> (<?= htmlspecialchars($details['employment_type']) ?>)</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-2 border-bottom">
                            <span class="text-muted small d-block">Residential Address</span>
                            <span class="text-dark font-weight-bold" style="font-size: 0.9rem;"><?= htmlspecialchars($details['address'] ?? 'N/A') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Document Review inside profile -->
                <h6 class="text-primary font-weight-bold mb-3 border-bottom pb-2"><i class="fa-solid fa-file-shield me-2"></i>Workforce Documents & Verification</h6>
                <div class="list-group list-group-flush">
                    <?php if (empty($documents)): ?>
                        <div class="text-muted small p-2"><i class="fa-solid fa-circle-info me-1"></i>No credentials uploaded yet.</div>
                    <?php else: ?>
                        <?php foreach ($documents as $doc): ?>
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center flex-wrap gap-2" style="font-size: 0.85rem;">
                                <div>
                                    <span class="font-weight-bold d-block text-dark"><?= htmlspecialchars($doc['document_type']) ?></span>
                                    <a href="<?= get_base_url() . '/' . $doc['file_path'] ?>" target="_blank" class="small text-decoration-none text-primary"><i class="fa-solid fa-download me-1"></i>Download/View File</a>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if ($doc['status'] === 'Pending'): ?>
                                        <span class="badge bg-warning text-dark me-2">Pending Validation</span>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-success verify-doc-btn" data-id="<?= $doc['id'] ?>" data-status="Verified" title="Verify Document"><i class="fa-solid fa-check"></i></button>
                                            <button type="button" class="btn btn-danger verify-doc-btn" data-id="<?= $doc['id'] ?>" data-status="Rejected" title="Reject Document"><i class="fa-solid fa-xmark"></i></button>
                                        </div>
                                    <?php elseif ($doc['status'] === 'Verified'): ?>
                                        <span class="badge bg-success px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i>Verified</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger px-2 py-1"><i class="fa-solid fa-circle-xmark me-1"></i>Rejected</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php
    endif;
    exit;
}

$pageTitle = "Employees Directory";
require_once __DIR__ . '/../../includes/dashboard_header.php';
require_once __DIR__ . '/../../includes/utils.php';

$pdo = get_db_connection();

// Fetch employees with basic relationships
$employeesStmt = $pdo->query("
    SELECT e.id as employee_id, e.employee_custom_id, e.employment_status, e.employment_type,
           ep.full_name, ep.phone, u.status as user_status, u.email,
           d.name as department_name, dg.title as designation_title, b.name as branch_name
    FROM employees e
    JOIN employee_profiles ep ON e.id = ep.employee_id
    JOIN users u ON e.user_id = u.id
    JOIN departments d ON e.department_id = d.id
    JOIN designations dg ON e.designation_id = dg.id
    JOIN branches b ON e.branch_id = b.id
    ORDER BY e.id DESC
");
$employees = $employeesStmt->fetchAll();
?>

<!-- Directory DataTables Export Grid -->
<div class="card-custom p-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="text-primary mb-0"><i class="fa-solid fa-address-book me-2"></i>Active Workforce Records</h5>
        <span class="badge bg-primary px-3 py-2 rounded-pill"><?= count($employees) ?> Employees Registered</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle table-custom datatable-export">
            <thead>
                <tr>
                    <th>ID Code</th>
                    <th>Name</th>
                    <th>Branch / Department</th>
                    <th>Designation</th>
                    <th>Work Status</th>
                    <th>Log Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $emp): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($emp['employee_custom_id']) ?></strong></td>
                        <td>
                            <div class="font-weight-bold"><?= htmlspecialchars($emp['full_name']) ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($emp['email']) ?></div>
                        </td>
                        <td>
                            <div><?= htmlspecialchars($emp['department_name']) ?></div>
                            <span class="text-muted small"><?= htmlspecialchars($emp['branch_name']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($emp['designation_title']) ?></td>
                        <td>
                            <?php 
                            $statusClass = match ($emp['employment_status']) {
                                'Active'     => 'bg-success',
                                'Suspended'  => 'bg-warning',
                                'Terminated' => 'bg-danger',
                                'Resigned'   => 'bg-secondary',
                                default      => 'bg-primary'
                            };
                            ?>
                            <span class="badge <?= $statusClass ?> px-2 py-1 rounded-pill"><?= htmlspecialchars($emp['employment_status']) ?></span>
                        </td>
                        <td>
                            <span class="badge <?= $emp['user_status'] === 'Active' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' ?> px-2 py-1 rounded-pill">
                                <?= htmlspecialchars($emp['user_status']) ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <!-- Details Button -->
                                <button type="button" class="btn btn-outline-primary btn-sm view-profile-btn" data-id="<?= $emp['employee_id'] ?>" data-bs-toggle="tooltip" title="Profile Details">
                                    <i class="fa-solid fa-user-tie"></i>
                                </button>
                                <!-- Edit Status Button -->
                                <button type="button" class="btn btn-outline-warning btn-sm edit-status-btn" data-id="<?= $emp['employee_id'] ?>" data-emp-status="<?= $emp['employment_status'] ?>" data-user-status="<?= $emp['user_status'] ?>" data-bs-toggle="tooltip" title="Change Employment Status">
                                    <i class="fa-solid fa-user-gear"></i>
                                </button>
                                <!-- Export ID Badge -->
                                <a href="<?= get_base_url() ?>/api/download_id_card.php?id=<?= $emp['employee_id'] ?>" target="_blank" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Download Digital ID">
                                    <i class="fa-solid fa-address-card"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Profile Details Modal -->
<div class="modal fade" id="profileDetailsModal" tabindex="-1" aria-labelledby="profileDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white border-0">
                <h5 class="modal-title font-weight-bold" id="profileDetailsModalLabel"><i class="fa-solid fa-id-badge me-2"></i>Full Employee Profile Directory</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="profileModalContent">
                <!-- Dynamically loaded via Ajax / jQuery -->
                <div class="text-center py-5">
                    <span class="spinner-border text-primary" role="status"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Status Modal -->
<div class="modal fade" id="statusEditModal" tabindex="-1" aria-labelledby="statusEditModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-warning text-dark border-0">
                <h5 class="modal-title font-weight-bold" id="statusEditModalLabel"><i class="fa-solid fa-user-lock me-2"></i>Edit Account & Employment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="statusUpdateForm" action="<?= get_base_url() ?>/api/admin_actions.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update_employee_status">
                <input type="hidden" name="employee_id" id="editStatusEmpId">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Employment Status</label>
                        <select name="employment_status" id="editEmpStatus" class="form-select" required>
                            <option value="Active">Active</option>
                            <option value="Suspended">Suspended</option>
                            <option value="Terminated">Terminated</option>
                            <option value="Resigned">Resigned</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">User Portal Login Status</label>
                        <select name="user_status" id="editUserStatus" class="form-select" required>
                            <option value="Active">Active (Allowed to login)</option>
                            <option value="Inactive">Inactive (Blocked from login)</option>
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- AJAX fetching and handlers -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Edit status modal prefilling
    $('.edit-status-btn').on('click', function() {
        var empId = $(this).data('id');
        var empStatus = $(this).data('emp-status');
        var userStatus = $(this).data('user-status');
        
        $('#editStatusEmpId').val(empId);
        $('#editEmpStatus').val(empStatus);
        $('#editUserStatus').val(userStatus);
        
        var modal = new bootstrap.Modal(document.getElementById('statusEditModal'));
        modal.show();
    });

    setupAjaxForm('#statusUpdateForm', function(res) {
        window.location.reload();
    });

    // 2. Fetch full profile details dynamically inside modal
    $('.view-profile-btn').on('click', function() {
        var empId = $(this).data('id');
        var modal = new bootstrap.Modal(document.getElementById('profileDetailsModal'));
        modal.show();
        
        // Show spinner
        $('#profileModalContent').html('<div class="text-center py-5"><span class="spinner-border text-primary" role="status"></span></div>');
        
        // Perform direct Ajax call to retrieve details layout
        $.ajax({
            url: 'employees.php',
            type: 'GET',
            data: { ajax_profile_details: 1, employee_id: empId },
            success: function(html) {
                $('#profileModalContent').html(html);
                
                // Initialize document verify AJAX buttons inside the modal
                $('.verify-doc-btn').off('click').on('click', function() {
                    var docId = $(this).data('id');
                    var status = $(this).data('status');
                    var $btnGroup = $(this).closest('.btn-group');
                    
                    $btnGroup.html('<span class="spinner-border spinner-border-sm" role="status"></span>');
                    
                    $.ajax({
                        url: '<?= get_base_url() ?>/api/admin_actions.php',
                        type: 'POST',
                        data: {
                            action: 'verify_document',
                            document_id: docId,
                            status: status,
                            csrf_token: '<?= csrf_token() ?>'
                        },
                        dataType: 'json',
                        success: function(res) {
                            if (res.success) {
                                Swal.fire('Updated', res.message, 'success');
                                // Refresh profile modal
                                $('.view-profile-btn[data-id="' + empId + '"]').trigger('click');
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }
                    });
                });
            },
            error: function() {
                $('#profileModalContent').html('<div class="alert alert-danger text-center">Failed to retrieve profile record.</div>');
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
