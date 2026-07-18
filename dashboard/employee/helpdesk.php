<?php
declare(strict_types=1);

$pageTitle = "Help Desk & Support";
require_once __DIR__ . '/../../includes/dashboard_header.php';
?>

<div class="row g-4">
    <!-- Submit Ticket Column -->
    <div class="col-lg-5">
        <div class="card-modern shadow-lg border-0 p-4 p-md-5 bg-white">
            <h5 class="text-dark fw-bold mb-4" style="font-family: 'Poppins', sans-serif;">
                <i class="fa-solid fa-life-ring text-primary me-2"></i>Submit a Ticket
            </h5>
            
            <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Help Desk backend not implemented in UI mockup.');">
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem; text-transform: uppercase;">Issue Category</label>
                    <select class="form-select bg-light border-0 py-2" required>
                        <option value="">Select Category</option>
                        <option value="IT Support">IT Support (Hardware/Software)</option>
                        <option value="HR Inquiry">HR & Payroll Inquiry</option>
                        <option value="Portal Access">Portal Access Issue</option>
                        <option value="Other">Other Request</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem; text-transform: uppercase;">Subject</label>
                    <input type="text" class="form-control bg-light border-0 py-2" placeholder="Briefly describe the issue" required>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem; text-transform: uppercase;">Description</label>
                    <textarea class="form-control bg-light border-0 py-2" rows="5" placeholder="Provide detailed information..." required></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem; text-transform: uppercase;">Attachment (Optional)</label>
                    <input type="file" class="form-control bg-light border-0 py-2">
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm rounded-pill"><i class="fa-solid fa-paper-plane me-2"></i>Send Request</button>
            </form>
        </div>
    </div>

    <!-- Ticket History Column -->
    <div class="col-lg-7">
        <div class="card-modern shadow-sm border-0 p-4 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="text-dark fw-bold mb-0" style="font-family: 'Poppins', sans-serif;">My Tickets</h5>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill">3 Total</span>
            </div>
            
            <div class="list-group list-group-flush">
                <!-- Ticket Item -->
                <div class="list-group-item px-0 py-3 border-bottom border-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-warning text-dark rounded-pill shadow-sm px-3"><i class="fa-solid fa-spinner fa-spin me-1"></i> In Progress</span>
                        <small class="text-muted fw-bold">#TCK-0012</small>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Cannot access company VPN</h6>
                    <p class="text-muted small mb-2 text-truncate">I am receiving an authentication error when trying to connect to the VPN from my home network...</p>
                    <div class="d-flex align-items-center text-muted" style="font-size: 0.75rem;">
                        <span class="me-3"><i class="fa-solid fa-layer-group me-1"></i> IT Support</span>
                        <span><i class="fa-regular fa-clock me-1"></i> Updated 2 hours ago</span>
                    </div>
                </div>

                <!-- Ticket Item -->
                <div class="list-group-item px-0 py-3 border-bottom border-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-success rounded-pill shadow-sm px-3"><i class="fa-solid fa-check me-1"></i> Resolved</span>
                        <small class="text-muted fw-bold">#TCK-0008</small>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Update direct deposit information</h6>
                    <p class="text-muted small mb-2 text-truncate">I need to switch my direct deposit to my new bank account starting next month.</p>
                    <div class="d-flex align-items-center text-muted" style="font-size: 0.75rem;">
                        <span class="me-3"><i class="fa-solid fa-layer-group me-1"></i> HR Inquiry</span>
                        <span><i class="fa-regular fa-clock me-1"></i> Updated 1 week ago</span>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
