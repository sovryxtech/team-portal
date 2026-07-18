<?php
declare(strict_types=1);

/**
 * Mail SMTP Configuration settings
 */
return [
    'smtp_host'    => 'smtp.gmail.com', // Default Mailtrap Sandbox
    'smtp_port'    => 587,
    'smtp_auth'    => true,
    'smtp_user'    => 'sovryx.tech@gmail.com', // Leave empty to disable actual mailing or input credentials
    'smtp_pass'    => 'gypm utpn rbms deiy', 
    'smtp_secure'  => 'tls', // tls or ssl
    'from_email'   => 'sovryx.tech@gmail.com',
    'from_name'    => 'no-reply@sovryxtech.com.np',
    
    // Set to true to log emails to a local file instead of sending, useful for development
    'debug_mode'   => false, 
    'debug_log'    => __DIR__ . '/../logs/emails.log'
];
