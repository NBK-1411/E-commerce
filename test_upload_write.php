<?php
/**
 * Simple test to check if PHP can write to uploads folder
 * No admin access required - just upload this file and visit it
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Write Test</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            max-width: 700px;
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
        .status {
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            font-size: 16px;
        }
        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        .info {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            color: #0c5460;
        }
        .test-item {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        code {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .emoji {
            font-size: 24px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Upload Folder Write Test</h1>
        
        <?php
        $uploadsDir = __DIR__ . '/uploads';
        $testFile = $uploadsDir . '/test_write_' . time() . '.txt';
        $testContent = 'Write test successful at ' . date('Y-m-d H:i:s');
        
        echo "<h2>Test Results:</h2>";
        
        // Test 1: Check if uploads directory exists
        echo "<div class='test-item'>";
        echo "<strong>Test 1: Uploads Directory Exists</strong><br>";
        if (is_dir($uploadsDir)) {
            echo "<div class='status success'><span class='emoji'>✅</span>SUCCESS: Directory exists at <code>$uploadsDir</code></div>";
        } else {
            echo "<div class='status error'><span class='emoji'>❌</span>FAILED: Directory does not exist at <code>$uploadsDir</code></div>";
            echo "<div class='status info'>You need to create the <code>uploads</code> folder first!</div>";
            echo "</div></div></body></html>";
            exit;
        }
        echo "</div>";
        
        // Test 2: Check if directory is writable
        echo "<div class='test-item'>";
        echo "<strong>Test 2: Directory is Writable</strong><br>";
        if (is_writable($uploadsDir)) {
            echo "<div class='status success'><span class='emoji'>✅</span>SUCCESS: PHP reports directory as writable</div>";
        } else {
            echo "<div class='status error'><span class='emoji'>❌</span>FAILED: Directory is not writable</div>";
            echo "<div class='status info'>";
            echo "<strong>To fix this:</strong><br>";
            echo "1. Connect via FTP/SFTP<br>";
            echo "2. Right-click on 'uploads' folder<br>";
            echo "3. Select 'File Permissions' or 'Properties'<br>";
            echo "4. Set permissions to <code>775</code> or <code>777</code><br>";
            echo "5. Refresh this page";
            echo "</div>";
        }
        echo "</div>";
        
        // Test 3: Try to actually write a file
        echo "<div class='test-item'>";
        echo "<strong>Test 3: Actual File Write</strong><br>";
        $writeSuccess = @file_put_contents($testFile, $testContent);
        
        if ($writeSuccess !== false) {
            echo "<div class='status success'><span class='emoji'>✅</span>SUCCESS: File written successfully!</div>";
            echo "<div class='status info'>";
            echo "Created file: <code>" . basename($testFile) . "</code><br>";
            echo "Size: $writeSuccess bytes<br>";
            echo "Content: <code>$testContent</code>";
            echo "</div>";
            
            // Clean up
            if (@unlink($testFile)) {
                echo "<div class='status success'><span class='emoji'>🗑️</span>Test file cleaned up successfully</div>";
            } else {
                echo "<div class='status info'>Note: Test file was created but couldn't be automatically deleted. You may need to delete <code>" . basename($testFile) . "</code> manually via FTP.</div>";
            }
        } else {
            echo "<div class='status error'><span class='emoji'>❌</span>FAILED: Could not write file</div>";
            echo "<div class='status info'>";
            echo "<strong>Possible reasons:</strong><br>";
            echo "1. Folder permissions are not correct (need 775 or 777)<br>";
            echo "2. Disk quota exceeded<br>";
            echo "3. SELinux or similar security system blocking writes<br>";
            echo "4. Contact your hosting support for help";
            echo "</div>";
        }
        echo "</div>";
        
        // Test 4: Check current permissions
        echo "<div class='test-item'>";
        echo "<strong>Test 4: Current Permissions</strong><br>";
        $perms = fileperms($uploadsDir);
        $permsOctal = substr(sprintf('%o', $perms), -4);
        echo "<div class='status info'>";
        echo "Uploads folder permissions: <code>$permsOctal</code><br>";
        echo "Owner: <code>" . (function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($uploadsDir))['name'] : fileowner($uploadsDir)) . "</code><br>";
        
        if ($permsOctal >= '0775') {
            echo "<span class='emoji'>✅</span> Permissions look good!";
        } elseif ($permsOctal >= '0755') {
            echo "<span class='emoji'>⚠️</span> Permissions are restrictive. If writes fail, try 775 or 777.";
        } else {
            echo "<span class='emoji'>❌</span> Permissions are too restrictive. Set to 775 or 777.";
        }
        echo "</div>";
        echo "</div>";
        
        // Final verdict
        echo "<h2>🎯 Final Verdict:</h2>";
        if ($writeSuccess !== false) {
            echo "<div class='status success'>";
            echo "<h3 style='margin-top:0;'>🎉 All Tests Passed!</h3>";
            echo "Your uploads folder is properly configured. You can now:<br><br>";
            echo "1. Upload images via your admin panel<br>";
            echo "2. Images will be saved to the uploads folder<br>";
            echo "3. Images will display correctly on your site<br><br>";
            echo "<strong>Next step:</strong> Visit your admin panel and try uploading a product image!";
            echo "</div>";
        } else {
            echo "<div class='status error'>";
            echo "<h3 style='margin-top:0;'>⚠️ Action Required</h3>";
            echo "Your uploads folder needs configuration. Please:<br><br>";
            echo "1. Set folder permissions to <code>775</code> or <code>777</code> via FTP<br>";
            echo "2. Refresh this page to test again<br>";
            echo "3. If still failing, contact your hosting support<br><br>";
            echo "<strong>Tell them:</strong> \"Please make ~/public_html/your-project/uploads writable by PHP\"";
            echo "</div>";
        }
        ?>
        
        <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <strong>📝 Need Help?</strong><br>
            Read: <code>NO_ADMIN_DEPLOYMENT.md</code> for detailed troubleshooting steps.
        </div>
    </div>
</body>
</html>

