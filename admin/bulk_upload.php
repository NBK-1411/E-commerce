<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';

require_admin();

$user = get_current_customer();
$user_id = $user['customer_id'] ?? null;

if (!$user_id) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Image Upload - Admin</title>
    <link rel="stylesheet" href="../admin-styles.css">
    <style>
        .bulk-upload-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
        }
        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 3rem;
            text-align: center;
            background: #f8fafc;
            transition: all 0.3s;
            cursor: pointer;
        }
        .upload-area:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }
        .upload-area.dragover {
            border-color: #3b82f6;
            background: #dbeafe;
        }
        .file-list {
            margin-top: 2rem;
        }
        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-bottom: 0.5rem;
        }
        .file-item.success {
            border-color: #10b981;
            background: #f0fdf4;
        }
        .file-item.error {
            border-color: #ef4444;
            background: #fef2f2;
        }
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 0.5rem;
        }
        .progress-fill {
            height: 100%;
            background: #3b82f6;
            transition: width 0.3s;
        }
        .upload-btn {
            margin-top: 1.5rem;
            padding: 0.75rem 2rem;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .upload-btn:hover {
            background: #2563eb;
        }
        .upload-btn:disabled {
            background: #94a3b8;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="bulk-upload-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1>Bulk Image Upload</h1>
            <a href="perfume.php" class="btn btn-secondary">Back to Products</a>
        </div>

        <div class="upload-area" id="uploadArea">
            <input type="file" id="fileInput" multiple accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" style="display: none;">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 1rem; color: #64748b;">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
            </svg>
            <h2>Drag & Drop Images Here</h2>
            <p style="color: #64748b; margin-top: 0.5rem;">or click to select multiple files</p>
            <p style="color: #94a3b8; font-size: 0.875rem; margin-top: 0.5rem;">Supported formats: JPEG, PNG, GIF, WebP (Max 5MB per file)</p>
        </div>

        <div id="fileList" class="file-list" style="display: none;">
            <h3 style="margin-bottom: 1rem;">Selected Files (<span id="fileCount">0</span>)</h3>
            <div id="fileItems"></div>
            <button id="uploadBtn" class="upload-btn">Upload All Images</button>
        </div>

        <div id="message" style="margin-top: 1.5rem;"></div>
    </div>

    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('fileList');
        const fileItems = document.getElementById('fileItems');
        const fileCount = document.getElementById('fileCount');
        const uploadBtn = document.getElementById('uploadBtn');
        const messageDiv = document.getElementById('message');

        let selectedFiles = [];

        // Click to select files
        uploadArea.addEventListener('click', () => fileInput.click());

        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });

        fileInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });

        function handleFiles(files) {
            const validFiles = Array.from(files).filter(file => {
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                const maxSize = 5 * 1024 * 1024; // 5MB
                
                if (!validTypes.includes(file.type)) {
                    showMessage(`File "${file.name}" is not a valid image type`, 'error');
                    return false;
                }
                if (file.size > maxSize) {
                    showMessage(`File "${file.name}" exceeds 5MB limit`, 'error');
                    return false;
                }
                return true;
            });

            selectedFiles = [...selectedFiles, ...validFiles];
            updateFileList();
        }

        function updateFileList() {
            if (selectedFiles.length === 0) {
                fileList.style.display = 'none';
                return;
            }

            fileList.style.display = 'block';
            fileCount.textContent = selectedFiles.length;
            fileItems.innerHTML = '';

            selectedFiles.forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                fileItem.id = `file-${index}`;
                fileItem.innerHTML = `
                    <div style="flex: 1;">
                        <div style="font-weight: 500;">${escapeHtml(file.name)}</div>
                        <div style="font-size: 0.875rem; color: #64748b;">${formatFileSize(file.size)}</div>
                        <div class="progress-bar" style="display: none;">
                            <div class="progress-fill" style="width: 0%;"></div>
                        </div>
                    </div>
                    <button onclick="removeFile(${index})" style="margin-left: 1rem; color: #ef4444; background: none; border: none; cursor: pointer;">Remove</button>
                `;
                fileItems.appendChild(fileItem);
            });
        }

        function removeFile(index) {
            selectedFiles.splice(index, 1);
            updateFileList();
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        uploadBtn.addEventListener('click', async () => {
            if (selectedFiles.length === 0) {
                showMessage('Please select at least one file', 'error');
                return;
            }

            uploadBtn.disabled = true;
            uploadBtn.textContent = `Uploading ${selectedFiles.length} file${selectedFiles.length > 1 ? 's' : ''}...`;
            messageDiv.innerHTML = '';

            // Show progress bars
            selectedFiles.forEach((file, index) => {
                const fileItem = document.getElementById(`file-${index}`);
                const progressBar = fileItem.querySelector('.progress-bar');
                progressBar.style.display = 'block';
            });

            try {
                // Create FormData with all files
                const formData = new FormData();
                selectedFiles.forEach((file, index) => {
                    formData.append('images[]', file);
                });

                // Upload all files in one request
                const response = await fetch('../actions/bulk_upload_product_images_action.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    const results = data.data.results;
                    
                    // Update UI for each file
                    results.forEach((result, index) => {
                        const fileItem = document.getElementById(`file-${index}`);
                        const progressFill = fileItem.querySelector('.progress-fill');
                        
                        if (result.success) {
                            progressFill.style.width = '100%';
                            progressFill.style.background = '#10b981';
                            fileItem.classList.add('success');
                        } else {
                            progressFill.style.width = '100%';
                            progressFill.style.background = '#ef4444';
                            fileItem.classList.add('error');
                        }
                    });

                    // Show results
                    const successCount = data.data.success_count;
                    const errorCount = data.data.error_count;
                    
                    let resultMessage = `<div style="padding: 1rem; border-radius: 6px; margin-top: 1rem; ${
                        errorCount === 0 ? 'background: #f0fdf4; border: 1px solid #10b981;' : 'background: #fef2f2; border: 1px solid #ef4444;'
                    }">`;
                    resultMessage += `<strong>Upload Complete:</strong> ${successCount} successful, ${errorCount} failed<br><br>`;
                    
                    if (successCount > 0) {
                        resultMessage += '<strong>Uploaded Files:</strong><ul style="margin-top: 0.5rem;">';
                        results.filter(r => r.success).forEach(r => {
                            resultMessage += `<li>${escapeHtml(r.filename)} → ${escapeHtml(r.saved_as)}<br><small style="color: #64748b;">Path: ${escapeHtml(r.path)}</small></li>`;
                        });
                        resultMessage += '</ul>';
                    }

                    if (errorCount > 0) {
                        resultMessage += '<strong>Failed Files:</strong><ul style="margin-top: 0.5rem; color: #991b1b;">';
                        results.filter(r => !r.success).forEach(r => {
                            resultMessage += `<li>${escapeHtml(r.filename)} - ${escapeHtml(r.error)}</li>`;
                        });
                        resultMessage += '</ul>';
                    }

                    resultMessage += '</div>';
                    messageDiv.innerHTML = resultMessage;

                    // Clear files after successful upload
                    if (errorCount === 0) {
                        setTimeout(() => {
                            selectedFiles = [];
                            updateFileList();
                            messageDiv.innerHTML = '';
                            showMessage('All files uploaded successfully! Ready for new upload.', 'success');
                        }, 3000);
                    }
                } else {
                    showMessage(data.message || 'Upload failed', 'error');
                }

            } catch (error) {
                console.error('Upload error:', error);
                showMessage('Error uploading files: ' + error.message, 'error');
                
                // Mark all as error
                selectedFiles.forEach((file, index) => {
                    const fileItem = document.getElementById(`file-${index}`);
                    fileItem.classList.add('error');
                });
            }

            uploadBtn.disabled = false;
            uploadBtn.textContent = 'Upload All Images';
        });

        function showMessage(text, type) {
            messageDiv.innerHTML = `<div style="padding: 1rem; border-radius: 6px; ${
                type === 'error' ? 'background: #fef2f2; border: 1px solid #ef4444; color: #991b1b;' : 'background: #f0fdf4; border: 1px solid #10b981; color: #065f46;'
            }">${escapeHtml(text)}</div>`;
            setTimeout(() => {
                messageDiv.innerHTML = '';
            }, 5000);
        }

        // Make removeFile available globally
        window.removeFile = removeFile;
    </script>
</body>
</html>

