<?php
$url = 'http://localhost/team-portal/api/employee_actions.php';
$data = ['action' => 'submit_ticket'];
$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
        'ignore_errors' => true
    ]
];
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
echo "RESPONSE:\n";
echo $result;
echo "\nHEADERS:\n";
print_r($http_response_header);
