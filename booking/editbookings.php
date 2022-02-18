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
    exit; // stop processing the page further
};

// function to clean input but not validate type and content
function cleanInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

// retrieve the bookingID from the URL
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid booking ID</h2>"; //simple error feedback
        exit;
    }
}

// the data was sent using a formtherefore we use the $_POST instead of $_GET
// check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {
    $error = 0; //clear our error flag
    $msg = 'Error: ';

    // bookingID (sent via a form ti is a string not a number so we try a type conversion!)    
    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
        $id = cleanInput($_POST['id']);
    } else {
        $error++; // bump the error flag
        $msg .= 'Invalid booking ID.'; // error feedback
        $id = 0;
    }

    // validate all incoming data
    $variables = array('roombrowser', 'checkindate', 'checkoutdate', 'contactnumber', 'bookingextras', 'breakfastbrowser', 'roomreview'); // make array for validation all incoming data
    foreach ($variables as $key) { // vallidate $variables array
        if (isset($_POST[$key]) and !empty($_POST[$key])) {

            // roomID server validate
            if (is_integer((intval($_POST['roombrowser'])))) {
                $roomID = cleanInput($_POST['roombrowser']);
            } else {
                $error++; // bump the error flag
                $msg .= 'Variable: roomID is NOT a integer.'; // error message
            }

            // checkindate server validate
            $cidate = $_POST['checkindate'];
            if (is_string($cidate) && (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $cidate))) {
                $checkindate = cleanInput($cidate);
            } else {
                $error++; // bump the error flag
                $msg .= 'Invalid checkindate.'; // error message
            }

            // checkoutdate server validate
            $codate = $_POST['checkoutdate'];
            if (is_string($codate) && (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $codate))) {
                $checkoutdate = cleanInput($codate);
            } else {
                $error++; // bump the error flag
                $msg .= 'Invalid checkoutdate.'; // error message
            }

            // contactnumber server validate
            $cnummber = $_POST['contactnumber'];
            if (is_string($cnummber) && strlen($cnummber) == 14 && (preg_match('/^\(([0-9]{3})\)[ ]([0-9]{3})[-]([0-9]{4})$/', $cnummber))) {
                $contactnumber = cleanInput($cnummber);
            } else {
                $error++; // bump the error flag
                $msg .= 'Invalid contactnumber.'; // error message
            }

            // bookingextras server validate
            $extrs = $_POST['bookingextras'];
            if (is_string($extrs)) {
                $bext = (strlen($extrs) > 1000) ? substr($extrs, 0, 1000) : $extrs; // check length and clip if too big
                $bookingextras = cleanInput($bext);
            } else {
                $error++; // bump the error flag
                $msg .= 'Invalid bookingextras.'; // error message
            }

            // breakfast server validate
            $bfast = $_POST['breakfastbrowser'];
            if (is_string($bfast)) {
                $breakf = (strlen($bfast) > 25) ? substr($bfast, 0, 25) : $bfast;
                $breakfast = cleanInput($breakf);
            } else {
                $error++; // bump the error flag
                $msg .= 'Invalid breakfast.'; // error message
            }

            // roomreview server validate
            $rreview = $_POST['roomreview'];
            if (is_string($rreview)) {
                $roomr = (strlen($rreview) > 1000) ? substr($rreview, 0, 1000) : $rreview; // check length and clip if too big
                $roomreview = cleanInput($roomr);
            } else {
                $error++; // bump the error flag
                $msg .= 'Invalid roomreview.'; // error message
            }
        }
    }

    if ($error == 0 and $id > 0) {
        $query = "UPDATE booking SET roomID=?,checkindate=?,checkoutdate=?,contactnumber=?,bookingextras=?,breakfast=?,roomreview=? WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC, $query); // prepare the query
        mysqli_stmt_bind_param($stmt, 'issssssi', $roomID, $checkindate, $checkoutdate, $contactnumber, $bookingextras, $breakfast, $roomreview, $id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<h2>Booking details updated.</h2>";
            echo "------------------------------------------------------------------------";
        } else {
            echo "<h2>Something went wrong.</h2>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<h2>$msg</h2>" . PHP_EOL;
    }
}

// locate the booking to edit by using the bookingID
$query = "SELECT b.bookingID, b.checkindate, b.checkoutdate, b.contactnumber, b.bookingextras, b.breakfast, b.roomreview,
                    r.roomID, r.roomname, r.roomtype, r.beds,
                    c.customerID
                FROM booking b
                INNER JOIN room r ON b.roomID = r.roomID 
                INNER JOIN customer c ON b.customerID = c.customerID
                WHERE b.bookingID=" . $id;
$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);

// locate the room for roombrowsing select options
$roombrowsing = "SELECT roomID, roomname, roomtype, beds 
                        FROM room 
                        ORDER BY roomID";
$roomresult = mysqli_query($DBC, $roombrowsing);
$roomcount = mysqli_num_rows($roomresult);
?>

