<?php
require_once __DIR__ . '/../includes/db_connection.php';

$pdo = get_db_connection();

$templates = [
    [
        'name' => 'Welcome Email',
        'subject' => 'Welcome to Sovryx Team Portal',
        'body' => '<p>Dear {{employee_name}},</p><p>Welcome to <strong>{{company_name}}</strong>!</p><p>Your employee account has been successfully created.</p><p><strong>Employee ID:</strong> {{employee_id}}<br><strong>Department:</strong> {{department}}<br><strong>Designation:</strong> {{designation}}</p><p>You can access your employee portal here:<br><a href="{{portal_url}}">{{portal_url}}</a></p><p>We\'re excited to have you on our team. If you have any questions, please contact the HR department.</p><p>Best Regards,<br><strong>{{company_name}} HR Team</strong></p>',
        'variables' => 'employee_name,company_name,employee_id,department,designation,portal_url',
        'is_active' => 1
    ],
    [
        'name' => 'Registration Submitted',
        'subject' => 'Registration Submitted Successfully',
        'body' => '<p>Dear {{employee_name}},</p><p>Thank you for registering with <strong>{{company_name}}</strong>.</p><p>Your application has been received successfully and is currently under review.</p><p>Current Status: <strong>Pending Review</strong></p><p>You will receive another email once our HR team reviews your application.</p><p>Thank you,<br><strong>{{company_name}}</strong></p>',
        'variables' => 'employee_name,company_name',
        'is_active' => 1
    ],
    [
        'name' => 'Registration Approved',
        'subject' => 'Congratulations! Your Registration Has Been Approved',
        'body' => '<p>Dear {{employee_name}},</p><p>Congratulations!</p><p>Your registration has been approved.</p><p>Employee ID: {{employee_id}}</p><p>Login Here:<br><a href="{{portal_url}}">{{portal_url}}</a></p><p>Welcome to the team!</p><p>Regards,<br>HR Department</p>',
        'variables' => 'employee_name,employee_id,portal_url',
        'is_active' => 1
    ],
    [
        'name' => 'Registration Rejected',
        'subject' => 'Registration Update',
        'body' => '<p>Dear {{employee_name}},</p><p>Thank you for your interest.</p><p>After reviewing your application, we are unable to approve your registration at this time.</p><p>Reason:<br>{{rejection_reason}}</p><p>If you believe this is an error, please contact HR.</p><p>Regards,<br>HR Department</p>',
        'variables' => 'employee_name,rejection_reason',
        'is_active' => 1
    ],
    [
        'name' => 'Account Activated',
        'subject' => 'Your Employee Account Is Now Active',
        'body' => '<p>Dear {{employee_name}},</p><p>Your employee account has been activated successfully.</p><p>You may now log in using your credentials.</p><p>Portal:<br><a href="{{portal_url}}">{{portal_url}}</a></p><p>Regards,<br>HR Team</p>',
        'variables' => 'employee_name,portal_url',
        'is_active' => 1
    ],
    [
        'name' => 'Account Deactivated',
        'subject' => 'Account Status Updated',
        'body' => '<p>Dear {{employee_name}},</p><p>Your employee account has been deactivated.</p><p>If you believe this was done by mistake, please contact HR.</p><p>Regards,<br>{{company_name}}</p>',
        'variables' => 'employee_name,company_name',
        'is_active' => 1
    ],
    [
        'name' => 'Employee ID Generated',
        'subject' => 'Your Employee ID Has Been Generated',
        'body' => '<p>Dear {{employee_name}},</p><p>Your Employee ID has been created.</p><p>Employee ID:<br>{{employee_id}}</p><p>You can download your digital ID card from the employee portal.</p><p>Regards,<br>HR Department</p>',
        'variables' => 'employee_name,employee_id',
        'is_active' => 1
    ],
    [
        'name' => 'Password Reset',
        'subject' => 'Reset Your Password',
        'body' => '<p>Dear {{employee_name}},</p><p>We received a request to reset your password.</p><p>Click the link below:</p><p><a href="{{reset_link}}">{{reset_link}}</a></p><p>If you didn\'t request this, you can safely ignore this email.</p><p>Regards,<br>{{company_name}}</p>',
        'variables' => 'employee_name,reset_link,company_name',
        'is_active' => 1
    ],
    [
        'name' => 'Password Changed',
        'subject' => 'Password Changed Successfully',
        'body' => '<p>Dear {{employee_name}},</p><p>Your password has been changed successfully.</p><p>If you did not perform this action, please contact HR immediately.</p><p>Regards,<br>Security Team</p>',
        'variables' => 'employee_name',
        'is_active' => 1
    ],
    [
        'name' => 'Email Verification',
        'subject' => 'Verify Your Email Address',
        'body' => '<p>Dear {{employee_name}},</p><p>Please verify your email address.</p><p>Verification Link:</p><p><a href="{{verification_link}}">{{verification_link}}</a></p><p>This link expires in {{expiry_time}}.</p><p>Regards,<br>{{company_name}}</p>',
        'variables' => 'employee_name,verification_link,expiry_time,company_name',
        'is_active' => 1
    ],
    [
        'name' => 'Document Request',
        'subject' => 'Additional Documents Required',
        'body' => '<p>Dear {{employee_name}},</p><p>HR requires the following document(s):</p><p>{{document_list}}</p><p>Please upload them before:</p><p>{{deadline}}</p><p>Regards,<br>HR Team</p>',
        'variables' => 'employee_name,document_list,deadline',
        'is_active' => 1
    ],
    [
        'name' => 'Document Approved',
        'subject' => 'Documents Verified Successfully',
        'body' => '<p>Dear {{employee_name}},</p><p>Your uploaded documents have been reviewed and approved.</p><p>No further action is required.</p><p>Regards,<br>HR Department</p>',
        'variables' => 'employee_name',
        'is_active' => 1
    ],
    [
        'name' => 'Document Rejected',
        'subject' => 'Document Verification Failed',
        'body' => '<p>Dear {{employee_name}},</p><p>One or more uploaded documents require correction.</p><p>Reason:<br>{{remarks}}</p><p>Please upload the corrected documents.</p><p>Regards,<br>HR Department</p>',
        'variables' => 'employee_name,remarks',
        'is_active' => 1
    ],
    [
        'name' => 'New Announcement',
        'subject' => 'New Company Announcement',
        'body' => '<p>Dear {{employee_name}},</p><p>A new announcement has been published.</p><p>Title:<br>{{announcement_title}}</p><p>View it in your employee portal.</p><p>Regards,<br>Management</p>',
        'variables' => 'employee_name,announcement_title',
        'is_active' => 1
    ],
    [
        'name' => 'Company News',
        'subject' => 'Latest Company News',
        'body' => '<p>Dear {{employee_name}},</p><p>A new company news article has been published.</p><p>Title:<br>{{news_title}}</p><p>Read more:<br><a href="{{portal_url}}">{{portal_url}}</a></p><p>Regards,<br>{{company_name}}</p>',
        'variables' => 'employee_name,news_title,portal_url,company_name',
        'is_active' => 1
    ],
    [
        'name' => 'Event Invitation',
        'subject' => 'You\'re Invited – {{event_name}}',
        'body' => '<p>Dear {{employee_name}},</p><p>You are invited to attend:</p><p>Event:<br>{{event_name}}</p><p>Date:<br>{{event_date}}</p><p>Time:<br>{{event_time}}</p><p>Location:<br>{{event_location}}</p><p>We look forward to seeing you.</p><p>Regards,<br>Management</p>',
        'variables' => 'employee_name,event_name,event_date,event_time,event_location',
        'is_active' => 1
    ],
    [
        'name' => 'Profile Incomplete',
        'subject' => 'Complete Your Employee Profile',
        'body' => '<p>Dear {{employee_name}},</p><p>Your employee profile is incomplete.</p><p>Please log in and complete the missing information.</p><p>Portal:<br><a href="{{portal_url}}">{{portal_url}}</a></p><p>Regards,<br>HR Team</p>',
        'variables' => 'employee_name,portal_url',
        'is_active' => 1
    ],
    [
        'name' => 'Login Alert',
        'subject' => 'New Login Detected',
        'body' => '<p>Dear {{employee_name}},</p><p>A new login to your account was detected.</p><p>Device:<br>{{device}}</p><p>IP Address:<br>{{ip_address}}</p><p>Time:<br>{{login_time}}</p><p>If this wasn\'t you, change your password immediately.</p><p>Regards,<br>Security Team</p>',
        'variables' => 'employee_name,device,ip_address,login_time',
        'is_active' => 1
    ],
    [
        'name' => 'Support Ticket Received',
        'subject' => 'Support Request Received',
        'body' => '<p>Dear {{employee_name}},</p><p>Your support request has been received.</p><p>Ticket ID:<br>{{ticket_id}}</p><p>Our team will respond as soon as possible.</p><p>Regards,<br>Support Team</p>',
        'variables' => 'employee_name,ticket_id',
        'is_active' => 1
    ],
    [
        'name' => 'General Notification',
        'subject' => 'Notification from {{company_name}}',
        'body' => '<p>Dear {{employee_name}},</p><p>{{message}}</p><p>Regards,<br><br><strong>{{company_name}}</strong></p>',
        'variables' => 'employee_name,company_name,message',
        'is_active' => 1
    ]
];

$stmt = $pdo->prepare("INSERT INTO email_templates (name, subject, body, variables, is_active) 
                       VALUES (:name, :subject, :body, :variables, :is_active)
                       ON DUPLICATE KEY UPDATE 
                       subject = VALUES(subject), 
                       body = VALUES(body), 
                       variables = VALUES(variables),
                       is_active = VALUES(is_active)");

foreach ($templates as $t) {
    $stmt->execute([
        'name' => $t['name'],
        'subject' => $t['subject'],
        'body' => $t['body'],
        'variables' => $t['variables'],
        'is_active' => $t['is_active']
    ]);
}

echo "Seeded " . count($templates) . " templates successfully.\n";
