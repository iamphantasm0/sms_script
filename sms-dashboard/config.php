<?php

// ─── Credentials ─────────────────────────────────────────────────────────────
// Change 'yourpassword' to your actual password before deploying.
define('DASHBOARD_USER', 'admin');
define('DASHBOARD_PASS', password_hash('lasaco26!!', PASSWORD_BCRYPT));

// ─── Whitelisted scripts ──────────────────────────────────────────────────────
// Keys are used in the UI and as ?script= param values — never expose raw paths.
define('SCRIPTS', [
    'four' => '/root/sms_script/four_oclock_scripts.sh',
    'five' => '/root/sms_script/five_oclock_scripts.sh',
    'ten'  => '/root/sms_script/ten_oclock_scripts.sh',
]);

// ─── Log directory ────────────────────────────────────────────────────────────
define('LOG_DIR', '/opt/sms-dashboard/logs/');
