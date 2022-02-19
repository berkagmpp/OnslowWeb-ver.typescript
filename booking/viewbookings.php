<?php
include "../header.php";
include "../menu.php";
loginStatus(); //show the current login status

echo '<div id="site_content">';
include "../sidebar.php";

echo '<div id="content">';

include "../config.php"; //load in any variables
$DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

//insert DB code from here onwards
//check if the connection was good
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit; //stop processing the page further
}

//do some simple validation to check if id exists
$id = $_GET['id'];
if (empty($id) or !is_numeric($id)) {
    echo "<h2>Invalid Booking ID</h2>"; //simple error feedback
    exit;
}

//prepare a query
$query = "SELECT * FROM ( SELECT b.bookingID, b.checkindate, b.checkoutdate, b.contactnumber, b.bookingextras, b.breakfast, b.roomreview,
                                    r.roomID, r.roomname,
                                    c.customerID, c.firstname, c.lastname
                            FROM booking b
                            INNER JOIN room r ON b.roomID = r.roomID 
                            INNER JOIN customer c ON b.customerID = c.customerID ) t 
            WHERE bookingID=" . $id;

$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);
?>

<h1>Booking Details View</h1>
<h2><a href='listbookings.php'>[Return to the Booking listing]</a>
    <a href='/onslow/'>[Return to the main page]</a>
</h2>

<?php
//makes sure we have the booking
if ($rowcount > 0) {
    echo "<fieldset><legend>Booking detail #$id</legend><dl><br>";
    $row = mysqli_fetch_assoc($result);
    echo "<dt>Room name:</dt><dd>" . $row['roomname'] . "</dd><br>";
    echo "<dt>Check-in date:</dt><dd>" . $row['checkindate'] . "</dd><br>";
    echo "<dt>Check-out date:</dt><dd>" . $row['checkoutdate'] . "</dd><br>";
    echo "<dt>Customer name:</dt><dd>" . $row['firstname'] . " " . $row['lastname'] . "</dd><br>";
    echo "<dt>Contact number:</dt><dd>" . $row['contactnumber'] . "</dd><br>";
    echo "<dt>Booking extras:</dt><dd>" . $row['bookingextras'] . "</dd><br>";
    echo "<dt>Breakfast:</dt><dd>" . $row['breakfast'] . "</dd><br>";
    echo "<dt>Room review:</dt><dd>" . $row['roomreview'] . "</dd><br>";
    echo '</dl></fieldset>' . PHP_EOL;
} else {
    echo "<h2>No booking found!</h2>"; //suitable feedback
}

mysqli_free_result($result); //free any memory used by the query
mysqli_close($DBC); //close the connection once done

require_once "../footer.php";
?>