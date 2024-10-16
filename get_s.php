<?php

function sendService1(string $url, array $params): void {
    // Build query string from the parameters array
    $queryString = http_build_query($params);
    
    // Append query string to the URL
    $fullUrl = $url . '?' . $queryString;

    // Initialize cURL session
    $ch = curl_init($fullUrl);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string

    // Execute the GET request
    $response = curl_exec($ch);

    // Check for errors
    if ($response === false) {
        echo 'cURL Error: ' . curl_error($ch);
    } else {
        echo 'Response: ' . $response; // Display the response
    }

    // Close cURL session
    curl_close($ch);
}

// Example usage
$url1 = 'http://172.21.3.18/sendsms'; // Replace with your target URL
$params1 = [
    'username' => 'ovsms',
    'password' => 'ovSMS@2020',
    'phonenumber' => '09670266317',
    'message' => 'chatgpt1',
    'id' => 'missmsapi',
];

#sendService1($url1, $params1);


function sendService2(string $url, array $params): void {
    // Build query string from the parameters array
    $queryString = http_build_query($params);
    
    // Append query string to the URL
    $fullUrl = $url . '?' . $queryString;

    // Initialize cURL session
    $ch = curl_init($fullUrl);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string

    // Execute the GET request
    $response = curl_exec($ch);

    // Check for errors
    if ($response === false) {
        echo 'cURL Error: ' . curl_error($ch);
    } else {
        // echo 'Response: ' . $response; // Display the response
        // preg_match("/messageid=/", $response, $result);
        $position1 = strpos($response,"messageid=");
        $position2 = strpos($response,"&USERNAME=");
        $pos1new = $position1 + 10;
        $id = substr($response,$pos1new,$position2-$pos1new);
        #echo $pos1new . " | " . $position2 . " | " . $id;
    }

    // Close cURL session
    curl_close($ch);
}

// Example usage
$url2 = 'http://172.21.3.32/goip/en/dosend.php'; // Replace with your target URL
$params2 = [
    'USERNAME' => 'sms-api',
    'PASSWORD' => '5m5-AP1',
    'smsnum' => '09670266317',
    'Memo' => 'chatgpt2',
    'smsprovider' => '1',
    'method' => '2',
];

$url3 = 'http://172.21.3.32/goip/en/dosend.php'; // Replace with your target URL
$params3 = [
    'USERNAME' => 'sms-api',
    'PASSWORD' => '5m5-AP1',
    'smsnum' => '09670266317',
    'Memo' => 'chatgpt2',
    'smsprovider' => '1',
    'method' => '2',
];

sendService2($url2, $params2);

?>