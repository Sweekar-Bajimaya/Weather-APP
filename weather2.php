<!-- Name: Sweekar Bajimaya
Stud ID: 2329474 -->
<html>
<head>
</head>
<body>

<form method="get" action="">
  <input type="text" name="city" placeholder="Enter city name">
  <input type="submit" name="submit" value="Search">
</form>
<?php

if (isset($_GET['submit'])) {
  $city = $_GET['city'];
} else {
  $city = "aberdeen";
}

$url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid=db124e6ee942e3a3706701106ccf3f83&units=metric";

// Make API request and parse JSON response
$response = file_get_contents($url);
$data = json_decode($response, true);

if (!$data) {
  // Handle API error
  die("Error: Failed to retrieve data from OpenWeatherMap API.");
}

// Extract relevant weather data
$city_name = $data['name'];
$condition = $data['weather'][0]['main'];
$icon = $data['weather'][0]['icon'];
$temperature = $data['main']['temp'];
$pressure = $data['main']['pressure'];
$humidity = $data['main']['humidity'];
$wind_speed = $data['wind']['speed'];
$rainfall = isset($data['rain']['1h']) ? $data['rain']['1h'] : 'not given';


// Insert or update weather data in database
$host = 'sql305.epizy.com';
$username = 'epiz_34167322';
$password = 'MgGRTqvq6mM';
$dbname = "epiz_34167322_firsttry";

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Check if data for the current hour is already present in database
$sql = "SELECT * FROM datw WHERE `city`='$city_name' AND DATE(`date`) = CURDATE()";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // Update existing row with latest weather data
  $sql = "UPDATE datw SET `condition`='$condition', `icon`='$icon', `temperature`='$temperature', `rainfall`=0, `humidity`='$humidity', `wind_speed`='$wind_speed' WHERE `city`='$city_name' AND `date`= DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')";
} else {
  // Insert new row with current weather data
 
  $sql = "INSERT INTO datw (`city`, `date`, `condition`, `icon`, `temperature`, `rainfall`, `wind_speed`, `humidity`)
        VALUES ('$city_name', NOW(), '$condition', '$icon', '$temperature', '0', '$wind_speed', '$humidity')";
}


mysqli_query($conn, $sql);

// Retrieve latest weather data from database
$sql = "SELECT * FROM datw WHERE `city`='$city_name' ORDER BY `date` DESC LIMIT 7";
$result = mysqli_query($conn, $sql);

echo "<table border='1'>";
echo "<tr>";
echo "<th>Date/Time</th>";
echo "<th>Condition</th>";
echo "<th>Icon</th>";
echo "<th>Temperature</th>";
echo "<th>Humidity</th>";
echo "<th>Wind Speed</th>";
echo "</tr>";
while ($row = mysqli_fetch_assoc($result)) {
  $date = date('Y-m-d H:i:s', strtotime($row['date']));
  $condition = $row['condition'];
  $icon = $row['icon'];
  $temperature = $row['temperature'];
  $humidity = $row['humidity'];
  $wind_speed = $row['wind_speed'];

  echo "<tr>";
  echo "<td>{$date}</td>";
  echo "<td>{$condition}</td>";
  echo "<td><img src='http://openweathermap.org/img/w/{$icon}.png'></td>";
  echo "<td>{$temperature}°C</td>";
  echo "<td>{$humidity}%</td>";
  echo "<td>{$wind_speed} m/s</td>";
  echo "</tr>";
}
echo "</table>";


// Close database connection
mysqli_close($conn);
?>
</body>
</html>