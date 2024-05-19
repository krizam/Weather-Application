// Krisam Byanju
// 230210
const searchButton = document.querySelector('.search-button');
searchButton.addEventListener('click', () => {
  const searchBox = document.querySelector('.search-box');
  const query = searchBox.value;
  getResults(query);

});



document.addEventListener('DOMContentLoaded', async () => {
  await getResults('Colchester');
  // await getForecast('Colchester'); // Fetch 7-day forecast
});
// ..
const forecastForm = document.getElementById('forecast-form');
forecastForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  await getForecast('Colchester');
});

// async function getForecast(city) {
//   try {
//     const response = await fetch(`newindex.php?get_forecast=true&city=${city}`);
//     if (!response.ok) {
//       displayError();
//       return;
//     }
//     const forecastData = await response.json();
//     displayForecast(forecastData);
//   } catch (error) {
//     console.error('Error:', error);
//     displayError();
//   }
// }
async function getForecast(city) {
  try {
    const response = await fetch(`main.php?get_forecast=true&city=${city}`);
    console.log('Response:', response);
    if (!response.ok) {
      displayError();
      return;
    }
    const forecastData = await response.json();
    console.log('Forecast Data:', forecastData);
    displayForecast(forecastData); // Call the displayForecast function
  } catch (error) {
    console.error('Error:', error);
    displayError();
  }
}


// function displayForecast(forecastData) {
//   // Display the 7-day forecast data on the webpage
//   // You can modify this part based on your design and needs
// }
// // ..
// ....
function displayForecast(forecastData) {
  const forecastContainer = document.getElementById('forecast-data');

  // Clear previous forecast data
  forecastContainer.innerHTML = '';

  // Loop through the forecast data and display each day's information
  forecastData.forEach((forecastItem) => {
    const forecastDate = new Date(forecastItem.date);
    const dayOfWeek = forecastDate.toLocaleDateString('en-US', { weekday: 'long' });
    const tempMin = Math.round(forecastItem.temperature_min);
    const tempMax = Math.round(forecastItem.temperature_max);
    const weatherDescription = forecastItem.weather_description;

    const forecastItemDiv = document.createElement('div');
    forecastItemDiv.className = 'forecast-item';

    forecastItemDiv.innerHTML = `
      <p>${dayOfWeek}</p>
      <img src="${getWeatherIcon(weatherDescription)}" alt="${weatherDescription}">
      <p>${tempMin}°C / ${tempMax}°C</p>
    `;

    forecastContainer.appendChild(forecastItemDiv);
  });

  // Hide the current weather information (optional)
  // document.querySelector('.current').style.display = 'none';
}

async function setQuery(evt) {
  if (evt.keyCode == 13) {
    await getResults(searchbox.value);
  }
}
async function getResults(query) {
  try {
    const response = await fetch(`https://api.openweathermap.org/data/2.5/weather?q=${query}&units=metric&appid=d5c15ab887bf994a5d917e1a9889331b`);
    const weather = await response.json();
    displayResults(weather);
  } catch (error) {
    console.error('Error:', error);
  }
}async function getResults(query) {
  try {
    const response = await fetch(`https://api.openweathermap.org/data/2.5/weather?q=${query}&units=metric&appid=d5c15ab887bf994a5d917e1a9889331b`);
    if (!response.ok) {
      displayError();
      return; // Return early if an error occurs
    }
    const weather = await response.json();
    displayResults(weather);
  } catch (error) {
    console.error('Error:', error);
    displayError();
  }
}
function displayError() {
  document.querySelector('.error').style.display = 'block';
  document.querySelector('.location').style.display = 'none'; // Hide location information
  document.querySelector('.current').style.display = 'none';  // Hide current weather information
  }
  function displayResults(weather) {
  let city = document.querySelector('.location .city');
  city.innerText = `${weather.name}, ${weather.sys.country}`;

  let now = new Date();
  let date = document.querySelector('.location .date');
  date.innerText = dateBuilder(now);

  let temp = document.querySelector('.current .temp');
  temp.innerHTML = `${Math.round(weather.main.temp)}<span>°c</span>`;

  let weather_el = document.querySelector('.current .weather');
  weather_el.innerText = weather.weather[0].main;

  let hilow = document.querySelector('.hi-low');
  hilow.innerText = `${Math.round(weather.main.temp_min)}°c / ${Math.round(weather.main.temp_max)}°c`;
  
 
  
  let windSpeed = document.querySelector('.current .wind span');
  windSpeed.innerText = `${weather.wind.speed} m/s`;
  
  
  let humidityValue = document.querySelector('.current .humidity span');
  humidityValue.innerText = `${weather.main.humidity}%`;

  let icon = document.querySelector('.current .icon img');
  let weatherMain = weather.weather[0].main;
  icon.src = getWeatherIcon(weatherMain);

  document.querySelector(".weather").style.display = "block";
  document.querySelector(".error").style.display = "none";
  document.querySelector('.location').style.display = 'block';
  document.querySelector('.current').style.display = 'block';
}  
function getWeatherIcon(weatherMain) {
  switch (weatherMain) {
    case "Clouds":
      return "cloud.png";
    case "Clear":
      return "sunny.png";
    case "Drizzle":
      return "drizzle.png";
    case "Mist":
      return "mist.png";
    case "Rain":
      return "rain.png";
    case "Snow":
      return "snow.png";
    case"Haze":
      return "haze.png"
    default:
      return "sunny.png";
  }
}
function dateBuilder(d) {
  let months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
  let days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
  let day = days[d.getDay()];
  let date = d.getDate();
  let month = months[d.getMonth()];
  let year = d.getFullYear();

  return `${day} ${date} ${month} ${year}`;


}