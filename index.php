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
            echo "Weather data for $city on $date stored successfully!<br>";
            
        } else {
            echo "Error storing weather data: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "Error fetching weather data from API.<br>";
    }
}
?>

