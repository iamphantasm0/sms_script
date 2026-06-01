<?php
session_start();
require_once __DIR__ . '/config.php';

if (!($_SESSION['authed'] ?? false)) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Dashboard</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .container {
            max-width: 860px;
            margin: 0 auto;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2.5rem;
        }

        header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #f1f5f9;
        }

        header .meta {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            font-size: 0.875rem;
            color: #64748b;
        }

        header .meta a {
            color: #3b82f6;
            text-decoration: none;
        }

        header .meta a:hover { text-decoration: underline; }

        .scripts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .script-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 10px;
            padding: 1.4rem 1.25rem;
        }

        .script-card h2 {
            font-size: 1rem;
            font-weight: 600;
            color: #f1f5f9;
            margin-bottom: 0.35rem;
        }

        .script-card p {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 1.1rem;
            line-height: 1.4;
        }

        .btn-run {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.55rem 1.1rem;
            background: #3b82f6;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s, opacity 0.15s;
        }

        .btn-run:hover:not(:disabled) { background: #2563eb; }

        .btn-run:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-run .spinner {
            display: none;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        .btn-run.running .spinner { display: inline-block; }

        @keyframes spin { to { transform: rotate(360deg); } }

        .log-section h2 {
            font-size: 0.9rem;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.75rem;
        }

        #log-output {
            background: #020817;
            border: 1px solid #1e293b;
            border-radius: 8px;
            padding: 1rem 1.1rem;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.82rem;
            color: #86efac;
            min-height: 240px;
            max-height: 520px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-all;
            line-height: 1.55;
        }

        #log-status {
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: #64748b;
            min-height: 1.2em;
        }

        #log-status.error { color: #f87171; }
        #log-status.done  { color: #4ade80; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>SMS Dashboard</h1>
            <div class="meta">
                <a href="logs.php">View Past Logs</a>
                <a href="logout.php">Sign Out</a>
            </div>
        </header>

        <div class="scripts-grid">
            <div class="script-card">
                <h2>4 O'Clock Scripts</h2>
                <p>Fleet renewal reminders — 60, 30, 15, and 1 day notices</p>
                <button class="btn-run" data-script="four" onclick="runScript(this)">
                    <span class="spinner"></span>
                    Run Now
                </button>
            </div>

            <div class="script-card">
                <h2>5 O'Clock Scripts</h2>
                <p>Welcome messages and renewed policy notifications</p>
                <button class="btn-run" data-script="five" onclick="runScript(this)">
                    <span class="spinner"></span>
                    Run Now
                </button>
            </div>

            <div class="script-card">
                <h2>10 O'Clock Scripts</h2>
                <p>Non-fleet and non-motor renewal reminders</p>
                <button class="btn-run" data-script="ten" onclick="runScript(this)">
                    <span class="spinner"></span>
                    Run Now
                </button>
            </div>
        </div>

        <div class="log-section">
            <h2>Output</h2>
            <div id="log-output">Ready. Click a button above to run a script.</div>
            <div id="log-status"></div>
        </div>
    </div>

    <script>
        let activeSource = null;
        const allButtons = () => document.querySelectorAll('.btn-run');

        function setRunning(btn, running) {
            allButtons().forEach(b => b.disabled = running);
            btn.classList.toggle('running', running);
        }

        function runScript(btn) {
            const key = btn.dataset.script;
            const logBox = document.getElementById('log-output');
            const status = document.getElementById('log-status');

            if (activeSource) { activeSource.close(); activeSource = null; }

            setRunning(btn, true);
            logBox.textContent = '';
            status.textContent = 'Running…';
            status.className = '';

            const src = new EventSource('run.php?script=' + encodeURIComponent(key));
            activeSource = src;

            src.onmessage = function(e) {
                if (e.data === '__DONE__') {
                    src.close();
                    activeSource = null;
                    setRunning(btn, false);
                    status.textContent = 'Completed.';
                    status.className = 'done';
                    return;
                }
                logBox.textContent += e.data + '\n';
                logBox.scrollTop = logBox.scrollHeight;
            };

            src.onerror = function() {
                src.close();
                activeSource = null;
                setRunning(btn, false);
                if (status.textContent === 'Running…') {
                    status.textContent = 'Connection lost.';
                    status.className = 'error';
                }
            };
        }
    </script>
</body>
</html>
