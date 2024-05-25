// Function to fetch weather data from an API
async function fetchWeather(city) {
  const apiKey = 'YOUR_API_KEY'; // Replace 'YOUR_API_KEY' with your actual API key
  const apiUrl = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}`;

  try {
    const response = await fetch(apiUrl);
    const data = await response.json();
    return data;
  } catch (error) {
    console.log('Error fetching weather data:', error);
    return null;
  }
}

// Function to display weather data in the console
function displayWeather(weatherData) {
  if (!weatherData) {
    console.log('No weather data available');
    return;
  }

  console.log('Weather Data:');
  console.log('-------------');
  console.log('City:', weatherData.name);
  console.log('Temperature:', weatherData.main.temp, '°K');
  console.log('Description:', weatherData.weather[0].description);
}

// Usage
const city = 'New York'; // Replace 'New York' with the city you want to get weather data for
fetchWeather(city)
  .then(weatherData => displayWeather(weatherData))
  .catch(error => console.log('Error:', error));
