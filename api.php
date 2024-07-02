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

// Function to parse visitor name from query parameter
function get_visitor_name() {
    return isset($_GET['visitor_name']) ? htmlspecialchars($_GET['visitor_name']) : 'Visitor';
}

// Function to handle /api/hello endpoint
function handle_api_hello() {
    $clientIP = get_ip();

    // Replace '::1' with a default IP if testing locally
    if ($clientIP == '::1') {
        $clientIP = '8.8.8.8'; // Default to Google's public DNS server for local testing
    }

    $visitorName = get_visitor_name();

    // Fetch location data using ipinfo.io
    $ipinfoToken = '8c8beab3595e6c';
    $ipinfoUrl = "https://ipinfo.io/{$clientIP}?token={$ipinfoToken}";
    $locationData = json_decode(file_get_contents($ipinfoUrl), true);

    $city = isset($locationData['city']) ? $locationData['city'] : 'Unknown Location';

    if ($city === 'Unknown Location') {
        $city = 'New York'; // Default city
    }

    // Fetch weather data using OpenWeatherMap API
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
}

// Main routing logic based on query parameter
if (isset($_GET['route']) && $_GET['route'] === 'api/hello') {
    handle_api_hello();
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}
?>
