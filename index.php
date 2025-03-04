<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portworx Storage Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
        }
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 2rem;
        }
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .status-badge {
            background-color: #2ecc71;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            display: inline-block;
            margin: 1rem 0;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
            margin-bottom: 1.5rem;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background-color: white;
            border-bottom: 2px solid #f1f1f1;
            padding: 1rem;
        }
        .card-header h5 {
            color: var(--primary-color);
            margin: 0;
            font-weight: 600;
        }
        .card-body {
            padding: 1.5rem;
        }
        .info-icon {
            margin-right: 0.5rem;
            color: var(--accent-color);
        }
        .mount-list {
            max-height: 300px;
            overflow-y: auto;
        }
        .mount-list::-webkit-scrollbar {
            width: 8px;
        }
        .mount-list::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .mount-list::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-header text-center">
            <h1><i class="fas fa-database me-2"></i>Portworx Storage Dashboard</h1>
            <div class="status-badge">
                <i class="fas fa-check-circle me-2"></i>Storage is Working
            </div>
            <p class="mb-0">Storage Class: <strong>px-csi-replicated</strong> | Test Date: <?php echo date('F j, Y'); ?></p>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-server info-icon"></i>Hostname</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text fw-bold"><?php echo gethostname(); ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-file-code info-icon"></i>Index Location</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text text-break"><?php echo __FILE__; ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-folder-open info-icon"></i>Data Files</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $dataFolder = 'data';
                        if (is_dir($dataFolder)) {
                            $files = scandir($dataFolder);
                            echo "<div class='list-group'>";
                            foreach ($files as $file) {
                                if ($file !== "." && $file !== "..") {
                                    echo "<div class='list-group-item'><i class='fas fa-file me-2'></i>$file</div>";
                                }
                            }
                            echo "</div>";
                        } else {
                            echo "<div class='alert alert-warning'><i class='fas fa-exclamation-triangle me-2'></i>The 'data' folder does not exist.</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-upload info-icon"></i>Upload File</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fileToUpload"])) {
                            $targetDir = "data/";
                            if (!is_dir($targetDir)) {
                                mkdir($targetDir, 0777, true);
                            }
                            
                            $targetFile = $targetDir . basename($_FILES["fileToUpload"]["name"]);
                            $uploadOk = 1;
                            
                            // Check if file already exists
                            if (file_exists($targetFile)) {
                                echo "<div class='alert alert-warning'><i class='fas fa-exclamation-triangle me-2'></i>File already exists.</div>";
                                $uploadOk = 0;
                            }
                            
                            // Check file size (5MB max)
                            if ($_FILES["fileToUpload"]["size"] > 5000000) {
                                echo "<div class='alert alert-warning'><i class='fas fa-exclamation-triangle me-2'></i>File is too large (max 5MB).</div>";
                                $uploadOk = 0;
                            }
                            
                            if ($uploadOk == 1) {
                                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
                                    echo "<div class='alert alert-success'><i class='fas fa-check-circle me-2'></i>File " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.</div>";
                                } else {
                                    echo "<div class='alert alert-danger'><i class='fas fa-times-circle me-2'></i>Error uploading file.</div>";
                                }
                            }
                        }
                        ?>
                        <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="fileToUpload" class="form-label">Select file to upload:</label>
                                <input type="file" class="form-control" name="fileToUpload" id="fileToUpload" required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Upload File
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-hdd info-icon"></i>Storage Information</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $disks = disk_total_space('/');
                        if ($disks !== false) {
                            echo "<div class='d-flex align-items-center'>";
                            echo "<i class='fas fa-database me-2 text-primary'></i>";
                            echo "<span>Total disk space: <strong>" . formatBytes($disks) . "</strong></span>";
                            echo "</div>";
                        } else {
                            echo "<div class='alert alert-danger'><i class='fas fa-exclamation-circle me-2'></i>Unable to retrieve disk information.</div>";
                        }

                        function formatBytes($bytes, $precision = 2) {
                            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                            $bytes = max($bytes, 0);
                            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
                            $pow = min($pow, count($units) - 1);
                            $bytes /= (1 << (10 * $pow));
                            return round($bytes, $precision) . ' ' . $units[$pow];
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-network-wired info-icon"></i>Mount Points</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                            echo "<div class='alert alert-info'><i class='fas fa-info-circle me-2'></i>Mount information is not available on Windows systems.</div>";
                        } else {
                            $mounts = file('/proc/mounts');
                            echo "<div class='mount-list'>";
                            echo "<div class='list-group'>";
                            foreach ($mounts as $mount) {
                                echo "<div class='list-group-item'><i class='fas fa-plug me-2'></i>" . htmlspecialchars($mount) . "</div>";
                            }
                            echo "</div>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
