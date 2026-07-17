<?php
declare(strict_types=1);

/**
 * Mail SMTP Configuration settings
 */
return [
    'smtp_host'    => 'sandbox.smtp.mailtrap.io', // Default Mailtrap Sandbox
    'smtp_port'    => 2525,
    'smtp_auth'    => true,
    'smtp_user'    => '', // Leave empty to disable actual mailing or input credentials
    'smtp_pass'    => '', 
    'smtp_secure'  => 'tls', // tls or ssl
    'from_email'   => 'noreply@sovryxtech.com.np',
    'from_name'    => 'Sovryx Tech Employee Portal',
    
    // Set to true to log emails to a local file instead of sending, useful for development
    'debug_mode'   => true, 
    'debug_log'    => __DIR__ . '/../logs/emails.log'
];
