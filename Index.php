<?php
header('Content-Type: application/json');

// Function to get client IP address
function get_ip() {
    if (getenv('HTTP_CLIENT_IP'))
        return getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR'))
        return getenv('HTTP_X_FORWARDED_FOR');
    else if (getenv('HTTP_X_FORWARDED'))
        return getenv('HTTP_X_FORWARDED');
    else if (getenv('HTTP_FORWARDED_FOR'))
        return getenv('HTTP_FORWARDED_FOR');
    else if (getenv('HTTP_FORWARDED'))
        return getenv('HTTP_FORWARDED');
    else if (getenv('REMOTE_ADDR'))
        return getenv('REMOTE_ADDR');
    else
        return 'UNKNOWN';
}

// Get client IP address
$clientIP = get_ip();
if ($clientIP == '::1') {
    $clientIP = '8.8.8.8'; // Default to Google's public DNS server for local testing
}

$visitorName = isset($_GET['visitor_name']) ? htmlspecialchars($_GET['visitor_name']) : 'Visitor';

// Use ipinfo.io API to get location information
$ipinfoToken = '8c8beab3595e6c';
$ipinfoUrl = "https://ipinfo.io/{$clientIP}?token={$ipinfoToken}";
$locationData = json_decode(file_get_contents($ipinfoUrl), true);

$city = isset($locationData['city']) ? $locationData['city'] : 'Unknown Location';

// If the city is unknown, set a default city for weather information
if ($city === 'Unknown Location') {
    $city = 'New York'; // Default city
}

// Use OpenWeatherMap API to get temperature information
$weatherApiKey = 'e9c7326922d0a6298549bb56ddb8e8ee';
$weatherUrl = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$weatherApiKey}&units=metric";
$weatherData = json_decode(file_get_contents($weatherUrl), true);

$temperature = isset($weatherData['main']['temp']) ? $weatherData['main']['temp'] : 'Unknown';
$greeting = "Hello, $visitorName!, the temperature is $temperature degrees Celsius in $city";

$response = [
    'client_ip' => $clientIP,
    'location' => $city,
    'greeting' => $greeting,
];

echo json_encode($response);
?>
