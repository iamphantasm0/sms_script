<?php
session_start();
require_once __DIR__ . '/config.php';

if (!($_SESSION['authed'] ?? false)) {
    http_response_code(403);
    exit('Forbidden');
}

// Validate ?script= against the whitelist — never touch user input directly.
$key = $_GET['script'] ?? '';
$scripts = SCRIPTS;

if (!isset($scripts[$key])) {
    http_response_code(400);
    exit('Unknown script.');
}

$scriptPath = $scripts[$key];

// Build a log filename: e.g. logs/four_20250601_143022.log
$timestamp  = date('Ymd_His');
$logFile    = LOG_DIR . $key . '_' . $timestamp . '.log';

// Disable all output buffering layers so the browser sees each line immediately.
@ini_set('output_buffering', 'off');
@ini_set('zlib.output_compression', false);
while (@ob_end_flush()) {}

header('Content-Type: text/plain; charset=utf-8');
header('X-Accel-Buffering: no');   // Tells nginx/proxies not to buffer this response.
header('Cache-Control: no-cache');

$fh = @fopen($logFile, 'w');

$startLine = '--- Started at ' . date('Y-m-d H:i:s') . " ---\n";
echo $startLine;
if ($fh) fwrite($fh, $startLine);
flush();

// Open the script as a readable pipe. 2>&1 merges stderr into stdout.
$proc = popen('bash ' . escapeshellarg($scriptPath) . ' 2>&1', 'r');

if (!$proc) {
    $msg = "ERROR: Failed to start script.\n";
    echo $msg;
    if ($fh) { fwrite($fh, $msg); fclose($fh); }
    exit;
}

while (!feof($proc)) {
    $line = fgets($proc);
    if ($line === false) break;
    echo $line;
    if ($fh) fwrite($fh, $line);
    flush();
}

pclose($proc);

$endLine = '--- Completed at ' . date('Y-m-d H:i:s') . " ---\n";
echo $endLine;
if ($fh) {
    fwrite($fh, $endLine);
    fclose($fh);
}
