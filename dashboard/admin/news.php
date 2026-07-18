<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/auth.php';
auth_enforce(['Super Admin', 'Admin/HR']);

$pageTitle = "Company News";
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
            $category = trim($_POST['category'] ?? 'General');
            $published_date = !empty($_POST['published_date']) ? $_POST['published_date'] : date('Y-m-d');
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            
            if (empty($title) || empty($content)) {
                $error = "Title and Content are required.";
            } else {
                // Handle file upload
                $featured_image = null;
                if ($action === 'update') {
                    $featured_image = $_POST['existing_image'] ?? null;
                }

                if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../../uploads/news/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $filename = uniqid('news_') . '_' . basename($_FILES['featured_image']['name']);
                    $targetPath = $uploadDir . $filename;
                    
                    if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $targetPath)) {
                        $featured_image = 'uploads/news/' . $filename;
                    }
                }

                if ($action === 'create') {
                    $stmt = $pdo->prepare("INSERT INTO company_news (title, content, category, featured_image, published_date, is_featured) VALUES (?, ?, ?, ?, ?, ?)");
                    if ($stmt->execute([$title, $content, $category, $featured_image, $published_date, $is_featured])) {
                        $message = "News published successfully.";
                    }
                } else {
                    $id = (int)($_POST['id'] ?? 0);
                    $stmt = $pdo->prepare("UPDATE company_news SET title=?, content=?, category=?, featured_image=?, published_date=?, is_featured=? WHERE id=?");
                    if ($stmt->execute([$title, $content, $category, $featured_image, $published_date, $is_featured, $id])) {
                        $message = "News updated successfully.";
                    }
                }
            }
        } elseif ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare("DELETE FROM company_news WHERE id=?")->execute([$id]);
            $message = "News deleted.";
        }
    }
}

// Fetch news
$news_items = $pdo->query("SELECT * FROM company_news ORDER BY published_date DESC, created_at DESC")->fetchAll();
?>

<!-- Include Quill Styles -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0 text-primary"><i class="fa-solid fa-newspaper"></i> Company News</h2>
    <button class="btn btn-primary" onclick="openNewsModal()">
        <i class="fa-solid fa-plus"></i> Publish News
    </button>
</div>

<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= e($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= e($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="row">
    <?php foreach ($news_items as $news): ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm border-0">
            <?php if ($news['featured_image']): ?>
                <img src="<?= get_base_url() . '/' . e($news['featured_image']) ?>" class="card-img-top" alt="News Image" style="height: 180px; object-fit: cover;">
            <?php else: ?>
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center text-muted" style="height: 180px;">
                    <i class="fa-solid fa-image fa-3x"></i>
                </div>
            <?php endif; ?>
            
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><?= e($news['category']) ?></span>
                    <?php if ($news['is_featured']): ?>
                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-star"></i> Featured</span>
                    <?php endif; ?>
                </div>
                <h5 class="card-title text-primary"><?= e($news['title']) ?></h5>
                <p class="text-muted small mb-3"><i class="fa-solid fa-calendar"></i> <?= e($news['published_date']) ?></p>
                <div class="card-text text-muted" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                    <?= strip_tags($news['content']) ?>
                </div>
            </div>
            <div class="card-footer bg-white text-end border-top-0">
                <button class="btn btn-sm btn-outline-primary me-1" onclick="openNewsModal(<?= htmlspecialchars(json_encode($news)) ?>)">
                    <i class="fa-solid fa-edit"></i> Edit
                </button>
                <form method="POST" class="d-inline" onsubmit="return confirm('Delete this news?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $news['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- News Editor Modal -->
<div class="modal fade" id="newsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="POST" id="newsForm" enctype="multipart/form-data">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="newsModalLabel">News Article</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="id" id="newsId" value="">
                    <input type="hidden" name="existing_image" id="existingImage" value="">
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">News Title</label>
                            <input type="text" name="title" id="newsTitle" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category</label>
                            <input type="text" name="category" id="newsCategory" class="form-control" placeholder="e.g., General, Awards, Events" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Published Date</label>
                            <input type="date" name="published_date" id="newsDate" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Featured Image</label>
                            <input type="file" name="featured_image" class="form-control" accept="image/*">
                            <small class="text-muted">Leave empty to keep existing image</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="newsFeatured" value="1">
                            <label class="form-check-label text-warning" for="newsFeatured"><i class="fa-solid fa-star"></i> Mark as Featured News</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">News Content</label>
                        <div id="editor-container" style="height: 350px; background: #fff;"></div>
                        <input type="hidden" name="content" id="newsContent">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Publish News</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        placeholder: 'Write the news content here...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'header': [1, 2, 3, false] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'color': [] }, { 'background': [] }],
                ['link', 'image', 'video', 'clean']
            ]
        }
    });

    document.getElementById('newsForm').onsubmit = function() {
        var html = document.querySelector('#editor-container .ql-editor').innerHTML;
        document.getElementById('newsContent').value = html;
    };

    function openNewsModal(news = null) {
        if (news) {
            document.getElementById('newsModalLabel').innerText = 'Edit News Article';
            document.getElementById('formAction').value = 'update';
            document.getElementById('newsId').value = news.id;
            document.getElementById('newsTitle').value = news.title;
            document.getElementById('newsCategory').value = news.category;
            document.getElementById('newsDate').value = news.published_date;
            document.getElementById('newsFeatured').checked = parseInt(news.is_featured) === 1;
            document.getElementById('existingImage').value = news.featured_image || '';
            document.querySelector('#editor-container .ql-editor').innerHTML = news.content;
        } else {
            document.getElementById('newsModalLabel').innerText = 'Publish News';
            document.getElementById('formAction').value = 'create';
            document.getElementById('newsId').value = '';
            document.getElementById('newsTitle').value = '';
            document.getElementById('newsCategory').value = 'General';
            
            // Set today's date
            const today = new Date();
            const yyyy = today.getFullYear();
            let mm = today.getMonth() + 1; // Months start at 0!
            let dd = today.getDate();
            if (dd < 10) dd = '0' + dd;
            if (mm < 10) mm = '0' + mm;
            document.getElementById('newsDate').value = yyyy + '-' + mm + '-' + dd;
            
            document.getElementById('newsFeatured').checked = false;
            document.getElementById('existingImage').value = '';
            document.querySelector('#editor-container .ql-editor').innerHTML = '';
        }
        new bootstrap.Modal(document.getElementById('newsModal')).show();
    }
</script>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
