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

//function to clean input but not validate type and content
function cleanInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

//retrieve the Roomid from the URL
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid Booking ID</h2>"; //simple error feedback
        exit;
    }
}

//the data was sent using a formtherefore we use the $_POST instead of $_GET
//check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Delete')) {
    $error = 0; //clear our error flag
    $msg = 'Error: ';
    //BookingID (sent via a form it is a string not a number so we try a type conversion!)    
    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
        $id = cleanInput($_POST['id']);
    } else {
        $error++; //bump the error flag
        $msg .= 'Invalid Booking ID '; //append error message
        $id = 0;
    }

    //save the Room data if the error flag is still clear and Room id is > 0
    if ($error == 0 and $id > 0) {
        $query = "DELETE FROM booking WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC, $query); //prepare the query
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo "<h2>Booking details deleted.</h2>";
    } else {
        echo "<h2>$msg</h2><br>";
    }
}

//prepare a query and send it to the server
$query = "SELECT * FROM ( SELECT b.bookingID, b.checkindate, b.checkoutdate, 
                                    r.roomID, r.roomname,
                                    c.customerID
                            FROM booking b
                            INNER JOIN room r ON b.roomID = r.roomID 
                            INNER JOIN customer c ON b.customerID = c.customerID ) t 
            WHERE bookingID=" . $id;
$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);
?>

<h1>Booking details preview before deletion</h1>

<h2><a href='listbookings.php'>[Return to the booking listing]</a>
    <a href='/onslow-ts/'>[Return to the main page]</a>
</h2>

<?php
//makes sure we have the booking
if ($rowcount > 0) {
    //check if we have permission to modify data
    if (isAdmin()) {
        echo "<fieldset><legend>Booking detail #$id</legend><dl><br>";
        $row = mysqli_fetch_assoc($result);
        echo "<dt>Room name:</dt><dd>" . $row['roomname'] . "</dd><br>";
        echo "<dt>Check-in date:</dt><dd>" . $row['checkindate'] . "</dd><br>";
        echo "<dt>Check-out date:</dt><dd>" . $row['checkoutdate'] . "</dd><br>";
        echo '</dl></fieldset>' . PHP_EOL;
?>
        <form method="POST" action="deletebookings.php">
            <h2>Are you sure you want to delete this Booking?</h2>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="submit" name="submit" value="Delete">
            <a href="listbookings.php">[Cancel]</a>
        </form>
<?php
    } else {
        echo "<h2>Signin as an admin is required to delete</h2>"; // error feedback
    }
} else {
    echo "<h2>No Booking found, possbily deleted!</h2>"; //suitable feedback
}

mysqli_free_result($result); //free any memory used by the query
mysqli_close($DBC); //close the connection once done

require_once "../footer.php";
?>