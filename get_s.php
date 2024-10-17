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

// sendService1($url1, $params1);

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
        $position1 = strpos($response,"messageid=");
        $position2 = strpos($response,"&USERNAME=");
        $pos1new = $position1 + 10;
        $id = substr($response,$pos1new,$position2-$pos1new);

        $url3 = 'http://172.21.3.32/goip/en/resend.php'; // Replace with your target URL
        $params3 = [
            'messageid' => $id,
            'USERNAME' => 'sms-api',
            'PASSWORD' => '5m5-AP1',
        ];

        usleep(200000);

        sendService3($url3, $params3);
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
    'Memo' => 'chatgpt3',
    'smsprovider' => '1',
    'method' => '2',
];

// sendService2($url2, $params2);

function sendService3(string $url, array $params): void {
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

function insertApi($mysqli, $table, $data) {
    // Prepare column names and placeholders for the SQL statement
    $columns = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_fill(0, count($data), '?'));

    // Construct the SQL statement
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";

    // Prepare the statement
    if ($stmt = $mysqli->prepare($sql)) {
        // Dynamically bind parameters
        $types = str_repeat('s', count($data)); // Assuming all data are strings
        $stmt->bind_param($types, ...array_values($data));

        // Execute the statement
        if ($stmt->execute()) {
            return $stmt->insert_id; // Return the ID of the inserted row
        } else {
            return "Error: " . $stmt->error;
        }
    } else {
        return "Error: " . $mysqli->error;
    }
}

// Usage example:
// $mysqli = new mysqli("localhost", "janrey.dumaog", "janr3yD", "sms_api");

// $data = [
//     'reply' => '1',
//     'sender' => '2',
//     'phonenumber' => '3',
//     'message' => '4',
//     'token' => '5',
//     'servicetype' => '6',
//     'messagefrom' => '7',
// ];

// $result = insertApi($mysqli, 'api_log', $data);

// if (is_numeric($result)) {
//     echo "Data inserted successfully with ID: $result";
// } else {
//     echo $result; // Print the error message
// }

// $mysqli->close();


function checkAuth($mysqli, $whereToken, $whereAddress) {
    // Prepare the SQL statement
    $sql = "SELECT id FROM token WHERE token = ? AND address = ? AND active = 1";

    // Prepare the statement
    if ($stmt = $mysqli->prepare($sql)) {
        // Bind the parameter
        $stmt->bind_param('ss', $whereToken, $whereAddress); // Assuming the whereValue is a string

        // Execute the statement
        $stmt->execute();

        // Bind the result variable
        $stmt->bind_result($result);

        // Fetch the result
        if ($stmt->fetch()) {
            return $result; // Return the fetched value
        } else {
            return null; // Return null if no result is found
        }
    } else {
        return "Error: " . $mysqli->error; // Return error message if statement preparation fails
    }
}

// Usage example:
$mysqli = new mysqli("localhost", "janrey.dumaog", "janr3yD", "sms_api");

// "SELECT * FROM token WHERE token = :token AND address = :address AND active = 1"
$whereToken = '7ca04a3af82999d90c60f06cf3780d99';
$whereAddress = '103.62.153.11';

$result = checkAuth($mysqli, $whereToken, $whereAddress);

if ($result !== null) {
    echo "Authorized.";
} else {
    echo "Unauthorized.";
}

$mysqli->close();
?>