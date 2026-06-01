<?php
session_start();
require_once __DIR__ . '/config.php';

if (!($_SESSION['authed'] ?? false)) {
    header('Location: index.php');
    exit;
}

// ─── Collect log files ────────────────────────────────────────────────────────
$files = glob(LOG_DIR . '*.log');
$logs  = [];

if ($files) {
    foreach ($files as $path) {
        $name  = basename($path);
        // Filename format: {key}_{Ymd}_{His}.log
        $parts = explode('_', $name, 2);
        $key   = $parts[0] ?? '';

        $labels = [
            'four' => '4 O\'Clock Scripts',
            'five' => '5 O\'Clock Scripts',
            'ten'  => '10 O\'Clock Scripts',
        ];

        $logs[] = [
            'path'  => $path,
            'name'  => $name,
            'label' => $labels[$key] ?? $key,
            'mtime' => filemtime($path),
            'size'  => filesize($path),
        ];
    }
    usort($logs, fn($a, $b) => $b['mtime'] - $a['mtime']);
}

// ─── View a specific log ──────────────────────────────────────────────────────
$viewContent = null;
$viewName    = null;

if (isset($_GET['view'])) {
    // Sanitise: only allow a plain filename (no slashes, must end in .log, must exist in our list).
    $requested = basename($_GET['view']);
    $fullPath  = LOG_DIR . $requested;

    // Verify the resolved path stays inside LOG_DIR and is in our list.
    $realLog  = realpath($fullPath);
    $realDir  = realpath(LOG_DIR);

    if (
        $realLog &&
        $realDir &&
        str_starts_with($realLog, $realDir . DIRECTORY_SEPARATOR) &&
        in_array($realLog, array_map(fn($l) => realpath($l['path']), $logs), true)
    ) {
        $viewContent = file_get_contents($realLog);
        $viewName    = $requested;
    }
}

function fmt_size(int $bytes): string {
    if ($bytes < 1024) return $bytes . ' B';
    if ($bytes < 1024 * 1024) return round($bytes / 1024, 1) . ' KB';
    return round($bytes / (1024 * 1024), 1) . ' MB';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Past Logs — SMS Dashboard</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .container { max-width: 860px; margin: 0 auto; }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        header h1 { font-size: 1.5rem; font-weight: 700; color: #f1f5f9; }

        header .meta { display: flex; gap: 1.25rem; font-size: 0.875rem; }

        header .meta a { color: #3b82f6; text-decoration: none; }
        header .meta a:hover { text-decoration: underline; }

        .log-table {
            width: 100%;
            border-collapse: collapse;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .log-table th {
            text-align: left;
            padding: 0.75rem 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            background: #0f172a;
            border-bottom: 1px solid #334155;
        }

        .log-table td {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            border-bottom: 1px solid #1e293b;
            color: #cbd5e1;
        }

        .log-table tr:last-child td { border-bottom: none; }

        .log-table tr:hover td { background: #263045; }

        .log-table td a { color: #3b82f6; text-decoration: none; }
        .log-table td a:hover { text-decoration: underline; }

        .log-viewer h2 {
            font-size: 0.9rem;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.75rem;
        }

        .log-viewer h2 span {
            font-weight: 400;
            color: #64748b;
            text-transform: none;
            letter-spacing: 0;
            margin-left: 0.5rem;
        }

        pre#log-view {
            background: #020817;
            border: 1px solid #1e293b;
            border-radius: 8px;
            padding: 1rem 1.1rem;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.82rem;
            color: #86efac;
            max-height: 580px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-all;
            line-height: 1.55;
        }

        .empty-state {
            color: #475569;
            font-size: 0.9rem;
            padding: 2rem 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Past Logs</h1>
            <div class="meta">
                <a href="dashboard.php">Back to Dashboard</a>
                <a href="logout.php">Sign Out</a>
            </div>
        </header>

        <?php if (empty($logs)): ?>
            <p class="empty-state">No log files found yet. Run a script to generate logs.</p>
        <?php else: ?>
            <table class="log-table">
                <thead>
                    <tr>
                        <th>Script</th>
                        <th>Date / Time</th>
                        <th>Size</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['label']) ?></td>
                        <td><?= date('Y-m-d H:i:s', $log['mtime']) ?></td>
                        <td><?= fmt_size($log['size']) ?></td>
                        <td>
                            <a href="logs.php?view=<?= urlencode($log['name']) ?>">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($viewContent !== null): ?>
            <div class="log-viewer">
                <h2>Log Output <span><?= htmlspecialchars($viewName) ?></span></h2>
                <pre id="log-view"><?= htmlspecialchars($viewContent) ?></pre>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
