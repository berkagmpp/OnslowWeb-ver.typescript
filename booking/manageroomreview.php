<?php
include "../header.php";
include "../menu.php";
loginStatus(); //show the current login status

echo '<div id="site_content">';
include "../sidebar.php";

echo '<div id="content">';

include "../config.php"; //load in any variables
$DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit; //stop processing the page further
};

//function to clean input but not validate type and content
function cleanInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

//retrieve the bookingID from the URL
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid booking ID</h2>"; //simple error feedback
        exit;
    }
}
//the data was sent using a formtherefore we use the $_POST instead of $_GET
//check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {
    $error = 0; //clear our error flag
    $msg = 'Error: ';

    //bookingID (sent via a form ti is a string not a number so we try a type conversion!)    
    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
        $id = cleanInput($_POST['id']);
    } else {
        $error++; //bump the error flag
        $msg .= 'Invalid booking ID.'; //error feedback
        $id = 0;
    }

    //validate roomriview
    $roomr = $_POST['roomreview'];
    $roomreview = (strlen($roomr) > 1000) ? substr($roomr, 0, 1000) : $roomr; // check length and clip if too big
    $roomreview = cleanInput($roomreview);
    if ($error == 0 and $id > 0) {
        $query = "UPDATE booking SET roomreview=? WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC, $query); //prepare the query
        mysqli_stmt_bind_param($stmt, 'si', $roomreview, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo "<h2>Room review updated.</h2>";
    } else {
        echo "<h2>Error or invalid!</h2>" . PHP_EOL;
    }
}

//locate the booking to edit by using the bookingID
$query = "SELECT bookingID, roomreview
                FROM booking
                WHERE bookingID=" . $id;
$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);

if ($rowcount > 0) {
$row = mysqli_fetch_assoc($result);
?>

    <h1>Edit &#47; add room review</h1>

    <h2><a href="listbookings.php">[Return to the booking listing]</a>
        <a href="/onslow-ts/">[Return to the main page]</a>
    </h2>

    <form id="review" method="POST" action="manageroomreview.php">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <p>
            <label for="roomreview">Room review: </label>
            <!-- bring review from $query -->
            <textarea id="roomreview" name="roomreview" rows="5" cols="60"><?php echo $row['roomreview']; ?></textarea>
        </p>
        <input type="submit" name="submit" value="Update">
        <a href='listbookings.php'>[Cancel]</a>
        <br>
        <br>
        <div id="confirm"></div>
    </form>

<?php
} else {
    echo "<h2>room not found with that ID</h2>"; //error feedback
}

mysqli_free_result($result); //free any memory used by the query
mysqli_close($DBC); //close the connection once done

require_once "../footer.php";
?>