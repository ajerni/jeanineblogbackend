<?php
// Include authentication check
require_once 'auth.php';

// Include database configuration
require_once 'config.php';

// Get all blog posts
try {
    $sql = "SELECT * FROM jeanine_blog_posts ORDER BY published_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $posts = $stmt->fetchAll();
} catch(PDOException $e) {
    die("ERROR: Could not execute query. " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Posts - Blog Backend</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .container { max-width: 1000px; }
        .post-image { max-width: 150px; max-height: 100px; object-fit: cover; }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Manage Blog Posts</h1>
            <div>
                <a href="index.php" class="btn btn-primary me-2">Add New Post</a>
                <a href="logout.php" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>
        
        <?php if(isset($_GET['status_changed'])): ?>
        <div class="alert alert-success">
            Post status updated successfully!
        </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['deleted'])): ?>
        <div class="alert alert-success">
            Post deleted successfully!
        </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            Error: <?= htmlspecialchars($_GET['error']) ?>
        </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['image_updated']) || isset($_GET['image_uploaded'])): ?>
        <div class="alert alert-success">
            Image updated successfully!
        </div>
        <?php endif; ?>
        
        <?php if(count($posts) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Image</th>
                        <th>Excerpt</th>
                        <th>Published Date</th>
                        <th>Tags</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($posts as $post): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($post['title']) ?></strong>
                            <div class="text-muted small">Slug: <?= htmlspecialchars($post['slug']) ?></div>
                        </td>
                        <td>
                            <img src="<?= htmlspecialchars($post['featured_image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="post-image">
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#changeImageModal<?= htmlspecialchars($post['id']) ?>">
                                Change Image
                            </button>
                            
                            <!-- Modal for Changing Image -->
                            <div class="modal fade" id="changeImageModal<?= htmlspecialchars($post['id']) ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Change Image for "<?= htmlspecialchars($post['title']) ?>"</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="update_image.php" method="post">
                                            <div class="modal-body">
                                                <input type="hidden" name="slug" value="<?= htmlspecialchars($post['slug']) ?>">
                                                
                                                <div class="mb-3">
                                                    <label for="current_image" class="form-label">Current Image:</label>
                                                    <div>
                                                        <img src="<?= htmlspecialchars($post['featured_image']) ?>" 
                                                             alt="Current Image" 
                                                             class="img-fluid mb-2" 
                                                             style="max-height: 150px;">
                                                    </div>
                                                    <div class="text-muted small">
                                                        <?= htmlspecialchars($post['featured_image']) ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="image_name" class="form-label">New Image Name:</label>
                                                    <input type="text" class="form-control" id="image_name_<?= htmlspecialchars($post['id']) ?>" name="image_name" 
                                                           placeholder="e.g. apfel" required>
                                                    <div class="form-text">
                                                        Will be used in: https://ik.imagekit.io/mywine/andiblog/tr:w-800,h-400/[image_name].jpg
                                                    </div>
                                                    <button type="button" class="btn btn-secondary mt-2" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#uploadImageModal<?= htmlspecialchars($post['id']) ?>">
                                                        Upload Image
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update Image</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Modal for Uploading Image -->
                            <div class="modal fade" id="uploadImageModal<?= htmlspecialchars($post['id']) ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Upload Image for "<?= htmlspecialchars($post['title']) ?>"</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="uploadAlert<?= htmlspecialchars($post['id']) ?>" class="alert d-none mb-3"></div>
                                            
                                            <form id="uploadImageForm<?= htmlspecialchars($post['id']) ?>" action="upload_image.php" method="post" enctype="multipart/form-data">
                                                <input type="hidden" name="slug" value="<?= htmlspecialchars($post['slug']) ?>">
                                                <input type="hidden" name="return_url" value="posts.php">
                                                
                                                <div class="mb-3">
                                                    <label for="direct_modal_image_name_<?= htmlspecialchars($post['id']) ?>" class="form-label">Image Name:</label>
                                                    <input type="text" class="form-control" id="direct_modal_image_name_<?= htmlspecialchars($post['id']) ?>" name="image_name" 
                                                           placeholder="e.g. apfel" required>
                                                    <div class="form-text">This will be the name of your uploaded image (without .jpg extension)</div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="direct_modal_image_<?= htmlspecialchars($post['id']) ?>" class="form-label">Select JPG Image:</label>
                                                    <input type="file" class="form-control" id="direct_modal_image_<?= htmlspecialchars($post['id']) ?>" name="image" accept=".jpg,.jpeg" required>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="button" class="btn btn-primary" id="directUploadButton<?= htmlspecialchars($post['id']) ?>">Direct Upload</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($post['excerpt']) ?></td>
                        <td><?= date('Y-m-d', strtotime($post['published_date'])) ?></td>
                        <td>
                            <?php 
                            $tags = json_decode($post['tags'], true);
                            if(is_array($tags)) {
                                foreach($tags as $tag) {
                                    echo '<span class="badge bg-secondary me-1">' . htmlspecialchars($tag) . '</span>';
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <div class="d-flex flex-column align-items-center">
                                <?php if($post['published']): ?>
                                    <span class="badge bg-success mb-2">Published</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark mb-2">Draft</span>
                                <?php endif; ?>
                                
                                <a href="toggle_status.php?slug=<?= urlencode($post['slug']) ?>" 
                                   class="btn btn-sm <?= $post['published'] ? 'btn-outline-warning' : 'btn-outline-success' ?>">
                                    <?= $post['published'] ? 'Set to Draft' : 'Publish' ?>
                                </a>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="delete_post.php?slug=<?= urlencode($post['slug']) ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Are you sure you want to delete this post?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            No blog posts found. <a href="index.php">Create your first post</a>.
        </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sync image name between the change and upload forms
        document.addEventListener('DOMContentLoaded', function() {
            <?php foreach($posts as $post): ?>
            (function() {
                const postId = <?= htmlspecialchars($post['id']) ?>;
                const changeImageInput = document.getElementById('image_name_' + postId);
                const directImageName = document.getElementById('direct_modal_image_name_' + postId);
                const directUploadButton = document.getElementById('directUploadButton' + postId);
                const uploadAlert = document.getElementById('uploadAlert' + postId);
                const uploadModalEl = document.getElementById('uploadImageModal' + postId);
                const changeModalEl = document.getElementById('changeImageModal' + postId);
                
                // Make sure critical elements exist before using them
                if (!directUploadButton) {
                    console.error(`Upload button for post ID ${postId} not found`);
                    return;
                }
                
                let uploadModal = null;
                let changeImageModal = null;
                
                if (uploadModalEl) {
                    uploadModal = new bootstrap.Modal(uploadModalEl);
                }
                
                if (changeModalEl) {
                    changeImageModal = new bootstrap.Modal(changeModalEl);
                }
                
                // When upload modal is shown, copy the image name from change form
                if (uploadModalEl && directImageName && changeImageInput) {
                    uploadModalEl.addEventListener('show.bs.modal', function() {
                        directImageName.value = changeImageInput.value;
                        
                        // Reset any previous alert
                        if (uploadAlert) {
                            uploadAlert.classList.add('d-none');
                            uploadAlert.classList.remove('alert-danger', 'alert-success');
                            uploadAlert.textContent = '';
                        }
                    });
                }
                
                // Handle direct upload button click
                directUploadButton.addEventListener('click', function() {
                    console.log('Direct upload button clicked for post ID: ' + postId);
                    
                    const directForm = document.getElementById('uploadImageForm' + postId);
                    if (!directForm) {
                        console.error(`Direct form for post ID ${postId} not found!`);
                        
                        // Try finding the form directly from modal
                        const uploadModal = document.getElementById('uploadImageModal' + postId);
                        if (uploadModal) {
                            const formInModal = uploadModal.querySelector('form');
                            if (formInModal) {
                                console.log(`Found form inside modal for post ID ${postId}, using that instead`);
                                formInModal.submit();
                                return;
                            }
                        }
                        
                        alert('Upload form not found. Please refresh the page and try again.');
                        return;
                    }
                    
                    const directImageName = document.getElementById('direct_modal_image_name_' + postId);
                    if (!directImageName) {
                        console.error(`Image name input for post ID ${postId} not found!`);
                        return;
                    }
                    
                    // Check if image name is provided
                    if (!directImageName.value.trim()) {
                        if (uploadAlert) {
                            uploadAlert.textContent = 'Please provide an image name';
                            uploadAlert.classList.remove('d-none', 'alert-success', 'alert-warning');
                            uploadAlert.classList.add('alert-danger');
                        } else {
                            alert('Please provide an image name');
                        }
                        return;
                    }
                    
                    const directImage = document.getElementById('direct_modal_image_' + postId);
                    if (!directImage) {
                        console.error(`Direct image input for post ID ${postId} not found!`);
                        return;
                    }
                    
                    // Check if a file is selected
                    if (!directImage.files || !directImage.files[0]) {
                        if (uploadAlert) {
                            uploadAlert.textContent = 'Please select a file to upload';
                            uploadAlert.classList.remove('d-none', 'alert-success', 'alert-warning');
                            uploadAlert.classList.add('alert-danger');
                        } else {
                            alert('Please select a file to upload');
                        }
                        return;
                    }
                    
                    // Validate form
                    if (!directForm.checkValidity()) {
                        directForm.reportValidity();
                        return;
                    }
                    
                    // Store the image name in localStorage before submitting
                    localStorage.setItem('lastUploadedImageName_' + postId, directImageName.value.trim());
                    
                    // Show loading state
                    directUploadButton.disabled = true;
                    directUploadButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...';
                    
                    // Submit the form
                    directForm.submit();
                });
            })();
            <?php endforeach; ?>
            
            // Handle URL parameter for image_uploaded
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('image_uploaded') && urlParams.get('image_uploaded') === '1') {
                <?php foreach($posts as $post): ?>
                (function() {
                    const postId = <?= htmlspecialchars($post['id']) ?>;
                    const lastUploadedImageName = localStorage.getItem('lastUploadedImageName_' + postId);
                    if (lastUploadedImageName) {
                        const changeImageInput = document.getElementById('image_name_' + postId);
                        if (changeImageInput) {
                            changeImageInput.value = lastUploadedImageName;
                            localStorage.removeItem('lastUploadedImageName_' + postId);
                        }
                    }
                })();
                <?php endforeach; ?>
            }
        });
    </script>
</body>
</html> 