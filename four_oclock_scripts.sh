#!/bin/bash
# Script to run all 4 o'clock SMS scripts
# Created on: May 5, 2025

echo "Starting 4 o'clock SMS scripts at $(date)"

# Fleet Renewal 60 Days Script
echo "Running fleet renewal 60 days reminder script..."
php74 -f /var/www/html/sms-scripts/scripts-fleets/fleetrenewal60.php

# Fleet Renewal 30 Days Script
echo "Running fleet renewal 30 days reminder script..."
php74 -f /var/www/html/sms-scripts/scripts-fleets/fleetrenewal30.php

# Fleet Renewal 15 Days Script
echo "Running fleet renewal 15 days reminder script..."
php74 -f /var/www/html/sms-scripts/scripts-fleets/fleetrenewal15.php

# Fleet Renewal 1 Day Script
echo "Running fleet renewal 1 day reminder script..."
php74 -f /var/www/html/sms-scripts/scripts-fleets/fleetrenewal1.php

echo "All 4 o'clock SMS scripts completed at $(date)"