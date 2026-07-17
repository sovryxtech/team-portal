<?php
declare(strict_types=1);

$pageTitle = "Organizational Structure CRUD Manager";
require_once __DIR__ . '/../../includes/dashboard_header.php';
require_once __DIR__ . '/../../includes/utils.php';

$pdo = get_db_connection();

// Fetch organizational lists
$companies = $pdo->query("SELECT * FROM companies")->fetchAll();
$branches = $pdo->query("SELECT b.*, c.name as company_name FROM branches b JOIN companies c ON b.company_id = c.id")->fetchAll();
$departments = $pdo->query("SELECT d.*, b.name as branch_name FROM departments d JOIN branches b ON d.branch_id = b.id")->fetchAll();
$designations = $pdo->query("SELECT ds.*, dp.name as department_name FROM designations ds JOIN departments dp ON ds.department_id = dp.id")->fetchAll();
$careers = $pdo->query("SELECT c.*, d.name as dept_name, b.name as branch_name FROM careers c JOIN departments d ON c.department_id = d.id JOIN branches b ON c.branch_id = b.id ORDER BY c.id DESC")->fetchAll();
?>

<!-- Tab Selector Navigation -->
<ul class="nav nav-pills mb-4 gap-2" id="orgTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active px-4 py-2 font-weight-bold" id="companies-tab" data-bs-toggle="tab" data-bs-target="#companies-pane" type="button" role="tab"><i class="fa-solid fa-building me-2"></i>Companies</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link px-4 py-2 font-weight-bold" id="branches-tab" data-bs-toggle="tab" data-bs-target="#branches-pane" type="button" role="tab"><i class="fa-solid fa-code-branch me-2"></i>Branches</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link px-4 py-2 font-weight-bold" id="departments-tab" data-bs-toggle="tab" data-bs-target="#departments-pane" type="button" role="tab"><i class="fa-solid fa-folder me-2"></i>Departments</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link px-4 py-2 font-weight-bold" id="designations-tab" data-bs-toggle="tab" data-bs-target="#designations-pane" type="button" role="tab"><i class="fa-solid fa-award me-2"></i>Designations</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link px-4 py-2 font-weight-bold" id="careers-tab" data-bs-toggle="tab" data-bs-target="#careers-pane" type="button" role="tab"><i class="fa-solid fa-briefcase me-2"></i>Careers Opportunities</button>
    </li>
</ul>

