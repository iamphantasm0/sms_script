<?php
session_start();
require_once __DIR__ . '/config.php';

if (!($_SESSION['authed'] ?? false)) {
    http_response_code(403);
    exit('Forbidden');
}

$key = $_GET['script'] ?? '';
$scripts = SCRIPTS;

if (!isset($scripts[$key])) {
    http_response_code(400);
    exit('Unknown script.');
}

$scriptPath = $scripts[$key];
$timestamp  = date('Ymd_His');
$logFile    = LOG_DIR . $key . '_' . $timestamp . '.log';

// Disable all buffering layers before setting SSE headers.
@ini_set('output_buffering', 'off');
@ini_set('zlib.output_compression', false);
while (@ob_end_flush()) {}
ob_implicit_flush(true);

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('X-Accel-Buffering: no');
header('Connection: keep-alive');

// Send one SSE line; multi-line text is split across multiple data: fields.
function sse(string $text): void {
    foreach (explode("\n", rtrim($text, "\n")) as $part) {
        echo 'data: ' . $part . "\n";
    }
    echo "\n";
    flush();
}

$fh = @fopen($logFile, 'w');

$startLine = '--- Started at ' . date('Y-m-d H:i:s') . ' ---';
sse($startLine);
if ($fh) fwrite($fh, $startLine . "\n");

$proc = popen('sudo bash ' . escapeshellarg($scriptPath) . ' 2>&1', 'r');

if (!$proc) {
    sse('ERROR: Failed to start script.');
    sse('__DONE__');
    if ($fh) fclose($fh);
    exit;
}

while (!feof($proc)) {
    $line = fgets($proc);
    if ($line === false) break;
    sse($line);
    if ($fh) fwrite($fh, $line);
}

pclose($proc);

$endLine = '--- Completed at ' . date('Y-m-d H:i:s') . ' ---';
sse($endLine);
if ($fh) {
    fwrite($fh, $endLine . "\n");
    fclose($fh);
}

sse('__DONE__');
