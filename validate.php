<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Form</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="styles.css">
	</head>
	<body>
		<div class="header">
			<h2>Form</h2>
		</div>
		<div class="main">
<?php
function checkName($nameStr) { /* Checks for first and last name. */
	$pattern = "/^[A-Za-z]+ [A-Za-z]+$/";
	if(preg_match($pattern, $nameStr)) return true;
	return false;
}
function checkAddress($addressStr) { /* Checks for valid address format. */
	$pattern = "/^[0-9]+ [A-Za-z]. [A-Za-z0-9]+ [A-Za-z]+., [A-Za-z]+ ?([A-Za-z]+), [A-Za-z]+, [0-9]+$/";
	if(preg_match($pattern,$addressStr)) return true;
	return false;
}
function checkAge($ageInt) { /* Checks if the age entered is greater than or equal to 18. */
	if (is_numeric($ageInt) && $ageInt >= 18) return true;
	return false;
}
function checkField($fieldStr) { /* Checks if the field entered is one of the valid fields. */
	$validFields = array("computer science", "mathematics", "computer engineering", "data science");
	if (in_array($fieldStr, $validFields)) return true;
	return false;
}
function checkPassword($pwdStr) { /* Checks if the password entered is at least 8 characters long. */
	if (strlen($pwdStr) >= 8) return true;
	return false;
}
function createUsername($first, $last){ /* Generates your Username. */
	return substr($first,0,1) . substr($last,0,6);
}
function saltPassword($pwd){ /* Generates your new password by adding onto the one entered. */
	return $pwd . rand(0,9). rand(0,9). rand(0,9). rand(0,9);
}
/* Defining the variables. */
$name = $_POST['name'];
$address = $_POST['address'];
$age = $_POST['age'];
$field = $_POST['field'];
$pwd = $_POST['password'];
$type = "";
if(isset($_POST['task'])){
	$type = $_POST['task'];
}
// Variables for connecting to SQL.
$servername = "localhost";
$username = "cs247_joseph";
$password = "211073828";
$dbname = "cs247_joseph";
// Setup connection with SQL.
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	die("Connection was not made! " . $conn->connect_error);
}
/* Displays how to fix the problem depending on what field had a issue. */
if ($type == "a") { 
	if (!checkName($name)) {
		$problem = "You must enter your first and last name.";
	}
	if (!checkAddress($address)) {
		$problem = "Your address must be in this format: 123 S. Main St., Beverly Hills, CA, 90210";
	}
	if (!checkAge($age)) {
		$problem = "Your age must be 18 or older.";
	}
	if (!checkField($field)) {
		$problem = "You must be in one of the following fields: computer science, mathematics, computer engineering, or data science.";
	}
	if (!checkPassword($pwd)) {
		$problem = "Your password must be at least 8 characters in length.";
	}
	if (checkName($name) and checkAddress($address) and checkAge($age) and checkField($field) and checkPassword($pwd)) {
		$parts = explode(' ', trim($name));
		$first = $parts[0];
		$last = $parts[1];
		$userid = createUsername($first,$last);
		$npwdRandom = saltPassword($pwd);
		$npwd = sha1($pwd,FALSE); // HASH so password is not in plain text(for security).
		echo "<h1>Welcome $name</h1>"; /* Welcomes user if form is completed correctly. */
		echo "Your username is $userid and your password is $npwdRandom"; /* Gives you your new password. */
		// ADD INFORMATION TO THE DATABASE
		$sql = "INSERT INTO `Users` (`first`, `last`, `address`, `age`, `field`, `password`) VALUES ('".$first."','".$last."','".$address."',".$age.",'".$field."','".$npwd."')";
		if($conn->query($sql) === TRUE) {
			echo "<h3>Data added to database!<h3>"; // Information added to SQL database.
		} else {
			echo "Problem detected, data was not added."; // Error when data was not added.
		}
	} else {
		echo "<h1>Sorry, invalid form entry.</h1>"; /* Notifies the user that the form was not completed correctly. */
		echo "Problem: $problem"; /* Tells the user how to fix their mistake. */
	}
} else {
	if(checkName($name) && checkPassword($pwd)){
		$parts = explode(' ', trim($name));
		$first = $parts[0];
		$last = $parts[1];
		$npwd = sha1($pwd,FALSE); // HASH so password is not in plain text(for security).
		// RETRIEVE INFORMATION FROM DATABASE
		$sql = "SELECT * FROM `Users` WHERE `first` = '".$first."' AND `last` = '".$last."' AND password = '".$npwd."' LIMIT 1";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) { // Prints out the data from the database.
			while ($row = $result->fetch_assoc()) {
				echo $row["first"]. $row["last"];
				echo "</br>";
				echo "</br>";
				echo $row["address"];
				echo "</br>";
				echo "</br>";
				echo $row["age"];
				echo "</br>";
				echo "</br>";
				echo $row["field"];
				echo "</br>";
				echo "</br>";
				echo $row["password"];
			}
		} else {
			echo "<h3>No data found!<h3>"; // Error for nothing found under those credentials. 
		}
	}else{
		echo "<h3>Fill out the form correctly!</h3>"; // Form was not filled out properly.
	}
}
$conn->close(); // Close SQL connection.
?>
		</div>
	</body>
</html>
