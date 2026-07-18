<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/auth.php';
auth_enforce(['Super Admin', 'Admin/HR']);

$pageTitle = "Email Templates";
require_once __DIR__ . '/../../includes/dashboard_header.php';
require_once __DIR__ . '/../../includes/utils.php';

$pdo = get_db_connection();
$message = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'create' || $action === 'update') {
            $name = trim($_POST['name'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $body = trim($_POST['body'] ?? '');
            $variables = trim($_POST['variables'] ?? '');
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            if (empty($name) || empty($subject) || empty($body)) {
                $error = "Name, subject, and body are required.";
            } else {
                if ($action === 'create') {
                    $stmt = $pdo->prepare("INSERT INTO email_templates (name, subject, body, variables, is_active) VALUES (?, ?, ?, ?, ?)");
                    if ($stmt->execute([$name, $subject, $body, $variables, $is_active])) {
                        $message = "Template created successfully.";
                    }
                } else {
                    $id = (int)($_POST['id'] ?? 0);
                    $stmt = $pdo->prepare("UPDATE email_templates SET name=?, subject=?, body=?, variables=?, is_active=? WHERE id=?");
                    if ($stmt->execute([$name, $subject, $body, $variables, $is_active, $id])) {
                        $message = "Template updated successfully.";
                    }
                }
            }
        } elseif ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare("DELETE FROM email_templates WHERE id=?")->execute([$id]);
            $message = "Template deleted.";
        } elseif ($action === 'test_email') {
            $id = (int)($_POST['id'] ?? 0);
            $test_email = trim($_POST['test_email'] ?? '');
            $stmt = $pdo->prepare("SELECT * FROM email_templates WHERE id=?");
            $stmt->execute([$id]);
            $template = $stmt->fetch();
            
            if ($template && !empty($test_email)) {
                // Parse dummy variables
                $body = $template['body'];
                $subject = $template['subject'];
                // Regex to find {vars} and replace with [Test Var]
                $body = preg_replace('/\{[a-zA-Z0-9_]+\}/', '[Test]', $body);
                $subject = preg_replace('/\{[a-zA-Z0-9_]+\}/', '[Test]', $subject);
                
                if (send_notification_email($test_email, $subject, $body)) {
                    $message = "Test email sent to $test_email";
                } else {
                    $error = "Failed to send test email. Check logs.";
                }
            }
        }
    }
}

// Fetch all templates
$templates = $pdo->query("SELECT * FROM email_templates ORDER BY name ASC")->fetchAll();
?>

<!-- Include Quill Styles -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0 text-primary"><i class="fa-solid fa-envelope"></i> Email Templates</h2>
    <button class="btn btn-primary" onclick="openTemplateModal()">
        <i class="fa-solid fa-plus"></i> New Template
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
                        <th>Template Name</th>
                        <th>Subject</th>
                        <th>Variables</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($templates as $tpl): ?>
                        <tr>
                            <td><strong><?= e($tpl['name']) ?></strong></td>
                            <td><?= e($tpl['subject']) ?></td>
                            <td><span class="text-muted small"><?= e($tpl['variables'] ?? 'None') ?></span></td>
                            <td>
                                <?php if ($tpl['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Disabled</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-info me-1" onclick="openTestModal(<?= $tpl['id'] ?>)">
                                    <i class="fa-solid fa-paper-plane"></i> Test
                                </button>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="openTemplateModal(<?= htmlspecialchars(json_encode($tpl)) ?>)">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Delete this template?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $tpl['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($templates)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">No templates found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Template Editor Modal -->
<div class="modal fade" id="templateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="templateForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="templateModalLabel">Email Template</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="id" id="templateId" value="">
                    
                    <div class="mb-3">
                        <label class="form-label">Template Name</label>
                        <input type="text" name="name" id="templateName" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" id="templateSubject" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Allowed Variables (comma separated)</label>
                        <input type="text" name="variables" id="templateVariables" class="form-control" placeholder="{company_name}, {employee_name}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Body Content</label>
                        <div id="editor-container" style="height: 250px; background: #fff;"></div>
                        <!-- Hidden input to hold the HTML content from Quill -->
                        <input type="hidden" name="body" id="templateBody">
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="templateActive" value="1" checked>
                        <label class="form-check-label" for="templateActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Template</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Test Email Modal -->
<div class="modal fade" id="testEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Send Test Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="test_email">
                    <input type="hidden" name="id" id="testTemplateId">
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="test_email" class="form-control" required value="<?= e($currentUser['email']) ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Send Test</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include Quill script -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        placeholder: 'Write your email template here...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'header': 1 }, { 'header': 2 }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'color': [] }, { 'background': [] }],
                ['link', 'clean']
            ]
        }
    });

    // Populate hidden field on form submit
    document.getElementById('templateForm').onsubmit = function() {
        var html = document.querySelector('#editor-container .ql-editor').innerHTML;
        document.getElementById('templateBody').value = html;
    };

    function openTemplateModal(template = null) {
        if (template) {
            document.getElementById('templateModalLabel').innerText = 'Edit Template';
            document.getElementById('formAction').value = 'update';
            document.getElementById('templateId').value = template.id;
            document.getElementById('templateName').value = template.name;
            document.getElementById('templateSubject').value = template.subject;
            document.getElementById('templateVariables').value = template.variables;
            document.getElementById('templateActive').checked = parseInt(template.is_active) === 1;
            
            // Set Quill content
            document.querySelector('#editor-container .ql-editor').innerHTML = template.body;
        } else {
            document.getElementById('templateModalLabel').innerText = 'New Template';
            document.getElementById('formAction').value = 'create';
            document.getElementById('templateId').value = '';
            document.getElementById('templateName').value = '';
            document.getElementById('templateSubject').value = '';
            document.getElementById('templateVariables').value = '';
            document.getElementById('templateActive').checked = true;
            document.querySelector('#editor-container .ql-editor').innerHTML = '';
        }
        var modal = new bootstrap.Modal(document.getElementById('templateModal'));
        modal.show();
    }
    
    function openTestModal(id) {
        document.getElementById('testTemplateId').value = id;
        var modal = new bootstrap.Modal(document.getElementById('testEmailModal'));
        modal.show();
    }
</script>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
