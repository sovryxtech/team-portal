<?php
declare(strict_types=1);

$pageTitle = "Help Desk & Support";
require_once __DIR__ . '/../../includes/dashboard_header.php';
require_once __DIR__ . '/../../src/Controller/EmployeeController.php';

$currentUser = auth_user();
$empController = new \Src\Controller\EmployeeController();
$tickets = $empController->getSupportTickets((int)$currentUser['id']);
?>

<div class="row g-4">
    <!-- Submit Ticket Column -->
    <div class="col-lg-5">
        <div class="card-modern shadow-lg border-0 p-4 p-md-5 bg-white">
            <h5 class="text-dark fw-bold mb-4" style="font-family: 'Poppins', sans-serif;">
                <i class="fa-solid fa-life-ring text-primary me-2"></i>Submit a Ticket
            </h5>
            
            <form id="helpdeskForm" action="<?= get_base_url() ?>/api/employee_actions.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="submit_ticket">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem; text-transform: uppercase;">Issue Category</label>
                    <select name="category" class="form-select bg-light border-0 py-2" required>
                        <option value="">Select Category</option>
                        <option value="IT Support">IT Support (Hardware/Software)</option>
                        <option value="HR Inquiry">HR & Payroll Inquiry</option>
                        <option value="Portal Access">Portal Access Issue</option>
                        <option value="Other">Other Request</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem; text-transform: uppercase;">Subject</label>
                    <input type="text" name="subject" class="form-control bg-light border-0 py-2" placeholder="Briefly describe the issue" required>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem; text-transform: uppercase;">Description</label>
                    <textarea name="description" class="form-control bg-light border-0 py-2" rows="5" placeholder="Provide detailed information..." required></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem; text-transform: uppercase;">Attachment (Optional)</label>
                    <input type="file" name="attachment" class="form-control bg-light border-0 py-2">
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm rounded-pill">
                    <i class="fa-solid fa-paper-plane me-2"></i>Send Request
                </button>
            </form>
        </div>
    </div>

    <!-- Ticket History Column -->
    <div class="col-lg-7">
        <div class="card-modern shadow-sm border-0 p-4 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="text-dark fw-bold mb-0" style="font-family: 'Poppins', sans-serif;">My Tickets</h5>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill"><?= count($tickets) ?> Total</span>
            </div>
            
            <div class="list-group list-group-flush">
                <?php if (empty($tickets)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fa-regular fa-folder-open fa-2x mb-2 text-light"></i>
                        <p>No support tickets found.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($tickets as $t): 
                        $statusClass = 'bg-secondary';
                        $statusIcon = 'fa-circle-info';
                        if ($t['status'] === 'Pending') {
                            $statusClass = 'bg-primary';
                            $statusIcon = 'fa-clock';
                        } elseif ($t['status'] === 'In Progress') {
                            $statusClass = 'bg-warning text-dark';
                            $statusIcon = 'fa-spinner fa-spin';
                        } elseif ($t['status'] === 'Resolved') {
                            $statusClass = 'bg-success';
                            $statusIcon = 'fa-check';
                        } elseif ($t['status'] === 'Closed') {
                            $statusClass = 'bg-dark';
                            $statusIcon = 'fa-lock';
                        }
                    ?>
                    <div class="list-group-item px-0 py-3 border-bottom border-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge <?= $statusClass ?> rounded-pill shadow-sm px-3"><i class="fa-solid <?= $statusIcon ?> me-1"></i> <?= htmlspecialchars($t['status']) ?></span>
                            <small class="text-muted fw-bold">#<?= htmlspecialchars($t['ticket_number']) ?></small>
                        </div>
                        <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($t['subject']) ?></h6>
                        <p class="text-muted small mb-2 text-truncate"><?= htmlspecialchars($t['description']) ?></p>
                        <div class="d-flex align-items-center text-muted" style="font-size: 0.75rem;">
                            <span class="me-3"><i class="fa-solid fa-layer-group me-1"></i> <?= htmlspecialchars($t['category']) ?></span>
                            <span><i class="fa-regular fa-clock me-1"></i> <?= date('M d, Y H:i', strtotime($t['created_at'])) ?></span>
                            <?php if ($t['attachment']): ?>
                                <span class="ms-3"><i class="fa-solid fa-paperclip me-1"></i> Attached</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const helpdeskForm = document.getElementById('helpdeskForm');
    if (helpdeskForm) {
        helpdeskForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Sending...';
            btn.disabled = true;
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message || 'An error occurred.');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('A network error occurred.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    }
});
</script>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