<!-- Tab Contents -->
<div class="tab-content" id="orgTabContent">
    <!-- Companies Pane -->
    <div class="tab-pane fade show active" id="companies-pane" role="tabpanel" tabindex="0">
        <div class="card-custom p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="text-primary mb-0">Companies List</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#companyModal"><i class="fa-solid fa-plus me-1"></i>Add Company</button>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($companies as $comp): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($comp['name']) ?></strong></td>
                                <td><?= htmlspecialchars($comp['address'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($comp['contact'] ?? 'N/A') ?></td>
                                <td class="text-end">
                                    <button class="btn btn-outline-danger btn-sm delete-org-btn" data-type="company" data-id="<?= $comp['id'] ?>"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Branches Pane -->
    <div class="tab-pane fade" id="branches-pane" role="tabpanel" tabindex="0">
        <div class="card-custom p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="text-primary mb-0">Branches List</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#branchModal"><i class="fa-solid fa-plus me-1"></i>Add Branch</button>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($branches as $br): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($br['name']) ?></strong></td>
                                <td><?= htmlspecialchars($br['company_name']) ?></td>
                                <td><?= htmlspecialchars($br['address'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($br['contact'] ?? 'N/A') ?></td>
                                <td class="text-end">
                                    <button class="btn btn-outline-danger btn-sm delete-org-btn" data-type="branch" data-id="<?= $br['id'] ?>"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Departments Pane -->
    <div class="tab-pane fade" id="departments-pane" role="tabpanel" tabindex="0">
        <div class="card-custom p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="text-primary mb-0">Departments List</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#departmentModal"><i class="fa-solid fa-plus me-1"></i>Add Department</button>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Department Name</th>
                            <th>Branch Location</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($departments as $dept): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($dept['name']) ?></strong></td>
                                <td><?= htmlspecialchars($dept['branch_name']) ?></td>
                                <td class="text-end">
                                    <button class="btn btn-outline-danger btn-sm delete-org-btn" data-type="department" data-id="<?= $dept['id'] ?>"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Designations Pane -->
    <div class="tab-pane fade" id="designations-pane" role="tabpanel" tabindex="0">
        <div class="card-custom p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="text-primary mb-0">Designations List</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#designationModal"><i class="fa-solid fa-plus me-1"></i>Add Designation</button>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Department</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($designations as $desig): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($desig['title']) ?></strong></td>
                                <td><?= htmlspecialchars($desig['department_name']) ?></td>
                                <td class="text-end">
                                    <button class="btn btn-outline-danger btn-sm delete-org-btn" data-type="designation" data-id="<?= $desig['id'] ?>"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Careers Pane -->
    <div class="tab-pane fade" id="careers-pane" role="tabpanel" tabindex="0">
        <div class="card-custom p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="text-primary mb-0">Careers List</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#careerModal"><i class="fa-solid fa-plus me-1"></i>Add Career Opportunity</button>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Department</th>
                            <th>Branch Location</th>
                            <th>Employment Type</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($careers as $car): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($car['title']) ?></strong></td>
                                <td><?= htmlspecialchars($car['dept_name']) ?></td>
                                <td><?= htmlspecialchars($car['branch_name']) ?></td>
                                <td><?= htmlspecialchars($car['type']) ?></td>
                                <td><span class="badge bg-success"><?= htmlspecialchars($car['status']) ?></span></td>
                                <td class="text-end">
                                    <button class="btn btn-outline-danger btn-sm delete-org-btn" data-type="career" data-id="<?= $car['id'] ?>"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Company Modal -->
<div class="modal fade" id="companyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="companyForm" action="<?= get_base_url() ?>/api/admin_actions.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="company_create">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title font-weight-bold">Create Company</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact</label>
                        <input type="text" name="contact" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary px-4">Save Company</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Branch Modal -->
<div class="modal fade" id="branchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="branchForm" action="<?= get_base_url() ?>/api/admin_actions.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="branch_create">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title font-weight-bold">Create Branch</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Mapping Company</label>
                        <select name="company_id" class="form-select" required>
                            <?php foreach ($companies as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Branch Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact</label>
                        <input type="text" name="contact" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary px-4">Save Branch</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Department Modal -->
<div class="modal fade" id="departmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="departmentForm" action="<?= get_base_url() ?>/api/admin_actions.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="department_create">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title font-weight-bold">Create Department</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Mapping Branch</label>
                        <select name="branch_id" class="form-select" required>
                            <?php foreach ($branches as $br): ?>
                                <option value="<?= $br['id'] ?>"><?= htmlspecialchars($br['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary px-4">Save Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Designation Modal -->
<div class="modal fade" id="designationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="designationForm" action="<?= get_base_url() ?>/api/admin_actions.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="designation_create">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title font-weight-bold">Create Designation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Mapping Department</label>
                        <select name="department_id" class="form-select" required>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Designation Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary px-4">Save Designation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Career Modal -->
<div class="modal fade" id="careerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="careerForm" action="<?= get_base_url() ?>/api/admin_actions.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="career_create">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title font-weight-bold">Create Career Opportunity</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Job Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Senior Software Engineer" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Office Branch</label>
                        <select name="branch_id" class="form-select" required>
                            <?php foreach ($branches as $br): ?>
                                <option value="<?= $br['id'] ?>"><?= htmlspecialchars($br['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Corporate Department</label>
                        <select name="department_id" class="form-select" required>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Employment Type</label>
                        <select name="type" class="form-select" required>
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Intern">Intern</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary px-4">Save Opportunity</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    setupAjaxForm('#companyForm', function(){ window.location.reload(); });
    setupAjaxForm('#branchForm', function(){ window.location.reload(); });
    setupAjaxForm('#departmentForm', function(){ window.location.reload(); });
    setupAjaxForm('#designationForm', function(){ window.location.reload(); });
    setupAjaxForm('#careerForm', function(){ window.location.reload(); });

    // Handle delete operations
    $('.delete-org-btn').on('click', function() {
        var id = $(this).data('id');
        var type = $(this).data('type');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently remove the organizational record and potentially cascade delete children links!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#475569',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= get_base_url() ?>/api/admin_actions.php',
                    type: 'POST',
                    data: {
                        action: type + '_delete',
                        id: id,
                        csrf_token: '<?= csrf_token() ?>'
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('Deleted!', res.message, 'success').then(function(){
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    }
                });
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
