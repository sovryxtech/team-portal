<?php
declare(strict_types=1);

/**
 * General Application Configuration
 */
return [
    'site_name' => 'Sovryx Tech Portal',
    'base_url'  => 'http://localhost/team-portal', // Update with actual local deployment URL
    'secret_key'=> 'c55bf1d3efde02787c808d4b31a84f39281a8b9d885a0ec7b80a7199', // Cryptographic salt/key
    
    // Security and Session settings
    'session_name' => 'SOVRYX_PORTAL_SESSION',
    'session_lifetime' => 3600, // 1 hour
    
    // Upload constraints
    'max_upload_size' => 5 * 1024 * 1024, // 5MB
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'pdf', 'docx'],
    'allowed_mime_types' => [
        'image/jpeg',
        'image/png',
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' // docx
    ],
    
    // Organization settings
    'default_company_id' => 1
];
