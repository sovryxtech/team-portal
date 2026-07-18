<?php
declare(strict_types=1);

$pageTitle = "Overview";
require_once __DIR__ . '/../../includes/dashboard_header.php';
require_once __DIR__ . '/../../src/Controller/EmployeeController.php';

$currentUser = auth_user();
$empController = new \Src\Controller\EmployeeController();
$details = $empController->getDetailsByUserId((int)$currentUser['id']);

if (!$details) {
    echo '<div class="alert alert-danger">Employee profile not loaded. Please contact HR.</div>';
    require_once __DIR__ . '/../../includes/dashboard_footer.php';
    exit;
}
?>

<div class="row g-4">
    <!-- Left Column (Hero, Quick Actions, Overview, Announcements) -->
    <div class="col-xl-8 col-lg-7">
        
        <!-- Hero Welcome Card -->
        <div class="card-modern bg-gradient-primary text-white position-relative overflow-hidden mb-4 border-0" style="min-height: 240px;">
            <div class="position-absolute" style="top: -50px; right: -50px; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%); border-radius: 50%;"></div>
            
            <div class="row h-100 position-relative z-1">
                <div class="col-md-7 d-flex flex-column justify-content-center">
                    <p class="mb-1 text-white-50">Welcome to</p>
                    <h2 class="mb-3 fw-bold">Sovryx Team Portal</h2>
                    <p class="mb-4 text-white-50 fw-light">Your digital workplace<br>all-in-one platform</p>
                    
                    <div>
                        <a href="<?= get_base_url() ?>/dashboard/employee/index.php" class="btn btn-light text-primary rounded-pill px-4 fw-bold shadow-sm">
                            View Profile <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                <!-- Mockup illustration space for QR Card -->
                <div class="col-md-5 d-none d-md-flex align-items-center justify-content-center position-relative">
                    <div class="bg-white p-3 rounded-4 shadow-lg text-center" style="width: 140px; transform: rotate(5deg);">
                        <?php if (!empty($details['profile_photo'])): ?>
                            <img src="<?= get_base_url() . '/' . $details['profile_photo'] ?>" alt="Profile Photo" class="rounded-circle mb-2 border" style="width: 50px; height: 50px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="fw-bold text-dark" style="font-size: 0.75rem;"><?= htmlspecialchars(explode(' ', trim($details['full_name']))[0]) ?></div>
                        <div class="text-muted mb-2" style="font-size: 0.6rem;"><?= htmlspecialchars($details['employee_custom_id']) ?></div>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=<?= urlencode(get_base_url() . '/verify/index.php?id=' . $details['employee_custom_id']) ?>" class="img-fluid border p-1 rounded" alt="QR Code">
                    </div>
                </div>
            </div>
            
            <!-- Pagination dots mockup -->
            <div class="position-absolute bottom-0 start-50 translate-middle-x mb-3 d-flex gap-2">
                <div class="rounded-circle bg-white" style="width: 8px; height: 8px;"></div>
                <div class="rounded-circle bg-white opacity-50" style="width: 8px; height: 8px;"></div>
                <div class="rounded-circle bg-white opacity-50" style="width: 8px; height: 8px;"></div>
                <div class="rounded-circle bg-white opacity-50" style="width: 8px; height: 8px;"></div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <a href="<?= get_base_url() ?>/dashboard/employee/id_card.php" class="text-decoration-none">
                    <div class="card-modern text-center h-100 hover-lift">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary bg-opacity-10 text-primary mb-3" style="width: 54px; height: 54px; font-size: 1.5rem;">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <h6 class="text-dark mb-0 fw-bold" style="font-size: 0.9rem;">My ID Card</h6>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="#" class="text-decoration-none">
                    <div class="card-modern text-center h-100 hover-lift">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-success bg-opacity-10 text-success mb-3" style="width: 54px; height: 54px; font-size: 1.5rem;">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                        <h6 class="text-dark mb-0 fw-bold" style="font-size: 0.9rem;">My Documents</h6>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="#" class="text-decoration-none">
                    <div class="card-modern text-center h-100 hover-lift">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-warning bg-opacity-10 text-warning mb-3" style="width: 54px; height: 54px; font-size: 1.5rem;">
                            <i class="fa-solid fa-calendar-plus"></i>
                        </div>
                        <h6 class="text-dark mb-0 fw-bold" style="font-size: 0.9rem;">Apply Leave</h6>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="<?= get_base_url() ?>/dashboard/employee/communications.php" class="text-decoration-none">
                    <div class="card-modern text-center h-100 hover-lift">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-info bg-opacity-10 text-info mb-3" style="width: 54px; height: 54px; font-size: 1.5rem;">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <h6 class="text-dark mb-0 fw-bold" style="font-size: 0.9rem;">Help Desk</h6>
                    </div>
                </a>
            </div>
        </div>

        <!-- Overview -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0 text-dark">Overview</h5>
            <a href="#" class="text-primary text-decoration-none fw-bold small">View All <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>
        
        <div class="row g-3 mb-5">
            <div class="col-md-3 col-6">
                <div class="card-modern h-100 p-3 text-center border">
                    <span class="text-muted d-block small mb-2">Employee ID</span>
                    <strong class="d-block mb-3 text-dark"><?= htmlspecialchars($details['employee_custom_id']) ?></strong>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3"><i class="fa-solid fa-circle me-1" style="font-size: 0.5rem;"></i> Active</span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card-modern h-100 p-3 border">
                    <span class="text-muted d-block small mb-2">Department</span>
                    <strong class="d-block mb-3 text-dark"><?= htmlspecialchars($details['department_name']) ?></strong>
                    <div class="text-success"><i class="fa-solid fa-code fs-4"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card-modern h-100 p-3 border">
                    <span class="text-muted d-block small mb-2">Designation</span>
                    <strong class="d-block mb-3 text-dark text-truncate"><?= htmlspecialchars($details['designation_title']) ?></strong>
                    <div class="text-warning"><i class="fa-solid fa-briefcase fs-4"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card-modern h-100 p-3 border">
                    <span class="text-muted d-block small mb-2">Joining Date</span>
                    <strong class="d-block mb-3 text-dark"><?= date('d M Y', strtotime($details['joining_date'])) ?></strong>
                    <div class="text-danger"><i class="fa-regular fa-calendar-check fs-4"></i></div>
                </div>
            </div>
        </div>

        <!-- Announcements -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0 text-dark">Announcements</h5>
            <a href="<?= get_base_url() ?>/dashboard/employee/communications.php" class="text-primary text-decoration-none fw-bold small">View All <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>
        
        <div class="card-modern p-0 overflow-hidden border">
            <div class="list-group list-group-flush">
                <!-- Mock Announcement 1 -->
                <div class="list-group-item d-flex gap-3 py-4 align-items-start border-0 border-bottom">
                    <div class="d-inline-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10 text-primary p-3">
                        <i class="fa-solid fa-bullhorn fs-5"></i>
                    </div>
                    <div class="w-100">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0 fw-bold text-dark">Office Closed on Friday</h6>
                            <small class="text-muted">2h ago</small>
                        </div>
                        <p class="text-secondary small mb-2">The office will remain closed on 23rd May 2024 due to Buddha Purnima.</p>
                        <span class="badge bg-primary rounded-1">New</span>
                    </div>
                </div>
                <!-- Mock Announcement 2 -->
                <div class="list-group-item d-flex gap-3 py-4 align-items-start border-0 border-bottom">
                    <div class="d-inline-flex align-items-center justify-content-center rounded bg-success bg-opacity-10 text-success p-3">
                        <i class="fa-solid fa-file-contract fs-5"></i>
                    </div>
                    <div class="w-100">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0 fw-bold text-dark">New HR Policy Update</h6>
                            <small class="text-muted">1d ago</small>
                        </div>
                        <p class="text-secondary small mb-2">Please review the new HR policy which has been updated on our portal.</p>
                        <span class="badge bg-success rounded-1">New</span>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Right Column (Events, Tasks, Profile) -->
    <div class="col-xl-4 col-lg-5">
        
        <!-- Profile Completion -->
        <div class="card-modern border mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold mb-0 text-dark">Profile Completion</h6>
                <a href="#" class="text-primary text-decoration-none fw-bold small">View Profile <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
            
            <div class="d-flex align-items-center gap-4 mb-4">
                <!-- Circular Progress Ring (CSS based) -->
                <div class="position-relative d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle" style="width: 80px; height: 80px; border: 4px solid var(--success);">
                    <h4 class="mb-0 text-success fw-bold">85%</h4>
                </div>
                <div>
                    <strong class="d-block text-dark mb-1">Great! Your profile is almost complete.</strong>
                    <span class="text-muted small">Keep your profile updated for better experience.</span>
                </div>
            </div>
            
            <div class="d-flex justify-content-between flex-wrap gap-2 small text-muted">
                <span><i class="fa-solid fa-circle-check text-success me-1"></i> Personal Info</span>
                <span><i class="fa-solid fa-circle-check text-success me-1"></i> Contact Info</span>
                <span><i class="fa-solid fa-circle-check text-success me-1"></i> Employment Info</span>
                <span><i class="fa-solid fa-circle text-primary me-1"></i> Documents</span>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="card-modern border mb-4 p-0">
            <div class="p-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">Upcoming Events</h6>
                    <a href="#" class="text-primary text-decoration-none fw-bold small">View All <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
            </div>
            <div class="list-group list-group-flush p-2">
                <div class="list-group-item d-flex gap-3 align-items-center border-0 p-3 rounded hover-bg-light">
                    <div class="text-center rounded bg-danger bg-opacity-10 text-danger p-2" style="min-width: 50px;">
                        <span class="d-block small fw-bold">MAY</span>
                        <strong class="d-block fs-5">25</strong>
                    </div>
                    <div>
                        <strong class="d-block text-dark mb-1">Monthly Meeting</strong>
                        <div class="text-muted small mb-1"><i class="fa-regular fa-clock me-1"></i> 10:00 AM - 11:00 AM</div>
                        <div class="text-muted small"><i class="fa-solid fa-location-dot me-1"></i> Conference Room</div>
                    </div>
                </div>
                
                <div class="list-group-item d-flex gap-3 align-items-center border-0 p-3 rounded hover-bg-light mt-1">
                    <div class="text-center rounded bg-primary bg-opacity-10 text-primary p-2" style="min-width: 50px;">
                        <span class="d-block small fw-bold">MAY</span>
                        <strong class="d-block fs-5">30</strong>
                    </div>
                    <div>
                        <strong class="d-block text-dark mb-1">Training Session</strong>
                        <div class="text-muted small mb-1"><i class="fa-regular fa-clock me-1"></i> 02:00 PM - 04:00 PM</div>
                        <div class="text-muted small"><i class="fa-solid fa-video me-1"></i> Online (Google Meet)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Tasks -->
        <div class="card-modern border p-0">
            <div class="p-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">My Tasks</h6>
                    <a href="#" class="text-primary text-decoration-none fw-bold small">View All <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
            </div>
            
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center border-0 p-4 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <input class="form-check-input fs-5 rounded-circle mt-0" type="checkbox" value="">
                        <div>
                            <strong class="d-block text-dark">Update Project Report</strong>
                            <span class="text-muted small">Due: 24 May 2024</span>
                        </div>
                    </div>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-1">High</span>
                </div>
                
                <div class="list-group-item d-flex justify-content-between align-items-center border-0 p-4 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <input class="form-check-input fs-5 rounded-circle mt-0" type="checkbox" value="">
                        <div>
                            <strong class="d-block text-dark">UI Design Review</strong>
                            <span class="text-muted small">Due: 26 May 2024</span>
                        </div>
                    </div>
                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-1">Medium</span>
                </div>
                
                <div class="list-group-item d-flex justify-content-between align-items-center border-0 p-4">
                    <div class="d-flex align-items-center gap-3">
                        <input class="form-check-input fs-5 rounded-circle mt-0" type="checkbox" value="">
                        <div>
                            <strong class="d-block text-dark">Fix Bugs</strong>
                            <span class="text-muted small">Due: 28 May 2024</span>
                        </div>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-1">Low</span>
                </div>
            </div>
        </div>
        
    </div>
</div>

<style>
/* Hover utilities just for this page */
.hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.hover-lift:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
.hover-bg-light:hover { background-color: rgba(0,0,0,0.02) !important; cursor: pointer; }
.form-check-input:checked { background-color: var(--primary-color); border-color: var(--primary-color); }
</style>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
