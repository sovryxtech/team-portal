<?php
declare(strict_types=1);

$pageTitle = "Contact Support Messages";
require_once __DIR__ . '/../../includes/dashboard_header.php';
require_once __DIR__ . '/../../includes/utils.php';

// Verify admin role
if (!auth_has_role(['Super Admin', 'Admin/HR'])) {
    header('Location: ' . get_base_url() . '/login.php');
    exit;
}

$pdo = get_db_connection();

// Handle Delete Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_message') {
    if (!csrf_verify()) {
        $error = "CSRF verification failed.";
    } else {
        $msgId = (int)$_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = :id");
        $stmt->execute(['id' => $msgId]);
        $_SESSION['success_flash'] = "Support message deleted successfully.";
        header("Location: messages.php");
        exit;
    }
}

// Fetch messages
$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY id DESC")->fetchAll();
?>

<!-- Flash Messages -->
<?php if (isset($_SESSION['success_flash'])): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($_SESSION['success_flash']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success_flash']); ?>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="fa-solid fa-circle-xmark me-2"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card-custom p-4 bg-white shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="text-primary mb-1">Support Portal Submissions</h5>
            <p class="text-muted small mb-0">View and manage comments submitted by guests and candidates from the public contact form.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle" id="messagesTable">
            <thead>
                <tr>
                    <th>Submitted At</th>
                    <th>Sender Name</th>
                    <th>Email Address</th>
                    <th>Subject Title</th>
                    <th>Snippet</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr>
                        <td class="text-muted small"><?= date('Y-m-d H:i:s', strtotime($msg['created_at'])) ?></td>
                        <td><strong><?= htmlspecialchars($msg['name']) ?></strong></td>
                        <td><a href="mailto:<?= htmlspecialchars($msg['email']) ?>" class="text-decoration-none text-secondary"><i class="fa-solid fa-envelope me-1"></i><?= htmlspecialchars($msg['email']) ?></a></td>
                        <td><span class="badge bg-secondary-custom text-primary font-weight-bold"><?= htmlspecialchars($msg['subject']) ?></span></td>
                        <td><span class="text-truncate d-inline-block" style="max-width: 250px;"><?= htmlspecialchars($msg['message']) ?></span></td>
                        <td class="text-end">
                            <button class="btn btn-outline-primary btn-sm view-msg-btn" 
                                    data-name="<?= htmlspecialchars($msg['name']) ?>" 
                                    data-email="<?= htmlspecialchars($msg['email']) ?>" 
                                    data-subject="<?= htmlspecialchars($msg['subject']) ?>" 
                                    data-date="<?= date('Y-m-d H:i:s', strtotime($msg['created_at'])) ?>" 
                                    data-message="<?= htmlspecialchars($msg['message']) ?>">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm delete-msg-btn" data-id="<?= $msg['id'] ?>"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- View Message Modal -->
<div class="modal fade" id="viewMessageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title font-weight-bold" id="modalSubject">Support Inquiry</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3 border-bottom pb-2">
                    <p class="mb-1 text-muted small">From</p>
                    <h6 class="mb-0 font-weight-bold" id="modalSender">Sender Name</h6>
                    <a href="#" class="small text-decoration-none" id="modalEmail">sender@email.com</a>
                </div>
                <div class="mb-3 border-bottom pb-2">
                    <p class="mb-1 text-muted small">Date Received</p>
                    <span class="small font-weight-bold" id="modalDate">2026-07-17</span>
                </div>
                <div>
                    <p class="mb-1 text-muted small">Message Text</p>
                    <p class="bg-light p-3 rounded-3 text-secondary" style="white-space: pre-wrap;" id="modalBody">Message body goes here.</p>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-light">
                <a href="#" class="btn btn-primary px-4" id="modalReplyBtn"><i class="fa-solid fa-reply me-1"></i>Reply via Mail</a>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteMessageForm" method="POST" action="messages.php" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="delete_message">
    <input type="hidden" name="id" id="deleteMsgId">
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
    $('#messagesTable').DataTable({
        order: [[0, 'desc']],
        language: {
            searchPlaceholder: "Search submissions..."
        }
    });

    // View Message
    $('.view-msg-btn').on('click', function() {
        var name = $(this).data('name');
        var email = $(this).data('email');
        var subject = $(this).data('subject');
        var date = $(this).data('date');
        var message = $(this).data('message');

        $('#modalSubject').text(subject);
        $('#modalSender').text(name);
        $('#modalEmail').text(email).attr('href', 'mailto:' + email);
        $('#modalDate').text(date);
        $('#modalBody').text(message);
        $('#modalReplyBtn').attr('href', 'mailto:' + email + '?subject=RE: ' + encodeURIComponent(subject));

        $('#viewMessageModal').modal('show');
    });

    // Delete Message
    $('.delete-msg-btn').on('click', function() {
        var id = $(this).data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently remove this support message from the audit trail!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#475569',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#deleteMsgId').val(id);
                $('#deleteMessageForm').submit();
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
