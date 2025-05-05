#!/bin/bash
# SMS Dashboard Script
# A central management script for SMS notification jobs
# Created on: May 5, 2025

# Location of all SMS scripts
SCRIPT_DIR="$(dirname "$0")"
LOG_DIR="$SCRIPT_DIR/logs"

# Create log directory if it doesn't exist
mkdir -p "$LOG_DIR"

# Function to display menu
show_menu() {
    clear
    echo "====================================================="
    echo "             SMS NOTIFICATION DASHBOARD              "
    echo "====================================================="
    echo "1. Run 4 o'clock scripts (Fleet renewal reminders)"
    echo "2. Run 5 o'clock scripts (Welcome and renewed notifications)"
    echo "3. Run 10 o'clock scripts (Non-fleet/motor renewal reminders)"
    echo "4. View logs"
    echo "0. Exit"
    echo "====================================================="
    echo -n "Please select an option [1-7]: "
}

# Function to run scripts with logging
run_script() {
    local script_name="$1"
    local log_file="$LOG_DIR/${script_name%.*}_$(date +%Y%m%d_%H%M%S).log"
    
    echo "Running $script_name... Log: $log_file"
    bash "$SCRIPT_DIR/$script_name" | tee "$log_file"
    echo "Script execution completed!"
    echo "Press Enter to continue..."
    read
}

# Function to view logs
view_logs() {
    echo "Available logs:"
    ls -lt "$LOG_DIR" | head -n 20
    echo
    echo -n "Enter log filename to view (or press Enter to go back): "
    read log_choice
    
    if [ -n "$log_choice" ]; then
        if [ -f "$LOG_DIR/$log_choice" ]; then
            #nano "$LOG_DIR/$log_choice"
            if command -v nano >/dev/null 2>&1; then
                nano "$LOG_DIR/$log_choice"
            else
                less "$LOG_DIR/$log_choice"
            fi
        else
            echo "Log file not found!"
            sleep 2
        fi
    fi
}



# Function to check script status
check_status() {
    echo "Checking status of SMS scripts..."
    echo
    
    echo "Last run times:"
    echo "==============="
    for script in four_oclock_scripts.sh five_oclock_scripts.sh ten_oclock_scripts.sh; do
        last_log=$(ls -t "$LOG_DIR"/${script%.*}_* 2>/dev/null | head -n 1)
        if [ -n "$last_log" ]; then
            last_run=$(stat -c %y "$last_log" | cut -d' ' -f1,2)
            echo "$script: $last_run"
        else
            echo "$script: Never run"
        fi
    done
    
    echo
    echo "Process status:"
    echo "=============="
    ps aux | grep -E 'php74 -f /var/www/html/sms-scripts' | grep -v grep
    
    echo
    echo "Press Enter to continue..."
    read
}

# Make scripts executable
chmod +x "$SCRIPT_DIR/four_oclock_scripts.sh"
chmod +x "$SCRIPT_DIR/five_oclock_scripts.sh"
chmod +x "$SCRIPT_DIR/ten_oclock_scripts.sh"

# Main loop
while true; do
    show_menu
    read choice
    
    case $choice in
        1) run_script "four_oclock_scripts.sh" ;;
        2) run_script "five_oclock_scripts.sh" ;;
        3) run_script "ten_oclock_scripts.sh" ;;
        4) view_logs ;;
        0) echo "Exiting SMS Dashboard. Goodbye!"; exit 0 ;;
        *) echo "Invalid option. Press Enter to continue..."; read ;;
    esac
done