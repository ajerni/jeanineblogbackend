<?php
// Include authentication check
require_once 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Backend</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .container { max-width: 800px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Blog Backend</h1>
            <div>
                <a href="posts.php" class="btn btn-outline-primary me-2">Manage Posts</a>
                <a href="logout.php" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>
        
        <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">
            Post added successfully!
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h2>Add New Blog Post</h2>
            </div>
            <div class="card-body">
                <form action="save_post.php" method="post" id="mainPostForm" onsubmit="console.log('Form is being submitted');">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Excerpt</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="2" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="6" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image_name" class="form-label">Image Name</label>
                        <input type="text" class="form-control" id="image_name" name="image_name" placeholder="e.g. apfel" required>
                        <div class="form-text">Will be used in: https://ik.imagekit.io/jeaniblog/blog/tr:w-800,h-400/[image_name].jpg</div>
                        <button type="button" class="btn btn-secondary mt-2" data-bs-toggle="modal" data-bs-target="#uploadImageModal">Upload Image</button>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags</label>
                        <input type="text" class="form-control" id="tags" name="tags" placeholder="Tag1, Tag2, Tag3" required>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="published" name="published" checked>
                        <label class="form-check-label" for="published">Publish immediately</label>
                    </div>
                    
                    <button type="button" id="savePostButton" class="btn btn-primary">Save Post</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal for Uploading Image - Moved outside main form -->
    <div class="modal fade" id="uploadImageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="uploadAlert" class="alert d-none mb-3"></div>
                    
                    <!-- Direct form for file upload -->
                    <form id="uploadImageForm" action="upload_image.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="return_url" value="index.php">
                        
                        <div class="mb-3">
                            <label for="direct_modal_image_name" class="form-label">Image Name:</label>
                            <input type="text" class="form-control" id="direct_modal_image_name" name="image_name" placeholder="e.g. apfel" required>
                            <div class="form-text">This will be the name of your uploaded image (without .jpg extension)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="direct_modal_image" class="form-label">Select JPG Image:</label>
                            <input type="file" class="form-control" id="direct_modal_image" name="image" accept=".jpg,.jpeg" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="directUploadButton">Direct Upload</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mainImageInput = document.getElementById('image_name');
            const directImageName = document.getElementById('direct_modal_image_name');
            const directUploadButton = document.getElementById('directUploadButton');
            const uploadAlert = document.getElementById('uploadAlert');
            const uploadModalEl = document.getElementById('uploadImageModal');
            const mainForm = document.getElementById('mainPostForm');
            
            // Form field elements
            const titleInput = document.getElementById('title');
            const excerptInput = document.getElementById('excerpt');
            const contentInput = document.getElementById('content');
            const tagsInput = document.getElementById('tags');
            const publishedCheckbox = document.getElementById('published');
            
            if (uploadModalEl) {
                const bootstrapModal = new bootstrap.Modal(uploadModalEl);
                
                // Disable required validation on modal hidden
                uploadModalEl.addEventListener('hidden.bs.modal', function() {
                    const modalForm = document.getElementById('uploadImageForm');
                    if (modalForm) {
                        const modalInputs = modalForm.querySelectorAll('input, select, textarea');
                        modalInputs.forEach(input => {
                            if (input.hasAttribute('required')) {
                                input.dataset.tempRequired = 'true';
                                input.removeAttribute('required');
                            }
                        });
                    }
                });
                
                // Re-enable required validation when modal is shown
                uploadModalEl.addEventListener('shown.bs.modal', function() {
                    const modalForm = document.getElementById('uploadImageForm');
                    if (modalForm) {
                        const modalInputs = modalForm.querySelectorAll('input, select, textarea');
                        modalInputs.forEach(input => {
                            if (input.dataset.tempRequired === 'true') {
                                input.setAttribute('required', 'required');
                            }
                        });
                    }
                });
            }
            
            // Ensure main form is working
            if (!mainForm) {
                console.error('Main post form not found!');
            } else {
                console.log('Main post form found:', mainForm);
                
                // Handle the save button click
                const saveButton = document.getElementById('savePostButton');
                if (saveButton) {
                    saveButton.addEventListener('click', function(e) {
                        console.log('Save button clicked');
                        
                        // Focus on the main form fields only, not modal fields
                        mainForm.querySelector('input[name="title"]').focus();
                        
                        // Manual form submission instead of relying on button type="submit"
                        if (mainForm.checkValidity()) {
                            console.log('Form is valid, submitting to save_post.php');
                            
                            // Ensure any hidden or modal fields don't interfere with validation
                            const modalForm = document.getElementById('uploadImageForm');
                            if (modalForm) {
                                // Temporarily disable the modal form's validation constraints
                                const modalInputs = modalForm.querySelectorAll('input, select, textarea');
                                modalInputs.forEach(input => {
                                    if (input.hasAttribute('required')) {
                                        input.dataset.wasRequired = 'true';
                                        input.removeAttribute('required');
                                    }
                                });
                            }
                            
                            // Now submit the form
                            mainForm.submit(); // Explicitly submit the form
                        } else {
                            console.log('Form validation failed');
                            mainForm.reportValidity();
                        }
                    });
                }
            }
            
            // Helper function to save form data to localStorage
            function saveFormData() {
                if (titleInput) localStorage.setItem('newpost_title', titleInput.value);
                if (excerptInput) localStorage.setItem('newpost_excerpt', excerptInput.value);
                if (contentInput) localStorage.setItem('newpost_content', contentInput.value);
                if (mainImageInput) localStorage.setItem('newpost_image_name', mainImageInput.value);
                if (tagsInput) localStorage.setItem('newpost_tags', tagsInput.value);
                if (publishedCheckbox) localStorage.setItem('newpost_published', publishedCheckbox.checked ? '1' : '0');
            }
            
            // When opening the modal, copy the value from main form to upload form
            if (uploadModalEl && directImageName && mainImageInput) {
                uploadModalEl.addEventListener('show.bs.modal', function() {
                    directImageName.value = mainImageInput.value;
                    // Reset any previous alert
                    if (uploadAlert) {
                        uploadAlert.classList.add('d-none');
                        uploadAlert.classList.remove('alert-danger', 'alert-success');
                        uploadAlert.textContent = '';
                    }
                    
                    // Save form data before uploading
                    saveFormData();
                });
            }
            
            // Handle direct upload button click
            if (directUploadButton) {
                directUploadButton.addEventListener('click', function() {
                    console.log('Direct upload button clicked');
                    
                    const directForm = document.getElementById('uploadImageForm');
                    if (!directForm) {
                        console.error('Direct form not found!');
                        console.log('Looking for alternative form methods...');
                        
                        // Try finding the form by other means
                        const formInModal = uploadModalEl.querySelector('form');
                        if (formInModal) {
                            console.log('Found form inside modal, using that instead');
                            formInModal.submit();
                            return;
                        }
                        
                        alert('Could not find upload form. Please refresh and try again.');
                        return;
                    }
                    
                    // Check if image name is provided
                    if (!directImageName || !directImageName.value.trim()) {
                        if (uploadAlert) {
                            uploadAlert.textContent = 'Please provide an image name';
                            uploadAlert.classList.remove('d-none', 'alert-success', 'alert-warning');
                            uploadAlert.classList.add('alert-danger');
                        } else {
                            alert('Please provide an image name');
                        }
                        return;
                    }
                    
                    const directImage = document.getElementById('direct_modal_image');
                    // Check if a file is selected
                    if (!directImage || !directImage.files || !directImage.files[0]) {
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
                    
                    // Save form data again before submitting
                    saveFormData();
                    
                    // Store the image name in localStorage before submitting
                    localStorage.setItem('lastUploadedImageName', directImageName.value.trim());
                    
                    // Show loading state
                    directUploadButton.disabled = true;
                    directUploadButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...';
                    
                    // Submit the form
                    directForm.submit();
                });
            } else {
                console.error('Upload button not found');
            }
            
            // Handle URL parameter for image_uploaded and restore form data
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('image_uploaded') && urlParams.get('image_uploaded') === '1') {
                // Restore the image name
                const uploadedImageName = localStorage.getItem('lastUploadedImageName');
                if (uploadedImageName && mainImageInput) {
                    mainImageInput.value = uploadedImageName;
                    localStorage.removeItem('lastUploadedImageName');
                }
                
                // Restore form data
                if (titleInput && localStorage.getItem('newpost_title')) {
                    titleInput.value = localStorage.getItem('newpost_title');
                }
                if (excerptInput && localStorage.getItem('newpost_excerpt')) {
                    excerptInput.value = localStorage.getItem('newpost_excerpt');
                }
                if (contentInput && localStorage.getItem('newpost_content')) {
                    contentInput.value = localStorage.getItem('newpost_content');
                }
                if (tagsInput && localStorage.getItem('newpost_tags')) {
                    tagsInput.value = localStorage.getItem('newpost_tags');
                }
                if (publishedCheckbox && localStorage.getItem('newpost_published')) {
                    publishedCheckbox.checked = localStorage.getItem('newpost_published') === '1';
                }
            }
            
            // Clear localStorage values when form is submitted
            if (mainForm) {
                mainForm.addEventListener('submit', function() {
                    localStorage.removeItem('newpost_title');
                    localStorage.removeItem('newpost_excerpt');
                    localStorage.removeItem('newpost_content');
                    localStorage.removeItem('newpost_image_name');
                    localStorage.removeItem('newpost_tags');
                    localStorage.removeItem('newpost_published');
                });
            }
        });
    </script>
</body>
</html> 