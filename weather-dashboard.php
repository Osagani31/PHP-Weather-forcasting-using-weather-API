<?php
// OpenWeatherMap API configuration
$apiKey = 'c6d8b35401b8def5176f15678f0fb6e4';
$city = 'Colombo';
$countryCode = 'LK';
$apiUrl = 'https://api.openweathermap.org/data/2.5/weather';

// Initialize variables
$weatherData = null;
$error = '';

// Build API request URL
$requestUrl = "$apiUrl?q=" . urlencode($city . ',' . $countryCode) . "&appid=$apiKey&units=metric";

// Make API request using cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $requestUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Process API response
if ($httpCode === 200) {
    $weatherData = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $error = 'Error parsing weather data.';
    }
} else {
    if ($httpCode === 404) {
        $error = "City '$city' not found.";
    } else if ($httpCode === 401) {
        $error = 'Invalid API key. Please check your configuration.';
    } else {
        $error = "Error fetching weather data. HTTP Code: $httpCode";
    }
}

// Function to get weather icon class based on condition code
function getWeatherIcon($code) {
    $iconMap = [
        '01d' => 'sun',
        '01n' => 'moon',
        '02d' => 'cloud-sun',
        '02n' => 'cloud-moon',
        '03d' => 'cloud',
        '03n' => 'cloud',
        '04d' => 'cloud',
        '04n' => 'cloud',
        '09d' => 'cloud-rain',
        '09n' => 'cloud-rain',
        '10d' => 'cloud-sun-rain',
        '10n' => 'cloud-moon-rain',
        '11d' => 'bolt',
        '11n' => 'bolt',
        '13d' => 'snowflake',
        '13n' => 'snowflake',
        '50d' => 'smog',
        '50n' => 'smog'
    ];
    
    return $iconMap[$code] ?? 'cloud';
}

// Sample forecast data
$forecast = [
    ['day' => 'Mon', 'icon' => 'cloud-sun', 'high' => 31, 'low' => 26],
    ['day' => 'Tue', 'icon' => 'cloud-rain', 'high' => 29, 'low' => 25],
    ['day' => 'Wed', 'icon' => 'sun', 'high' => 32, 'low' => 27],
    ['day' => 'Thu', 'icon' => 'cloud-sun', 'high' => 30, 'low' => 26],
    ['day' => 'Fri', 'icon' => 'bolt', 'high' => 28, 'low' => 25],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Dashboard - Colombo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container py-4">
        <div class="weather-card">
            <div class="weather-header">
                <h1 class="display-5"><i class="fas fa-cloud-sun me-2"></i> Weather Dashboard</h1>
                <p class="lead">Real-time weather information for Colombo, Sri Lanka</p>
            </div>
            
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $error; ?>
                    </div>
                <?php elseif ($weatherData): ?>
                    <div class="row align-items-center">
                        <div class="col-md-6 text-center">
                            <h2 class="city-name">Colombo, Sri Lanka</h2>
                            
                            <div class="weather-icon">
                                <i class="fas fa-<?php echo getWeatherIcon($weatherData['weather'][0]['icon']); ?>"></i>
                            </div>
                            
                            <div class="temperature"><?php echo round($weatherData['main']['temp']); ?>°C</div>
                            
                            <p class="weather-description lead text-capitalize">
                                <?php echo htmlspecialchars($weatherData['weather'][0]['description']); ?>
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <img src="colombo-sri-lanka.jpg" 
     alt="Colombo, Sri Lanka" class="city-image">
                        </div>
                    </div>
                    
                    <div class="row weather-details mt-4">
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card text-center p-3 h-100">
                                <div class="detail-icon">
                                    <i class="fas fa-temperature-high"></i>
                                </div>
                                <h5>Feels Like</h5>
                                <p class="mb-0 fs-5"><?php echo round($weatherData['main']['feels_like']); ?>°C</p>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card text-center p-3 h-100">
                                <div class="detail-icon">
                                    <i class="fas fa-tint"></i>
                                </div>
                                <h5>Humidity</h5>
                                <p class="mb-0 fs-5"><?php echo $weatherData['main']['humidity']; ?>%</p>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card text-center p-3 h-100">
                                <div class="detail-icon">
                                    <i class="fas fa-wind"></i>
                                </div>
                                <h5>Wind Speed</h5>
                                <p class="mb-0 fs-5"><?php echo $weatherData['wind']['speed']; ?> m/s</p>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card text-center p-3 h-100">
                                <div class="detail-icon">
                                    <i class="fas fa-compress-arrows-alt"></i>
                                </div>
                                <h5>Pressure</h5>
                                <p class="mb-0 fs-5"><?php echo $weatherData['main']['pressure']; ?> hPa</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="highlight-box mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h4><i class="fas fa-info-circle me-2"></i> Weather Summary</h4>
                                <p class="mb-1">High: <?php echo round($weatherData['main']['temp_max']); ?>°C</p>
                                <p class="mb-1">Low: <?php echo round($weatherData['main']['temp_min']); ?>°C</p>
                                <p class="mb-0">Visibility: <?php echo ($weatherData['visibility'] / 1000); ?> km</p>
                            </div>
                            <div class="col-md-6">
                                <h4><i class="fas fa-clock me-2"></i> Day Info</h4>
                                <p class="mb-1">Sunrise: <?php echo date('H:i', $weatherData['sys']['sunrise']); ?></p>
                                <p class="mb-0">Sunset: <?php echo date('H:i', $weatherData['sys']['sunset']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <h4 class="mt-4 mb-3">5-Day Forecast</h4>
                    <div class="row">
                        <?php foreach ($forecast as $day): ?>
                        <div class="col forecast-day">
                            <h5><?php echo $day['day']; ?></h5>
                            <div class="forecast-icon">
                                <i class="fas fa-<?php echo $day['icon']; ?>"></i>
                            </div>
                            <p class="mb-0 high-temp"><?php echo $day['high']; ?>°C</p>
                            <p class="mb-0 low-temp"><?php echo $day['low']; ?>°C</p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                <?php else: ?>
                    <div class="text-center py-4">
                        <div class="weather-icon">
                            <i class="fas fa-search-location"></i>
                        </div>
                        <p>Weather data could not be loaded. Please check your API key.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="card-footer text-center">
                <p class="mb-0">Powered by OpenWeatherMap • Updated: <?php echo date('Y-m-d H:i'); ?></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>