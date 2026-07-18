<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/auth.php';
auth_enforce(['Employee']);

$pageTitle = "Communications Hub";
require_once __DIR__ . '/../../includes/dashboard_header.php';
require_once __DIR__ . '/../../includes/utils.php';

$pdo = get_db_connection();
$currentUser = auth_user();

// Get employee details (branch_id, department_id)
$stmt = $pdo->prepare("SELECT branch_id, department_id FROM employees WHERE user_id = ?");
$stmt->execute([$currentUser['id']]);
$emp = $stmt->fetch();

$branch_id = $emp ? $emp['branch_id'] : null;
$department_id = $emp ? $emp['department_id'] : null;

// Fetch Announcements
// Applicable if global (no branch/dept), or matches branch, or matches dept.
$annStmt = $pdo->prepare("
    SELECT * FROM announcements 
    WHERE (expiry_date IS NULL OR expiry_date >= CURDATE())
      AND (
          (branch_id IS NULL AND department_id IS NULL)
          OR (branch_id = :b_id)
          OR (department_id = :d_id)
      )
    ORDER BY is_pinned DESC, created_at DESC
");
$annStmt->execute(['b_id' => $branch_id, 'd_id' => $department_id]);
$announcements = $annStmt->fetchAll();

// Fetch News
$newsStmt = $pdo->query("SELECT * FROM company_news ORDER BY published_date DESC LIMIT 10");
$news_items = $newsStmt->fetchAll();

// Fetch upcoming Events
$eventsStmt = $pdo->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC, event_time ASC LIMIT 5");
$events = $eventsStmt->fetchAll();
?>

<div class="row g-4">
    <!-- Main Column: Announcements & News -->
    <div class="col-lg-8">
        
        <!-- Announcements Section -->
        <h4 class="text-primary mb-3"><i class="fa-solid fa-bell"></i> Announcements</h4>
        <?php if (empty($announcements)): ?>
            <div class="alert alert-light text-muted border-0 shadow-sm">No new announcements at this time.</div>
        <?php else: ?>
            <div class="accordion mb-4 shadow-sm" id="announcementsAccordion">
                <?php foreach ($announcements as $index => $ann): 
                    $badgeColor = 'secondary';
                    if ($ann['priority'] === 'High') $badgeColor = 'danger';
                    if ($ann['priority'] === 'Medium') $badgeColor = 'warning';
                    if ($ann['priority'] === 'Low') $badgeColor = 'info';
                ?>
                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header" id="heading<?= $ann['id'] ?>">
                            <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?> bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $ann['id'] ?>">
                                <div class="d-flex align-items-center w-100 me-3">
                                    <?php if ($ann['is_pinned']): ?>
                                        <i class="fa-solid fa-thumbtack text-danger me-2"></i>
                                    <?php endif; ?>
                                    <strong class="me-auto text-primary"><?= e($ann['title']) ?></strong>
                                    <span class="badge bg-<?= $badgeColor ?>"><?= e($ann['priority']) ?></span>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse<?= $ann['id'] ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#announcementsAccordion">
                            <div class="accordion-body text-secondary bg-light rounded-bottom">
                                <div class="small text-muted mb-2"><i class="fa-regular fa-clock"></i> Posted on <?= date('M d, Y', strtotime($ann['created_at'])) ?></div>
                                <?= nl2br(e($ann['content'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Company News Section -->
        <h4 class="text-primary mt-5 mb-3"><i class="fa-solid fa-newspaper"></i> Company News</h4>
        <div class="row g-4">
            <?php foreach ($news_items as $news): ?>
            <div class="col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <?php if ($news['featured_image']): ?>
                        <img src="<?= get_base_url() . '/' . e($news['featured_image']) ?>" class="card-img-top" alt="News Image" style="height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center text-muted" style="height: 150px;">
                            <i class="fa-solid fa-image fa-2x"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><?= e($news['category']) ?></span>
                            <?php if ($news['is_featured']): ?>
                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-star"></i> Featured</span>
                            <?php endif; ?>
                        </div>
                        <h5 class="card-title text-primary h6 fw-bold"><?= e($news['title']) ?></h5>
                        <p class="text-muted small mb-2"><i class="fa-solid fa-calendar"></i> <?= date('M d, Y', strtotime($news['published_date'])) ?></p>
                        <button class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#newsModal<?= $news['id'] ?>">Read More</button>
                    </div>
                </div>
            </div>
            
            <!-- News Modal -->
            <div class="modal fade" id="newsModal<?= $news['id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header border-0 pb-0">
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body pt-0 px-4 pb-4">
                            <?php if ($news['featured_image']): ?>
                                <img src="<?= get_base_url() . '/' . e($news['featured_image']) ?>" class="img-fluid rounded mb-3 w-100" style="max-height: 300px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="mb-3">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-2"><?= e($news['category']) ?></span>
                                <span class="text-muted small"><i class="fa-solid fa-calendar"></i> <?= date('M d, Y', strtotime($news['published_date'])) ?></span>
                            </div>
                            <h3 class="text-primary mb-4"><?= e($news['title']) ?></h3>
                            <div class="text-secondary" style="line-height: 1.6;">
                                <?= $news['content'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($news_items)): ?>
                <div class="col-12"><div class="alert alert-light text-muted border-0 shadow-sm">No company news published yet.</div></div>
            <?php endif; ?>
        </div>

    </div>

    <!-- Sidebar Column: Events -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="text-primary mb-0"><i class="fa-solid fa-calendar-star me-2"></i>Upcoming Events</h5>
            </div>
            <div class="card-body">
                <?php if (empty($events)): ?>
                    <div class="text-muted small text-center py-3">No upcoming events scheduled.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($events as $evt): ?>
                            <div class="list-group-item px-0 py-3 border-bottom">
                                <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 text-primary fw-bold"><?= e($evt['title']) ?></h6>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle"><?= e($evt['event_type']) ?></span>
                                </div>
                                <div class="small text-muted mb-2">
                                    <div><i class="fa-regular fa-calendar me-1"></i><?= date('M d, Y', strtotime($evt['event_date'])) ?></div>
                                    <?php if ($evt['event_time']): ?>
                                        <div><i class="fa-regular fa-clock me-1"></i><?= date('h:i A', strtotime($evt['event_time'])) ?></div>
                                    <?php endif; ?>
                                </div>
                                <?php if ($evt['location']): ?>
                                    <div class="small mb-1"><i class="fa-solid fa-location-dot text-danger me-1"></i><?= e($evt['location']) ?></div>
                                <?php endif; ?>
                                <?php if ($evt['meeting_link']): ?>
                                    <div class="small mb-2"><a href="<?= e($evt['meeting_link']) ?>" target="_blank" class="text-decoration-none"><i class="fa-solid fa-video me-1"></i>Join Online</a></div>
                                <?php endif; ?>
                                
                                <?php if ($evt['requires_rsvp']): ?>
                                    <button class="btn btn-sm btn-primary w-100 mt-2" onclick="alert('RSVP functionality coming soon!')"><i class="fa-solid fa-check me-1"></i> RSVP Now</button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