<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
<script>
    let dateToday = new Date();

    // Edit a booking - jQuery UI datapicker (Date Range) //
    $(function() {
        let checkdates = $("#checkindate, #checkoutdate").datepicker({
            dateFormat: 'yy-mm-dd', // set format yy-mm-dd
            defaultDate: 0, // set default date: today
            changeMonth: true,
            numberOfMonths: 2,
            minDate: dateToday, // set user cannot select the date before today
            onSelect: function(selectedDate) { // set user cannot select checkoutdate before checkindate
                let option = this.id == "checkindate" ? "minDate" : "maxDate",
                    instance = $(this).data("datepicker"),
                    date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
                checkdates.not(this).datepicker("option", option, date);
            }
        })
    })

    // dates front-end validation: disable enter manually
    const autodatePattern = (target) => {
        target.value = target.value
            .replace(/[0-9a-zA-Z\s.,'!@#$"%^&*)(}{[\]=-]+$/, '') // user cannot enter manually
    }

    // contactnumber front-end validation: enter condition & auto pattern
    const handleOnInput = (target, length) => {
        target.value = target.value.replace(/[^0-9]/g, ''); // user can enter only numbers
        if (target.value.length == 10) {
            target.value = target.value.replace(/^(\d{3})(\d{3})(\d{4})$/, `($1) $2-$3`); // auto (###) ###-#### pattern change
        } else {
            return false;
        }
    }
</script>

<h1>Edit a booking (update)</h1>
<h2><a href="listbookings.php">[Return to the booking listing]</a>
    <a href="/bnb/">[Return to the main page]</a>
    <?php
    if ($rowcount > 0) {
        $row = mysqli_fetch_assoc($result);
        //check if we have permission to modify data
        if (isAdmin()) {
    ?>
            <form method="POST" action="editbookings.php">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <p>
                    <label for="roombrowser">Room (name, type, beds): </label>
                    <select id="roombrowser" name="roombrowser" required>
                        <!-- set select options from $roombrowsing and selected value from $query -->
                        <?php
                        if ($roomcount > 0) {
                            $selected_room = "";
                            while ($rooms = mysqli_fetch_assoc($roomresult)) {
                                $selected_room = "selected";
                                if ($row['roomID'] == $rooms['roomID']) {
                                    echo "<option value= '" . $rooms['roomID'] . "' selected= '" . $selected_room . "'>
                                            " . $rooms['roomname'] . ", " . $rooms['roomtype'] . ", " . $rooms['beds'] . "</option>";
                                } else {
                                    echo "<option value= '" . $rooms['roomID'] . "'>
                                            " . $rooms['roomname'] . ", " . $rooms['roomtype'] . ", " . $rooms['beds'] . "</option>";
                                }
                            }
                        } else {
                            echo "<h2>No room available</h2>"; // error feedback
                        }
                        ?>
                    </select>
                </p>
                <p>
                    <!-- Check-In date datapicker -->
                    <label for="checkindate">Check-in date: </label>
                    <input type="text" class="f_input" id="checkindate" name="checkindate" required placeholder="yyyy-mm-dd" oninput="autodatePattern(this)" value="<?php echo $row['checkindate']; ?>">
                    <!--other validation is in <script> -->
                </p>
                <p>
                    <!-- Check-Out date datapicker -->
                    <label for="checkoutdate">Check-out date: </label>
                    <input type="text" class="f_input" id="checkoutdate" name="checkoutdate" required placeholder="yyyy-mm-dd" oninput="autodatePattern(this)" value="<?php echo $row['checkoutdate']; ?>">
                    <!--other validation is in <script> -->
                </p>
                <p>
                    <label for="contactnumber">Contact number: </label>
                    <input type="tel" class="f_input" id="contactnumber" name="contactnumber" required placeholder="Only Enter 10 Numbers" oninput="handleOnInput(this,10)" pattern="(\(\d{3}\)) \d{3}-\d{4}" maxlength="10" value="<?php echo $row['contactnumber']; ?>">
                    <!--this is for validation, more condition and auto pattern are in <script> -->
                </p>
                <p>
                    <label for="bookingextras">Booking extras: </label>
                    <textarea id="bookingextras" class="f_input" name="bookingextras" rows="5" cols="60"><?php echo $row['bookingextras']; ?></textarea>
                </p>
                <p>
                    <label for="breakfastbrowser">Breakfast: </label>
                    <select id="breakfastbrowser" name="breakfastbrowser">
                        <!-- set select options and selected value from $query -->
                        <?php
                        $selected_breakfast = "selected";
                        $breakfasts = array("cooked", "continental", "none");
                        foreach ($breakfasts as $breakfast) {
                            $selected_breakfast = "selected";
                            if ($row['breakfast'] == $breakfast) {
                                echo "<option value='" . $breakfast . "' selected= '" . $selected_breakfast . "'>" . $breakfast . " breakfast</option>";
                            } else {
                                echo "<option value='" . $breakfast . "'>" . $breakfast . " breakfast</option>";
                            }
                        }
                        ?>
                    </select>
                </p>
                <p>
                    <label for="roomreview">Room review: </label>
                    <textarea id="roomreview" class="f_input" name="roomreview" rows="5" cols="60"> <?php echo $row['roomreview']; ?></textarea>
                </p>
                <input type="submit" name="submit" value="Update">
            </form>
    <?php
        } else {
            echo "<h2>Signin as an admin is required to edit</h2>"; // error feedback
        }
    } else {
        echo "<h2>Room not found with that ID</h2>"; // error feedback
    }

    mysqli_free_result($result); //free any memory used by the query
    mysqli_close($DBC); // close the connection once done

    require_once "../footer.php";
    ?>