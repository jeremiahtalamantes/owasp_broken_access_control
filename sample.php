<?php

/**
 * 
 * SAMPLE CODE FOR OWASP BROKEN AUTHENTICATION
 * 
 * 1. Implement Server-Side Checks
 * 2. Deny by Default
 * 3. Reuse Access Control Mechanisms
 * 4. Enforce Record Ownership
 * 5. Limit CORS Usage
 * 6. Log & Monitor
 * 7. Rate Limiting
 * 8. Session Management
 * 
 */

    /**
     * IMPLEMENT SERVER-SIDE CHECKS
    */

    // Vulnerable
    public function admin_page() {
        // No role check, making it vulnerable
        $this->load->view('admin_page');
    }

    // Secure
    public function admin_page() {
        // Check user role before granting access
        if ($this->session->userdata('role') !== 'admin') {
            show_error('Access Denied', 403);
            return;
        }
        $this->load->view('admin_page');
    }

    /**
     * DENY BY DEFAULT
     */

    // Vulnerable
    public function access_resource() {
        // No role check, making it vulnerable
        $this->load->view('resource_page');
    }
    
    // Secure
    public function access_resource() {
        // Deny by default unless user is authenticated
        if (!$this->session->userdata('role')) {
            show_error('Access Denied', 403);
            return;
        }
        $this->load->view('resource_page');
    }

    /**
     * REUSE ACCESS CONTROL MECHANISMS
     */

     // Vulnerable
     // Repeating role checks in multiple functions, making it prone to errors

     // Secure
    private function check_role($required_role) {
        // Reusable function to check user role
        if ($this->session->userdata('role') !== $required_role) {
            show_error('Access Denied', 403);
            return false;
        }
        return true;
    }

    /**
     * ENFORCE RECORD OWNERSHIP
     */

    // Vulnerable
    public function edit_record($record_id) {
        // No ownership check, making it vulnerable
        // Update logic
    }
    
    // Secure
    public function edit_record($record_id) {
        // Check if the user is the owner of the record
        $record = $this->record_model->get_record($record_id);
        if ($record['owner_id'] !== $this->session->userdata('user_id')) {
            show_error('Access Denied', 403);
            return;
        }
        // Update logic
    }

    /**
     * LIMIT CORS USAGE
     */

    // Vulnerable
    // No CORS restrictions, making it vulnerable

    // Secure
    // In .htaccess or Apache config to limit CORS to specific origins
    Header set Access-Control-Allow-Origin "https://compliiant.io"

    /**
     * LOG & MONITOR
     */

    // Vulnerable
    // No logging, making it vulnerable

    // Secure
    // Log unauthorized access attempts
    log_message('error', 'Unauthorized access attempt by user ' . $this->session->userdata('user_id'));

    /**
     * RATE LIMITING
     */

    // Vulnerable
    public function login() {
        // No rate limiting, making it vulnerable
    }
    
    // Secure
    // Use a library like "CodeIgniter-Rate-Limiter" to implement rate limiting
    // or using something like the following...
    //
    // Start the session
    session_start();

    // Define constants for rate limiting
    define('TIME_PERIOD', 60); // Time period in seconds
    define('MAX_ATTEMPTS', 5); // Max number of attempts within the time period

    // Initialize session variables if they don't exist
    if (!isset($_SESSION['ATTEMPTS'])) {
        $_SESSION['ATTEMPTS'] = 0;
    }
    if (!isset($_SESSION['FIRST_ATTEMPT_TIME'])) {
        $_SESSION['FIRST_ATTEMPT_TIME'] = null;
    }

    // Function to check if the user is rate-limited
    function is_rate_limited() {
        $current_time = time();
        
        // If this is the first attempt, record the time
        if ($_SESSION['FIRST_ATTEMPT_TIME'] === null) {
            $_SESSION['FIRST_ATTEMPT_TIME'] = $current_time;
        }
        
        // If the user has exceeded the max number of attempts
        if ($_SESSION['ATTEMPTS'] >= MAX_ATTEMPTS) {
            // Check if the time period has passed
            if (($current_time - $_SESSION['FIRST_ATTEMPT_TIME']) > TIME_PERIOD) {
                // Reset the attempt count and time
                $_SESSION['ATTEMPTS'] = 0;
                $_SESSION['FIRST_ATTEMPT_TIME'] = $current_time;
            } else {
                // User is rate-limited
                return true;
            }
        }
        
        // User is not rate-limited
        return false;
    }

    // Simulated login function
    function login() {
        // Increment the attempt count
        $_SESSION['ATTEMPTS']++;
        
        // Your login logic here
        // ...
    }

    // Check if the user is rate-limited
    if (is_rate_limited()) {
        // Inform the user that they are rate-limited
        echo "You have exceeded the maximum number of login attempts. Please try again later.";
    } else {
        // Proceed with the login
        login();
        echo "Login successful (or failed, depending on your login logic)";
    }

    /**
     * SESSION MANAGEMENT
     */

    // Vulnerable
    // No session invalidation after logout, making it vulnerable

    // Secure
    // Invalidate session after logout
    public function logout() {
        $this->session->sess_destroy();
        redirect('login');
    }
