<?php
/**
 * Environment Detection Test
 * Use this file to verify your upload configuration is working correctly
 */

require_once 'settings/upload_config.php';
require_once 'settings/core.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Environment Detection Test</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
        }
        h2 {
            color: #555;
            margin-top: 30px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 10px;
            margin: 20px 0;
        }
        .label {
            font-weight: 600;
            color: #666;
        }
        .value {
            color: #333;
            font-family: 'Courier New', monospace;
            background: #f8f8f8;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        .status.success {
            background: #4CAF50;
            color: white;
        }
        .status.error {
            background: #f44336;
            color: white;
        }
        .status.warning {
            background: #ff9800;
            color: white;
        }
        .test-section {
            background: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #2196F3;
            margin: 20px 0;
        }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert.info {
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            color: #1976d2;
        }
        .alert.success {
            background: #e8f5e9;
            border-left: 4px solid #4CAF50;
            color: #388e3c;
        }
        .alert.error {
            background: #ffebee;
            border-left: 4px solid #f44336;
            color: #c62828;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌐 Environment Detection Test</h1>
        
        <div class="info-grid">
            <div class="label">Environment:</div>
            <div class="value">
                <?php if (IS_SCHOOL_SERVER): ?>
                    <span class="status success">LIVE SERVER</span>
                <?php else: ?>
                    <span class="status warning">LOCAL DEVELOPMENT</span>
                <?php endif; ?>
            </div>
            
            <div class="label">Upload Base Path:</div>
            <div class="value"><?php echo htmlspecialchars(UPLOADS_BASE_PATH); ?></div>
            
            <div class="label">Upload Web Path:</div>
            <div class="value"><?php echo htmlspecialchars(UPLOADS_WEB_PATH); ?></div>
            
            <div class="label">Current Directory:</div>
            <div class="value"><?php echo htmlspecialchars(__DIR__); ?></div>
        </div>

        <h2>📁 Directory Status</h2>
        <div class="info-grid">
            <div class="label">Directory Exists:</div>
            <div class="value">
                <?php if (is_dir(UPLOADS_BASE_PATH)): ?>
                    <span class="status success">YES ✓</span>
                <?php else: ?>
                    <span class="status error">NO ✗</span>
                <?php endif; ?>
            </div>
            
            <div class="label">Directory Writable:</div>
            <div class="value">
                <?php if (is_dir(UPLOADS_BASE_PATH) && is_writable(UPLOADS_BASE_PATH)): ?>
                    <span class="status success">YES ✓</span>
                <?php elseif (is_dir(UPLOADS_BASE_PATH)): ?>
                    <span class="status error">NO ✗</span>
                <?php else: ?>
                    <span class="status warning">N/A</span>
                <?php endif; ?>
            </div>
            
            <div class="label">Permissions:</div>
            <div class="value">
                <?php 
                if (is_dir(UPLOADS_BASE_PATH)) {
                    echo substr(sprintf('%o', fileperms(UPLOADS_BASE_PATH)), -4);
                } else {
                    echo "N/A";
                }
                ?>
            </div>
        </div>

        <h2>🔗 URL Generation Test</h2>
        <?php
        $test_paths = [
            'uploads/u1/p5/test.jpg',
            '/uploads/u1/p5/test.jpg',
            'u1/p5/test.jpg'
        ];
        ?>
        <div class="test-section">
            <p><strong>Testing normalize_image_path() function:</strong></p>
            <?php foreach ($test_paths as $path): ?>
                <div style="margin: 10px 0; padding: 10px; background: white; border-radius: 4px;">
                    <strong>Input:</strong> <code><?php echo htmlspecialchars($path); ?></code><br>
                    <strong>Output:</strong> <code><?php echo htmlspecialchars(normalize_image_path($path)); ?></code>
                </div>
            <?php endforeach; ?>
        </div>

        <h2>📊 System Information</h2>
        <div class="info-grid">
            <div class="label">PHP Version:</div>
            <div class="value"><?php echo PHP_VERSION; ?></div>
            
            <div class="label">Server Software:</div>
            <div class="value"><?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'); ?></div>
            
            <div class="label">Document Root:</div>
            <div class="value"><?php echo htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'); ?></div>
            
            <div class="label">HTTP Host:</div>
            <div class="value"><?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'Unknown'); ?></div>
        </div>

        <?php if (!is_dir(UPLOADS_BASE_PATH)): ?>
            <div class="alert error">
                <strong>⚠️ Uploads Directory Not Found!</strong><br>
                The uploads directory does not exist at: <code><?php echo htmlspecialchars(UPLOADS_BASE_PATH); ?></code><br><br>
                <strong>To fix this:</strong><br>
                <?php if (IS_SCHOOL_SERVER): ?>
                    1. SSH into your server<br>
                    2. Run: <code>mkdir -p ~/uploads</code><br>
                    3. Run: <code>chmod 775 ~/uploads</code><br>
                    4. Refresh this page
                <?php else: ?>
                    1. Create the directory: <code><?php echo htmlspecialchars(UPLOADS_BASE_PATH); ?></code><br>
                    2. Set permissions to 775<br>
                    3. Refresh this page
                <?php endif; ?>
            </div>
        <?php elseif (!is_writable(UPLOADS_BASE_PATH)): ?>
            <div class="alert error">
                <strong>⚠️ Uploads Directory Not Writable!</strong><br>
                The uploads directory exists but PHP cannot write to it.<br><br>
                <strong>To fix this:</strong><br>
                1. Run: <code>chmod 775 <?php echo htmlspecialchars(UPLOADS_BASE_PATH); ?></code><br>
                2. Run: <code>chown -R your-user:www-data <?php echo htmlspecialchars(UPLOADS_BASE_PATH); ?></code><br>
                3. Refresh this page
            </div>
        <?php else: ?>
            <div class="alert success">
                <strong>✅ Configuration Looks Good!</strong><br>
                Your upload directory is properly configured and writable.<br>
                You should be able to upload images without issues.
            </div>
        <?php endif; ?>

        <?php if (IS_SCHOOL_SERVER): ?>
            <div class="alert info">
                <strong>ℹ️ Live Server Configuration</strong><br>
                You're running on a live server. Make sure:<br>
                1. The <code>/uploads</code> directory is accessible via web at <code><?php echo get_uploads_url(); ?></code><br>
                2. Apache/Nginx is configured to serve files from <code><?php echo htmlspecialchars(UPLOADS_BASE_PATH); ?></code><br>
                3. Test by creating a file: <code>echo "test" > <?php echo htmlspecialchars(UPLOADS_BASE_PATH); ?>/test.txt</code><br>
                4. Then visit: <code><?php echo get_uploads_url('test.txt'); ?></code>
            </div>
        <?php endif; ?>

        <h2>🧪 Next Steps</h2>
        <div class="test-section">
            <ol>
                <li>Verify all status indicators above show <span class="status success">YES ✓</span></li>
                <li>Test uploading an image through your admin panel</li>
                <li>Check if images display correctly on your shop page</li>
                <li>If issues persist, check your server error logs</li>
            </ol>
        </div>
    </div>
</body>
</html>

