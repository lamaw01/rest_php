<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);

function showGuide(string $message = '') {
    $guideFile = fopen("guide.txt", "r") or die("Unable to open file!");
    
    $guideText = fread($guideFile,filesize("guide.txt"));

    fclose($guideFile);

    return $message . $guideText;
}

function writeLog(string $message = ''): void {
    $logFile = fopen("log.txt", "a") or die("Unable to open file!");

    $newline = "\n";
    
    fwrite($logFile, $message . $newline);

    fclose($logFile);
}

// writeLog('write log init');
// exit;

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo showGuide('Method Not Allowed');
    exit;
}

// Get parameters from the URL
$phonenumber = isset($_GET['phonenumber']) ? urlencode($_GET['phonenumber']) : null;
$message = isset($_GET['message']) ? $_GET['message'] : null;
$token = isset($_GET['token']) ? $_GET['token'] : null;

$messagefrom = isset($_GET['messagefrom']) ? $_GET['messagefrom'] : '';
$servicetype = isset($_GET['servicetype']) ? $_GET['servicetype'] : '1';
$cilentIp = $_SERVER['REMOTE_ADDR'];

// Define required parameters
$requiredParams = ['phonenumber' => $phonenumber, 'message' => $message, 'token' => $token];
$optionalParams = ['messagefrom' => $messagefrom, 'servicetype' => $servicetype, 'cilentIp' => $cilentIp];

// Check if all required parameters are present
foreach ($requiredParams as $param => $value) {
    if (is_null($value)) {
        header('HTTP/1.1 400 Bad Request');
        // echo json_encode(['error' => "Missing parameter: $param", 'message'=>$guideJson]);
        echo showGuide("Missing parameter: $param");
        exit;
    }
}

// If everything is fine, you can proceed with your logic
// header('HTTP/1.1 200 OK');
// echo json_encode(['message' => 'Request successful', 'requiredParams' => $requiredParams, 'optionalParams' => $optionalParams]);
// exit;

// Check ip and token
checkAuth($token, $cilentIp);

// OpenVox
if($servicetype == '1'){
    // Example usage
    $url1 = 'http://172.21.3.18/sendsms'; // Replace with your target URL

    $params1 = [
        'username' => 'ovsms',
        'password' => 'ovSMS@2020',
        'phonenumber' => $phonenumber,
        'message' => $message,
        'id' => 'missmsapi',
    ];

    $response = sendOpenvox($url1, $params1);

    $data = [
        'reply' => $response === NULL ? '' : $response,
        'sender' => $messagefrom,
        'phonenumber' => $phonenumber,
        'message' => $message,
        'token' => $token,
        'servicetype' => $servicetype,
        'messagefrom' => $messagefrom,
    ];

    $result = insertApiLog('api_log', $data);

    header('HTTP/1.1 200 OK');
    echo showGuide("Request successful, id: $result");

}

// GoIp
else if($servicetype == '2'){
    // Example usage
    $url2 = 'https://172.21.3.32/goip/en/dosend.php'; // Replace with your target URL

    $params2 = [
        'USERNAME' => 'sms-api',
        'PASSWORD' => '5m5-AP1',
        'smsnum' => $phonenumber,
        'Memo' => $message,
        'smsprovider' => '1',
        'method' => '2',
    ];

    $response = sendGoip1($url2, $params2);

    // header('HTTP/1.1 200 OK');
    // echo showGuide("Response: $response");

    $data = [
        'reply' => $response === NULL ? '' : $response,
        'sender' => $messagefrom,
        'phonenumber' => $phonenumber,
        'message' => $message,
        'token' => $token,
        'servicetype' => $servicetype,
        'messagefrom' => $messagefrom,
    ];

    $result = insertApiLog('api_log', $data);

    header('HTTP/1.1 200 OK');
    echo showGuide("Request successful, id: $result");
    
}

else{

    header('HTTP/1.1 200 OK');
    echo showGuide("Invalid Service type");

}

function sendOpenvox(string $url, array $params) {
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
        // echo 'cURL Error: ' . curl_error($ch);
        $error = 'sendOpenvox cURL Error: ' . curl_error($ch);

        writeLog($error);

    } else {
        // echo 'Response: ' . $response; // Display the response
        return $response;
    }
    
    // Close cURL session
    curl_close($ch);
}

function sendGoip1(string $url, array $params) {
    // Build query string from the parameters array
    $queryString = http_build_query($params);
    
    // Append query string to the URL
    $fullUrl = $url . '?' . $queryString;

    // Initialize cURL session
    $ch = curl_init($fullUrl);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  // Disable SSL verification for the host
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  // Disable SSL verification for the peer

    // Execute the GET request
    $response = curl_exec($ch);

    // Check for errors
    if ($response === false) {
        // echo 'cURL Error: ' . curl_error($ch);
        $error = 'sendGoip1 cURL Error: ' . curl_error($ch);

        writeLog($error);

    } else {
        // echo 'Response: ' . $response; // Display the response
        $position1 = strpos($response, "messageid=");
        $position2 = strpos($response, "&USERNAME=");

        $pos1new = $position1 + 10;

        $id = substr($response, $pos1new, $position2-$pos1new);

        $url3 = 'https://172.21.3.32/goip/en/resend.php'; // Replace with your target URL

        $params3 = [
            'messageid' => $id,
            'USERNAME' => 'sms-api',
            'PASSWORD' => '5m5-AP1',
        ];

        usleep(200000);

        $goip2Response = sendGoip2($url3, $params3);

        return $goip2Response;
    }

    // Close cURL session
    curl_close($ch);
}

function sendGoip2(string $url, array $params) {
    // Build query string from the parameters array
    $queryString = http_build_query($params);
    
    // Append query string to the URL
    $fullUrl = $url . '?' . $queryString;

    // Initialize cURL session
    $ch = curl_init($fullUrl);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  // Disable SSL verification for the host
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  // Disable SSL verification for the peer
    

    // Execute the GET request
    $response = curl_exec($ch);

    // Check for errors
    if ($response === false) {
        // echo 'cURL Error: ' . curl_error($ch);
        $error = 'sendGoip2 cURL Error: ' . curl_error($ch);

        writeLog($error);

    } else {
        // echo 'Response: ' . $response; // Display the response
        return $response;
    }

    // Close cURL session
    curl_close($ch);
}

function insertApiLog($table, $data) {
    // Usage example:
    $mysqli = new mysqli("localhost", "janrey.dumaog", "janr3yD", "sms_api");

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

            $error = "insertApiLog Error: " . $stmt->error;
            
            writeLog($error);

            return $error;
            
        }
    } else {
        
        $error = "insertApiLog Error: " . $mysqli->error;
            
        writeLog($error);

        return $error;
    }

    $mysqli->close();
}

function checkAuth($whereToken, $whereAddress) {
    // Usage example:
    $mysqli = new mysqli("localhost", "janrey.dumaog", "janr3yD", "sms_api");

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
            // return $result; // Return the fetched value
        } else {
            header('HTTP/1.1 401 Unauthorized');
            // echo json_encode(['error' => 'Unauthorized', 'message'=>$guideJson]);
            echo showGuide("Unauthorized");
            exit;
            // return null; // Return null if no result is found
        }
    } else {

        $error = "checkAuth Error: " . $mysqli->error;

        writeLog($error);

        return $error; // Return error message if statement preparation fails
    }

    $mysqli->close();
}


?>