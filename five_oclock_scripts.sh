#!/bin/bash
# Script to run all 5 o'clock SMS scripts
# Created on: May 5, 2025

echo "Starting 5 o'clock SMS scripts at $(date)"

# Fleet Welcome Script
echo "Running fleet welcome script..."
php74 -f /var/www/html/sms-script/scripts-fleets/fleet-welcome.php

# Non-Fleet Welcome Script
echo "Running non-fleet welcome script..."
php74 -f /var/www/html/sms-scripts/scripts-non-fleets/nonfleet-welcome.php

# Fleet Renewed Script
echo "Running fleet renewed script..."
php74 -f /var/www/html/sms-scripts/scripts-fleets/fleetrenewed.php

# Non-Fleet Renewed Script
echo "Running non-fleet renewed script..."
php74 -f /var/www/html/sms-scripts/scripts-non-fleets/nonfleetrenewed.php

# Non-Motor Renewed Script
echo "Running non-motor renewed script..."
php74 -f /var/www/html/sms-scripts/scripts-non-motor/renewed-non-motor.php

echo "All 5 o'clock SMS scripts completed at $(date)"