<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/auth.php';
auth_enforce(['Super Admin', 'Admin/HR']);

$pageTitle = "Event Management";
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
            $description = trim($_POST['description'] ?? '');
            $event_type = trim($_POST['event_type'] ?? 'Meeting');
            $event_date = $_POST['event_date'] ?? '';
            $event_time = $_POST['event_time'] ?? null;
            $location = trim($_POST['location'] ?? '');
            $meeting_link = trim($_POST['meeting_link'] ?? '');
            $requires_rsvp = isset($_POST['requires_rsvp']) ? 1 : 0;
            
            if (empty($title) || empty($event_date)) {
                $error = "Title and Event Date are required.";
            } else {
                $banner = null;
                if ($action === 'update') {
                    $banner = $_POST['existing_banner'] ?? null;
                }

                if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../../uploads/events/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $filename = uniqid('evt_') . '_' . basename($_FILES['banner']['name']);
                    if (move_uploaded_file($_FILES['banner']['tmp_name'], $uploadDir . $filename)) {
                        $banner = 'uploads/events/' . $filename;
                    }
                }

                if ($action === 'create') {
                    $stmt = $pdo->prepare("INSERT INTO events (title, description, event_type, event_date, event_time, location, meeting_link, banner, requires_rsvp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    if ($stmt->execute([$title, $description, $event_type, $event_date, $event_time, $location, $meeting_link, $banner, $requires_rsvp])) {
                        $message = "Event created successfully.";
                    }
                } else {
                    $id = (int)($_POST['id'] ?? 0);
                    $stmt = $pdo->prepare("UPDATE events SET title=?, description=?, event_type=?, event_date=?, event_time=?, location=?, meeting_link=?, banner=?, requires_rsvp=? WHERE id=?");
                    if ($stmt->execute([$title, $description, $event_type, $event_date, $event_time, $location, $meeting_link, $banner, $requires_rsvp, $id])) {
                        $message = "Event updated successfully.";
                    }
                }
            }
        } elseif ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare("DELETE FROM events WHERE id=?")->execute([$id]);
            $message = "Event deleted.";
        }
    }
}

// Fetch events
$events = $pdo->query("SELECT * FROM events ORDER BY event_date ASC, event_time ASC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0 text-primary"><i class="fa-solid fa-calendar-star"></i> Event Notifications</h2>
    <button class="btn btn-primary" onclick="openEventModal()">
        <i class="fa-solid fa-plus"></i> Create Event
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
                        <th>Event</th>
                        <th>Type</th>
                        <th>Date & Time</th>
                        <th>Location / Link</th>
                        <th>RSVP</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $evt): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if ($evt['banner']): ?>
                                        <img src="<?= get_base_url() . '/' . e($evt['banner']) ?>" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted me-3" style="width: 50px; height: 50px;">
                                            <i class="fa-solid fa-calendar"></i>
                                        </div>
                                    <?php endif; ?>
                                    <strong><?= e($evt['title']) ?></strong>
                                </div>
                            </td>
                            <td><span class="badge bg-info"><?= e($evt['event_type']) ?></span></td>
                            <td>
                                <i class="fa-regular fa-calendar text-muted"></i> <?= e($evt['event_date']) ?><br>
                                <small class="text-muted"><i class="fa-regular fa-clock"></i> <?= e($evt['event_time'] ?? 'All Day') ?></small>
                            </td>
                            <td>
                                <?php if ($evt['location']): ?>
                                    <div class="small"><i class="fa-solid fa-location-dot text-danger"></i> <?= e($evt['location']) ?></div>
                                <?php endif; ?>
                                <?php if ($evt['meeting_link']): ?>
                                    <div class="small"><a href="<?= e($evt['meeting_link']) ?>" target="_blank"><i class="fa-solid fa-video text-primary"></i> Online Meeting</a></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($evt['requires_rsvp']): ?>
                                    <span class="badge bg-primary">Required</span>
                                <?php else: ?>
                                    <span class="text-muted small">No</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" onclick="openEventModal(<?= htmlspecialchars(json_encode($evt)) ?>)">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Delete this event?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $evt['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($events)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No events found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Event Editor Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="eventModalLabel">Create Event</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="id" id="evtId" value="">
                    <input type="hidden" name="existing_banner" id="existingBanner" value="">
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Event Title</label>
                            <input type="text" name="title" id="evtTitle" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Event Type</label>
                            <select name="event_type" id="evtType" class="form-select">
                                <option value="Meeting">Meeting</option>
                                <option value="Training">Training</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Webinar">Webinar</option>
                                <option value="Celebration">Celebration</option>
                                <option value="Outing">Team Outing</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="evtDesc" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <input type="date" name="event_date" id="evtDate" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Time</label>
                            <input type="time" name="event_time" id="evtTime" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Location (Physical)</label>
                            <input type="text" name="location" id="evtLocation" class="form-control" placeholder="E.g. Conference Room A">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Online Meeting Link</label>
                            <input type="url" name="meeting_link" id="evtLink" class="form-control" placeholder="https://zoom.us/j/1234">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Event Banner Image</label>
                            <input type="file" name="banner" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-4 d-flex align-items-center mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="requires_rsvp" id="evtRsvp" value="1">
                                <label class="form-check-label" for="evtRsvp">Requires RSVP</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEventModal(evt = null) {
    if (evt) {
        document.getElementById('eventModalLabel').innerText = 'Edit Event';
        document.getElementById('formAction').value = 'update';
        document.getElementById('evtId').value = evt.id;
        document.getElementById('evtTitle').value = evt.title;
        document.getElementById('evtType').value = evt.event_type;
        document.getElementById('evtDesc').value = evt.description;
        document.getElementById('evtDate').value = evt.event_date;
        document.getElementById('evtTime').value = evt.event_time || '';
        document.getElementById('evtLocation').value = evt.location || '';
        document.getElementById('evtLink').value = evt.meeting_link || '';
        document.getElementById('evtRsvp').checked = parseInt(evt.requires_rsvp) === 1;
        document.getElementById('existingBanner').value = evt.banner || '';
    } else {
        document.getElementById('eventModalLabel').innerText = 'Create Event';
        document.getElementById('formAction').value = 'create';
        document.getElementById('evtId').value = '';
        document.getElementById('evtTitle').value = '';
        document.getElementById('evtType').value = 'Meeting';
        document.getElementById('evtDesc').value = '';
        document.getElementById('evtDate').value = '';
        document.getElementById('evtTime').value = '';
        document.getElementById('evtLocation').value = '';
        document.getElementById('evtLink').value = '';
        document.getElementById('evtRsvp').checked = false;
        document.getElementById('existingBanner').value = '';
    }
    new bootstrap.Modal(document.getElementById('eventModal')).show();
}
</script>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
