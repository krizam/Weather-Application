<!-- Krisam Byanju  -->
<!-- 230210 -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Weather app</title>
  <link rel="stylesheet" href="main.css" />
</head>
<body>
  <div class="app-wrap">
    
  

    <header>

      <input type="text" autocomplete="off" class="search-box" placeholder="Enter Your City..." />
      <button type="button" class="search-button">Search</button> 
      <form id="forecast-form">
        <input type="hidden" name="get_forecast" value="true">
        <button type="submit" class="search-button">Click Here For 7 Days Forcast</button>
      </form>
    </header>
    <main>
      <section class="location">
        <div class="city"></div>
        <div class="date"></div>
      </section>
      <div class="current">
        <div class="icon"><img src="WeatherIcon.png" alt="cloud image"></div>
        <div class="temp"><span></span></div>
        <div class="weather"></div>
        <div class="wind-humidity">
          <div class="wind"><img src="wind.png" alt="wind Icon" class="icon"> Wind: <span></span></div>
          <div class="humidity"><img src="humidity.png" alt="humidity Icon" class="icon">
            Humidity: <span></span></div>
        </div>
        <div class="hi-low"></div>
      </div>
      <div id="forecast-data" class="forecast"></div>
      
      <div class="error">City Not Found</div>
    </main>
  </div>
  <script src="main.js"></script>
</body>
</html>
<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "weatherapp";

// Create a connection to the database
$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$city = "Colchester"; // Set the city name to Colchester
$date = date('Y-m-d'); // Get current date

// Fetch weather data and store it while maintaining a maximum of 7 rows
fetchAndStoreWeather($city, $date, $conn);

mysqli_close($conn);

// Function to fetch weather data and insert into the database
function fetchAndStoreWeather($city, $date, $conn) {
    $apiKey = "d5c15ab887bf994a5d917e1a9889331b";
    $url = "https://api.openweathermap.org/data/2.5/weather?q=$city&units=metric&appid=$apiKey";

    $response = file_get_contents($url);
    $weatherData = json_decode($response, true);

    if ($weatherData) {
        $city = mysqli_real_escape_string($conn, $city);
        $date = mysqli_real_escape_string($conn, $date);
        $temperature = $weatherData['main']['temp'];
        $weather_description = $weatherData['weather'][0]['main'];
        $wind_speed = $weatherData['wind']['speed'];
        $humidity = $weatherData['main']['humidity'];

        // Calculate the day of the week
        $dayOfWeek = date('l', strtotime($date));

        // Delete older records for the specific city if the maximum row count is reached
        $rowCountQuery = "SELECT COUNT(*) AS row_count FROM weather_data WHERE city = '$city'";
        $rowCountResult = mysqli_query($conn, $rowCountQuery);
        $rowCountData = mysqli_fetch_assoc($rowCountResult);

        if ($rowCountData['row_count'] >= 7) {
            $deleteOldestQuery = "DELETE FROM weather_data WHERE city = '$city' ORDER BY date ASC LIMIT 1";
            mysqli_query($conn, $deleteOldestQuery);
        }

        // Insert weather data for the specific city into the database
        $insertQuery = "INSERT INTO weather_data (city, date, day_of_week, temperature, weather_description, wind_speed, humidity)
                        VALUES ('$city', '$date', '$dayOfWeek', '$temperature', '$weather_description', '$wind_speed', '$humidity')";

        if (mysqli_query($conn, $insertQuery)) {
            // echo "Weather data for $city on $date stored successfully!<br>";
            
        } else {
            echo "Error storing weather data: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "Error fetching weather data from API.<br>";
    }
}
// ..
if (isset($_GET['get_forecast'])) {
  $city = "Colchester";
  fetchAndDisplayWeatherForecast($city, $conn);
}
// function fetchAndDisplayWeatherForecast($city, $conn) {
//     $query = "SELECT * FROM weather_data WHERE city = '$city' ORDER BY date DESC LIMIT 7";
//     $result = mysqli_query($conn, $query);

//     if ($result) {
//         $forecastData = mysqli_fetch_all($result, MYSQLI_ASSOC);
//         echo json_encode($forecastData); // Output JSON data for JavaScript processing
//     } else {
//         echo "Error fetching weather forecast data: " . mysqli_error($conn);
//     }
// }
// if (isset($_GET['get_forecast'])) {
//     fetchAndDisplayWeatherForecast($city, $conn);
// }

// ..
function fetchAndDisplayWeatherForecast($city, $conn) {
  $query = "SELECT * FROM weather_data WHERE city = '$city' ORDER BY date DESC LIMIT 7";
  $result = mysqli_query($conn, $query);

  if ($result) {
      $forecastData = mysqli_fetch_all($result, MYSQLI_ASSOC);
      echo json_encode($forecastData); // Output JSON data for JavaScript processing
  } else {
      echo "Error fetching weather forecast data: " . mysqli_error($conn);
  }
}

?>