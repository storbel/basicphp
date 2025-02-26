<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Environment Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .card { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Environment Details</h1>

        <!-- Hostname -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Hostname</h5>
            </div>
            <div class="card-body">
                <p class="card-text"><?php echo gethostname(); ?></p>
            </div>
        </div>

        <!-- Location of index.php -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Location of index.php</h5>
            </div>
            <div class="card-body">
                <p class="card-text"><?php echo __FILE__; ?></p>
            </div>
        </div>

        <!-- List files in the data folder -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Files in the Data Folder</h5>
            </div>
            <div class="card-body">
                <?php
                $dataFolder = 'data';
                if (is_dir($dataFolder)) {
                    $files = scandir($dataFolder);
                    echo "<ul>";
                    foreach ($files as $file) {
                        if ($file !== "." && $file !== "..") {
                            echo "<li>$file</li>";
                        }
                    }
                    echo "</ul>";
                } else {
                    echo "<p class='text-danger'>The 'data' folder does not exist.</p>";
                }
                ?>
            </div>
        </div>

        <!-- Available Disks -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Available Disks</h5>
            </div>
            <div class="card-body">
                <?php
                $disks = disk_total_space('/');
                if ($disks !== false) {
                    echo "<p>Total disk space: " . formatBytes($disks) . "</p>";
                } else {
                    echo "<p class='text-danger'>Unable to retrieve disk information.</p>";
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

        <!-- Available Mounts -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Available Mounts</h5>
            </div>
            <div class="card-body">
                <?php
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    echo "<p class='text-warning'>Mount information is not available on Windows.</p>";
                } else {
                    $mounts = file('/proc/mounts');
                    echo "<ul>";
                    foreach ($mounts as $mount) {
                        echo "<li>" . htmlspecialchars($mount) . "</li>";
                    }
                    echo "</ul>";
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
