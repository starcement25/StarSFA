<?php

// Function to execute a shell command and capture output
function executeCommand($command) {
    return shell_exec($command);
}

// Function to generate a system health report
function generateSystemReport() {
    echo "<h2>System Health Report</h2>";

    // Check CPU usage
    echo "<h3>CPU Usage</h3>";
    echo "<pre>" . executeCommand("top -b -n 1") . "</pre>";

    // Check Memory usage
    echo "<h3>Memory Usage</h3>";
    echo "<pre>" . executeCommand("free -m") . "</pre>";

    // Check Disk space
    echo "<h3>Disk Space</h3>";
    echo "<pre>" . executeCommand("df -h") . "</pre>";

    // Add more checks as needed

    // Note: Be cautious when executing shell commands from PHP, especially if the script
    // runs with elevated privileges. Validate user inputs and sanitize data to prevent security risks.
}

// Execute the system health report function
generateSystemReport();
?>
