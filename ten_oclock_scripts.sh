#!/bin/bash
# Script to run all 10 o'clock SMS scripts
# Created on: May 5, 2025

echo "Starting 10 o'clock SMS scripts at $(date)"

# Non-Fleet Renewal 60 Days Script
echo "Running non-fleet renewal 60 days reminder script..."
php74 -f /var/www/html/sms-scripts/scripts-non-fleets/nonfleetrenewal60.php

# Non-Fleet Renewal 30 Days Script
echo "Running non-fleet renewal 30 days reminder script..."
php74 -f /var/www/html/sms-scripts/scripts-non-fleets/nonfleetrenewal30.php

# Non-Fleet Renewal 15 Days Script
echo "Running non-fleet renewal 15 days reminder script..."
php74 -f /var/www/html/sms-scripts/scripts-non-fleets/nonfleetrenewal15.php

# Non-Fleet Renewal 1 Day Script
echo "Running non-fleet renewal 1 day reminder script..."
php74 -f /var/www/html/sms-scripts/scripts-non-fleets/nonfleetrenewal1.php

# Non-Motor Renewal 60 Days Script
echo "Running non-motor renewal 60 days reminder script..."
php74 -f /var/www/html/sms-scripts/scripts-non-motor/renewal-reminder-non-motor-60day.php

# Non-Motor Renewal 30 Days Script
echo "Running non-motor renewal 30 days reminder script..."
php74 -f /var/www/html/sms-scripts/scripts-non-motor/renewal-reminder-non-motor-30day.php

# Non-Motor Renewal 15 Days Script
echo "Running non-motor renewal 15 days reminder script..."
php74 -f /var/www/html/sms-scripts/scripts-non-motor/renewal-reminder-non-motor-15day.php

# Non-Motor Renewal 1 Day Script
echo "Running non-motor renewal 1 day reminder script..."
php74 -f /var/www/html/sms-scripts/scripts-non-motor/renewal-reminder-non-motor-1day.php

echo "All 10 o'clock SMS scripts completed at $(date)"