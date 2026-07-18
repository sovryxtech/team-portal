<?php
declare(strict_types=1);

$pageTitle = "Account Settings";
require_once __DIR__ . '/../../includes/dashboard_header.php';
$currentUser = auth_user();
?>

<div class="row g-4">
    <!-- Settings Navigation -->
    <div class="col-lg-3">
        <div class="card-modern shadow-sm border-0 bg-white sticky-top" style="top: 20px;">
            <div class="list-group list-group-flush" id="settings-tabs" role="tablist">
                <a class="list-group-item list-group-item-action active border-0 py-3 fw-bold" id="tab-profile" data-bs-toggle="list" href="#pane-profile" role="tab">
                    <i class="fa-solid fa-user me-2 text-primary"></i> Profile
                </a>
                <a class="list-group-item list-group-item-action border-0 py-3 fw-bold" id="tab-security" data-bs-toggle="list" href="#pane-security" role="tab">
                    <i class="fa-solid fa-shield-halved me-2 text-primary"></i> Security & Password
                </a>
                <a class="list-group-item list-group-item-action border-0 py-3 fw-bold" id="tab-preferences" data-bs-toggle="list" href="#pane-preferences" role="tab">
                    <i class="fa-solid fa-sliders me-2 text-primary"></i> Preferences
                </a>
                <a class="list-group-item list-group-item-action border-0 py-3 fw-bold text-danger" href="<?= get_base_url() ?>/logout.php">
                    <i class="fa-solid fa-right-from-bracket me-2"></i> Sign Out
                </a>
            </div>
        </div>
    </div>

    <!-- Settings Content -->
    <div class="col-lg-9">
        <div class="tab-content" id="nav-tabContent">
            
            <!-- Profile Pane -->
            <div class="tab-pane fade show active" id="pane-profile" role="tabpanel">
                <div class="card-modern shadow-lg border-0 p-4 p-md-5 bg-white mb-4">
                    <h5 class="text-dark fw-bold mb-4" style="font-family: 'Poppins', sans-serif;">Public Profile Details</h5>
                    <div class="d-flex align-items-center mb-5">
                        <div class="me-4 position-relative">
                            <?php if (!empty($currentUser['profile_photo'])): ?>
                                <img src="<?= get_base_url() . '/' . $currentUser['profile_photo'] ?>" alt="Profile" class="rounded-circle shadow" style="width: 100px; height: 100px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 100px; height: 100px; font-size: 2.5rem; font-weight: 600;">
                                    <?= strtoupper(substr($currentUser['username'], 0, 2)) ?>
                                </div>
                            <?php endif; ?>
                            <button class="btn btn-sm btn-light border position-absolute bottom-0 end-0 rounded-circle shadow-sm" style="width: 32px; height: 32px;" onclick="alert('Photo upload logic not implemented');"><i class="fa-solid fa-camera"></i></button>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1"><?= htmlspecialchars($currentUser['full_name']) ?></h5>
                            <p class="text-muted mb-2"><?= htmlspecialchars($currentUser['role_name']) ?></p>
                            <button class="btn btn-outline-primary btn-sm rounded-pill fw-bold px-3">Change Avatar</button>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem; text-transform: uppercase;">Full Name</label>
                            <input type="text" class="form-control bg-light border-0 py-2" value="<?= htmlspecialchars($currentUser['full_name']) ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem; text-transform: uppercase;">Username</label>
                            <input type="text" class="form-control bg-light border-0 py-2" value="<?= htmlspecialchars($currentUser['username']) ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem; text-transform: uppercase;">Email Address</label>
                            <input type="email" class="form-control bg-light border-0 py-2" value="<?= htmlspecialchars($currentUser['email']) ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem; text-transform: uppercase;">Phone Number</label>
                            <input type="text" class="form-control bg-light border-0 py-2" placeholder="Contact number" disabled>
                        </div>
                        <div class="col-12 mt-4">
                            <button class="btn btn-primary px-4 py-2 fw-bold shadow-sm rounded-pill" onclick="alert('Request profile update to HR functionality coming soon');">Request Details Update</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Pane -->
            <div class="tab-pane fade" id="pane-security" role="tabpanel">
                <div class="card-modern shadow-lg border-0 p-4 p-md-5 bg-white">
                    <h5 class="text-dark fw-bold mb-4" style="font-family: 'Poppins', sans-serif;">Change Password</h5>
                    
                    <form onsubmit="event.preventDefault(); alert('Password change mock only');">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem; text-transform: uppercase;">Current Password</label>
                            <input type="password" class="form-control bg-light border-0 py-2" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem; text-transform: uppercase;">New Password</label>
                            <input type="password" class="form-control bg-light border-0 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem; text-transform: uppercase;">Confirm New Password</label>
                            <input type="password" class="form-control bg-light border-0 py-2" required>
                        </div>
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm rounded-pill">Update Password</button>
                    </form>

                    <hr class="my-5 border-light">
                    
                    <h5 class="text-dark fw-bold mb-3" style="font-family: 'Poppins', sans-serif;">Two-Factor Authentication</h5>
                    <p class="text-muted small mb-4">Add an extra layer of security to your account by enabling 2FA. Currently, this relies on email OTPs.</p>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="2faSwitch" checked disabled>
                        <label class="form-check-label fw-bold" for="2faSwitch">Email OTP Authentication</label>
                    </div>
                </div>
            </div>

            <!-- Preferences Pane -->
            <div class="tab-pane fade" id="pane-preferences" role="tabpanel">
                <div class="card-modern shadow-lg border-0 p-4 p-md-5 bg-white">
                    <h5 class="text-dark fw-bold mb-4" style="font-family: 'Poppins', sans-serif;">System Preferences</h5>
                    
                    <div class="mb-4 pb-4 border-bottom border-light">
                        <h6 class="fw-bold mb-3">Language & Region</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem; text-transform: uppercase;">Display Language</label>
                                <select class="form-select bg-light border-0 py-2">
                                    <option>English (US)</option>
                                    <option>Nepali (NE)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 pb-4 border-bottom border-light">
                        <h6 class="fw-bold mb-3">Notification Settings</h6>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="notifAnnouncements" checked>
                            <label class="form-check-label fw-bold" for="notifAnnouncements">Company Announcements (Email)</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="notifNews" checked>
                            <label class="form-check-label fw-bold" for="notifNews">Company News Updates</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="notifApprove">
                            <label class="form-check-label fw-bold" for="notifApprove">Document Approvals (Email)</label>
                        </div>
                    </div>
                    
                    <button class="btn btn-primary px-4 py-2 fw-bold shadow-sm rounded-pill" onclick="alert('Preferences saved');">Save Preferences</button>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
/* Settings Navigation active state */
.list-group-item.active {
    background-color: var(--primary-color) !important;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(24, 28, 184, 0.2);
}
.list-group-item.active i {
    color: white !important;
}
.list-group-item {
    transition: all 0.2s ease;
}
</style>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
