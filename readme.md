# SMS Scripts Dashboard - Installation Instructions

## Setup Steps

1. **Save all scripts to a dedicated directory**
   ```bash
   mkdir -p /opt/sms-scripts
   ```

2. **Copy all script files to the directory**
   - Copy `four_oclock_scripts.sh`, `five_oclock_scripts.sh`, `ten_oclock_scripts.sh`, and `sms_dashboard.sh` to `/opt/sms-scripts/`

3. **Make all scripts executable**
   ```bash
   chmod +x /opt/sms-scripts/*.sh
   ```

## Running the Dashboard

1. **Start the SMS Dashboard**
   ```bash
   cd /opt/sms-scripts
   ./sms_dashboard.sh
   ```

   ```

## Dashboard Features

The SMS Dashboard provides the following features:

1. **Run scripts on demand**
   - Manually execute any of the three script batches

2. **View logs**
   - Check execution logs for troubleshooting


## Troubleshooting

If you encounter issues:

1. Check the log files in `logs`
2. Verify PHP 7.4 is installed and accessible via the `php74` command
3. Ensure proper permissions for script and log directories
4. Verify all script paths are correct for your environment

## Notes

- The scripts are organized by execution time (4, 5, and 10 o'clock)
- Each script writes detailed logs with timestamps
- You can customize the scripts as needed for your environment