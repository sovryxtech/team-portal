<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/auth.php';
auth_enforce(['Super Admin', 'Admin/HR']);

$pageTitle = "Announcements";
require_once __DIR__ . '/../../includes/dashboard_header.php';
require_once __DIR__ . '/../../includes/utils.php';

$pdo = get_db_connection();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'create' || $action === 'update') {
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $priority = $_POST['priority'] ?? 'Medium';
            $is_pinned = isset($_POST['is_pinned']) ? 1 : 0;
            $department_id = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;
            $branch_id = !empty($_POST['branch_id']) ? (int)$_POST['branch_id'] : null;
            $expiry_date = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;
            
            if (empty($title) || empty($content)) {
                $error = "Title and Content are required.";
            } else {
                if ($action === 'create') {
                    $stmt = $pdo->prepare("INSERT INTO announcements (title, content, priority, is_pinned, department_id, branch_id, expiry_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    if ($stmt->execute([$title, $content, $priority, $is_pinned, $department_id, $branch_id, $expiry_date])) {
                        $message = "Announcement published successfully.";
                    }
                } else {
                    $id = (int)($_POST['id'] ?? 0);
                    $stmt = $pdo->prepare("UPDATE announcements SET title=?, content=?, priority=?, is_pinned=?, department_id=?, branch_id=?, expiry_date=? WHERE id=?");
                    if ($stmt->execute([$title, $content, $priority, $is_pinned, $department_id, $branch_id, $expiry_date, $id])) {
                        $message = "Announcement updated successfully.";
                    }
                }
            }
        } elseif ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare("DELETE FROM announcements WHERE id=?")->execute([$id]);
            $message = "Announcement deleted.";
        }
    }
}

// Fetch lists for dropdowns
$departments = $pdo->query("SELECT id, name FROM departments")->fetchAll();
$branches = $pdo->query("SELECT id, name FROM branches")->fetchAll();

// Fetch announcements
$announcements = $pdo->query("
    SELECT a.*, d.name as dept_name, b.name as branch_name 
    FROM announcements a 
    LEFT JOIN departments d ON a.department_id = d.id 
    LEFT JOIN branches b ON a.branch_id = b.id 
    ORDER BY a.is_pinned DESC, a.created_at DESC
")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0 text-primary"><i class="fa-solid fa-bell"></i> Internal Announcements</h2>
    <button class="btn btn-primary" onclick="openAnnouncementModal()">
        <i class="fa-solid fa-plus"></i> New Announcement
    </button>
</div>

<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= e($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= e($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Target</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Expiry</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $ann): ?>
                        <?php 
                        $badgeColor = 'secondary';
                        if ($ann['priority'] === 'High') $badgeColor = 'danger';
                        if ($ann['priority'] === 'Medium') $badgeColor = 'warning';
                        if ($ann['priority'] === 'Low') $badgeColor = 'info';
                        ?>
                        <tr>
                            <td>
                                <?php if ($ann['is_pinned']): ?>
                                    <i class="fa-solid fa-thumbtack text-danger me-1"></i>
                                <?php endif; ?>
                                <strong><?= e($ann['title']) ?></strong>
                            </td>
                            <td>
                                <?php
                                if (!$ann['department_id'] && !$ann['branch_id']) {
                                    echo '<span class="badge bg-success">Everyone</span>';
                                } else {
                                    if ($ann['branch_name']) echo '<span class="badge bg-primary">Branch: '.e($ann['branch_name']).'</span> ';
                                    if ($ann['dept_name']) echo '<span class="badge bg-info">Dept: '.e($ann['dept_name']).'</span>';
                                }
                                ?>
                            </td>
                            <td><span class="badge bg-<?= $badgeColor ?>"><?= e($ann['priority']) ?></span></td>
                            <td>
                                <?php if ($ann['expiry_date'] && strtotime($ann['expiry_date']) < time()): ?>
                                    <span class="text-muted"><i class="fa-solid fa-clock"></i> Expired</span>
                                <?php else: ?>
                                    <span class="text-success"><i class="fa-solid fa-check-circle"></i> Active</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $ann['expiry_date'] ? e($ann['expiry_date']) : 'Never' ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" onclick="openAnnouncementModal(<?= htmlspecialchars(json_encode($ann)) ?>)">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Delete this announcement?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $ann['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($announcements)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No announcements found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Announcement Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="announcementModalLabel">Announcement</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="id" id="annId" value="">
                    
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" id="annTitle" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content" id="annContent" class="form-control" rows="5" required></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Priority</label>
                            <select name="priority" id="annPriority" class="form-select">
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Target Branch (Optional)</label>
                            <select name="branch_id" id="annBranch" class="form-select">
                                <option value="">All Branches</option>
                                <?php foreach ($branches as $b): ?>
                                    <option value="<?= $b['id'] ?>"><?= e($b['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Target Department (Optional)</label>
                            <select name="department_id" id="annDept" class="form-select">
                                <option value="">All Departments</option>
                                <?php foreach ($departments as $d): ?>
                                    <option value="<?= $d['id'] ?>"><?= e($d['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Expiry Date (Optional)</label>
                            <input type="date" name="expiry_date" id="annExpiry" class="form-control">
                        </div>
                        <div class="col-md-6 d-flex align-items-center mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_pinned" id="annPinned" value="1">
                                <label class="form-check-label" for="annPinned"><i class="fa-solid fa-thumbtack text-danger"></i> Pin to top</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Announcement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAnnouncementModal(ann = null) {
    if (ann) {
        document.getElementById('announcementModalLabel').innerText = 'Edit Announcement';
        document.getElementById('formAction').value = 'update';
        document.getElementById('annId').value = ann.id;
        document.getElementById('annTitle').value = ann.title;
        document.getElementById('annContent').value = ann.content;
        document.getElementById('annPriority').value = ann.priority;
        document.getElementById('annBranch').value = ann.branch_id || '';
        document.getElementById('annDept').value = ann.department_id || '';
        document.getElementById('annExpiry').value = ann.expiry_date || '';
        document.getElementById('annPinned').checked = parseInt(ann.is_pinned) === 1;
    } else {
        document.getElementById('announcementModalLabel').innerText = 'New Announcement';
        document.getElementById('formAction').value = 'create';
        document.getElementById('annId').value = '';
        document.getElementById('annTitle').value = '';
        document.getElementById('annContent').value = '';
        document.getElementById('annPriority').value = 'Medium';
        document.getElementById('annBranch').value = '';
        document.getElementById('annDept').value = '';
        document.getElementById('annExpiry').value = '';
        document.getElementById('annPinned').checked = false;
    }
    new bootstrap.Modal(document.getElementById('announcementModal')).show();
}
</script>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
