<?php
/**
 * CSE Activity 2.2.2 IntroducingPHP
 * 
 * 222indexA.php models use of PHP in conjunction with MySQL
 * @copyright Unpublished work 2013 Project Lead The Way
 * @version 2014.7.30
 */

/* 
This block allows our program to access the MySQL database.
Elaborated on in 2.2.3.
 */
require_once 'teacherdb.php';
$db_server = mysql_connect($host, $username, $password);
if (!$db_server) die("Unable to connect to MySQL: " . mysql_error());
mysql_select_db($dbname)
	or die("Unable to select database: " . mysql_error());

// $_COOKIE is a data structure that holds all cookies for this site.
// This conditional verifies that the cookie 'username' contains data.
// That would symbolize that the user is logged in as an artist.
if (isset($_COOKIE['username']))
{
	$username = $_COOKIE['username']; // Retrieves the value of the cookie
	
	// Dynamically respond to the data in the cookie to recognize the user
	// An echo statement is used to display something in PHP. Plain text
	// shows up as such, and html code enclosed in quotes functions as normal html.
	echo "Welcome back, " . $username . 
		", click <a href='artist_portal.php'>here to go to the Artist's page</a>.<br />";
	echo "Click <a href='logout.php'>here to Log Out</a>...<br />";
	echo "<br /><br />Enter information in the fields at the bottom of the page to search the image database.<br /><br />";
}
else
{
	echo "Click <a href='artist_portal.php'>here to log in as an artist</a>.<br />";
	echo "Or click <a href='account_creation.php'>here to create an artist account</a>.<br />";
}
$query = "SELECT * FROM uploaders WHERE firstname='" . $_POST['firstname'] . "' OR lastname='" . $_POST['lastname'] . "'";
$result_artist = mysql_query($query);
$row = mysql_fetch_row($result_artist);
$artistID = $row[0];
$query = "SELECT * FROM comics WHERE username='" . $artistID . "'";
$image_info_table = mysql_query($query);
// Call a function defined later in this file, with two arguments
display_table($artistID, $image_info_table, $dbname);

	
// HTML to display the form on this page.
echo '<br />Search the comic database using the fields below.';
// Sets POST as method of data submission
echo '<form action="index.php" method="post"><pre>'; 
echo 'First Name <input type="text" name="firstname" />';
echo '<br />Last Name <input type="text" name="lastname" />';
// Creates the SEARCH button which calls the POST method with the data entered
echo '<br /><input type="submit" value="SEARCH" />'; 
echo '</pre></form>';

/** 
 * Generates HTML to render table of images returned by user query
 * 
 * An example of code reuse. This code is needed for each of our form submission cases.
 * @param string $artistID the name of the artist's folder
 * @param array $image_info_table a 2D array containing the data about each relevant image.
 * @return null
 */
function display_table($artistID, $image_info_table, $dbname)
{
	echo "<TABLE><CAPTION>Your Results:</CAPTION>";
	$closed_tr = 0; // flag, used to determine if we are at the end of a row when the loop terminates

	if ($image_info_table)
	{
		// Iterate through all of the returned images, placing them in a table for easy viewing
		for ($count = 0; $count < mysql_num_rows($image_info_table); $count++)
		{
			// The following few lines store information from specific cells in the data about an image
			$image_row = mysql_fetch_row($image_info_table); // Advances a row each time it is called
			$thumb_name = $image_row[2];
			
			// Remember the mod operator, this one gives us the remainder when $count is divided by 6
			if ($count % 6 == 0)
			{
				echo "<TR>";
				$closed_tr = 0;
			}
			$domain = $_SERVER['SERVER_NAME'];

			echo "<TD><img src='http://$domain/~$dbname/$artistID/$thumb_name' /></TD>";
			if ($count % 6 == 5)
			{
				echo "</TR>";
				$closed_tr = 1;
			}
		}
	}
	if ($closed_tr == 0) echo "</TR>"; // Appends a close tag for the TR element if the loop did not terminate at a row end.
	echo "</TABLE>";
}
?>