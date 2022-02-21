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

// locate rooms for roombrowsing select options
$roombrowsing = "SELECT roomID, roomname, roomtype, beds 
                     FROM room 
                     ORDER BY roomID";
$roomresult = mysqli_query($DBC, $roombrowsing);
$roomcount = mysqli_num_rows($roomresult);

// the data was sent using a formtherefore we use the $_POST instead of $_GET
// check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Add')) {
  $error = 0; // clear our error flag
  $msg = 'Error: ';

  // needed a signingin with an admin id
  if (isAdmin()) {
    $customerID = 1;
  } else {
    $error++; //bump the error flag
    $msg .= "<h2>signin as an admin is required for booking at this stage.</h2>"; // error message
  }

  // validate all incoming data
  $variables = array('roombrowser', 'checkindate', 'checkoutdate', 'contactnumber', 'bookingextras', 'breakfastbrowser'); // make array for validation all incoming data
  foreach ($variables as $key) { // vallidate using $variables array
    if (isset($_POST[$key]) and !empty($_POST[$key])) {

      // roomID server validate
      if (is_integer((intval($_POST['roombrowser'])))) {
        $roomID = cleanInput($_POST['roombrowser']);
      } else {
        $error++; //bump the error flag
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
        $bookingextras = (strlen($extrs) > 1000) ? cleanInput(substr($extrs, 0, 1000)) : cleanInput($extrs); // check length and clip if too big
      } else {
        $error++; // bump the error flag
        $msg .= 'Invalid bookingextras.'; // error message
      }

      // breakfast server validate
      $bfast = $_POST['breakfastbrowser'];
      if (is_string($bfast)) {
        $breakfast = (strlen($bfast) > 25) ? cleanInput(substr($bfast, 0, 25)) : cleanInput($bfast); // check length and clip if too big
      } else {
        $error++; // bump the error flag
        $msg .= 'Invalid breakfast.'; // error message
      }
    }
  }

  // save the booking data if the error flag is still clear
  if ($error == 0) {
    $query = "INSERT INTO booking (customerID,roomID,checkindate,checkoutdate,contactnumber,bookingextras,breakfast) 
                      VALUES (?,?,?,?,?,?,?)";
    $stmt = mysqli_prepare($DBC, $query); // prepare the query
    mysqli_stmt_bind_param($stmt, 'iisssss', $customerID, $roomID, $checkindate, $checkoutdate, $contactnumber, $bookingextras, $breakfast);

    if (mysqli_stmt_execute($stmt)) {
      echo "<h2>New booking added to the list.</h2>";
      echo "------------------------------------------------------------------------";
    } else {
      echo "<h2>Something went wrong.</h2>";
    }

    mysqli_stmt_close($stmt);
  } else {
    echo "<h2>$msg</h2>" . PHP_EOL;
  }
}
?>

<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
<script>
  let dateToday = new Date();

  // Make a booking - jQuery UI datapicker (Date Range) //
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

  // Search for room availability - jQuery UI datapicker (Date Range) //
  $(function() {
    let listdates = $("#startdate, #enddate").datepicker({
      dateFormat: 'yy-mm-dd', // set format yy-mm-dd
      defaultDate: 0, // set default date: today for user convenience
      changeMonth: true,
      numberOfMonths: 2,
      minDate: dateToday, // set user cannot select the date before today
      onSelect: function(selectedDate) { // set user cannot select enddate before startdate
        let option = this.id == "startdate" ? "minDate" : "maxDate",
          instance = $(this).data("datepicker"),
          date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
        listdates.not(this).datepicker("option", option, date);
      }
    });
    // test for null error of the datepicker
    function getDate(element) {
      let date;
      try {
        date = $.datepicker.parseDate(dateFormat, element.value);
      } catch (error) {
        date = null;
      }
      return date;
    }
  })

  // 'Search availability' click
  window.onload = function() {
    document.getElementById("btn_search").addEventListener("click", function(event) {
      let startdate = document.getElementById("startdate").value;
      let enddate = document.getElementById("enddate").value;
      if (startdate == "" || enddate == "") {
        document.getElementById("listmsg").innerHTML = "Please select the exact dates to search"; // display error messege
        return;
      } else {
        xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {

            //take JSON text from the server and convert it to JavaScript objects
            let rooms = JSON.parse(this.responseText);
            let tbl = document.getElementById("tblavailability"); //find the table in the HTML

            //clear any existing rows from any previous searches
            //if this is not cleared rows will just keep being added
            let rowCount = tbl.rows.length;
            for (let i = 1; i < rowCount; i++) {
              //delete from the top - row 0 is the table header we keep
              tbl.deleteRow(1);
            }

            //populate the table
            //rooms.length is the size of our array
            for (let i = 0; i < rooms.length; i++) {
              let rid = rooms[i]['roomID'];
              let rname = rooms[i]['roomname'];
              let rtype = rooms[i]['roomtype'];
              let rbeds = rooms[i]['beds'];

              //create a table row with three cells  
              tr = tbl.insertRow(-1);
              let ridCell = tr.insertCell(-1);
              ridCell.innerHTML = rid; //roomID
              let rnameCell = tr.insertCell(-1);
              rnameCell.innerHTML = rname; //roomname      
              let rtypeCell = tr.insertCell(-1);
              rtypeCell.innerHTML = rtype; //roomtype
              let rbedsCell = tr.insertCell(-1);
              rbedsCell.innerHTML = rbeds; //beds       
            }
          }
        }
      }
      //call our php file that will look for rooms matching with period
      xmlhttp.open("GET", "../room/roomsearch.php?startdate=" + startdate + "&enddate=" + enddate, true);
      xmlhttp.send();
    })
  }
</script>

  <h1>Make a booking</h1>
  <h2><a href="listbookings.php">[Return to the booking listing]</a>
    <a href="/onslow-ts/">[Return to the main page]</a>
    <?php
    if (isAdmin()) {
    ?>
      <form method="POST" action="makebooking.php" id="mform">
        <p>
          <label for="roombrowser">Room (name, type, beds): </label>
          <select id="roombrowser" name="roombrowser" required>
            <option hidden="" disabled="disabled" selected="selected" value=""></option> <!-- make hidden option for :invalid css response -->
            <!-- set select options from $roombrowsing -->
            <?php
            if ($roomcount > 0) {
              $selected_room = "";
              while ($rooms = mysqli_fetch_assoc($roomresult)) {
                echo "<option value= '" . $rooms['roomID'] . "'>
                                        " . $rooms['roomname'] . ", " . $rooms['roomtype'] . ", " . $rooms['beds'] . "</option>";
              }
            } else {
              echo "<h2>No available room.</h2>"; // error feedback
            }
            ?>
          </select>
        </p>
        <p>
          <!-- Check-In date datapicker -->
          <label for="checkindate">Check-in date: </label>
          <input type="text" class="f_input" id="checkindate" name="checkindate" required placeholder="yyyy-mm-dd" oninput="autodatePattern(this)">
          <!--other validation is in <script> -->
        </p>
        <p>
          <!-- Check-Out date datapicker -->
          <label for="checkoutdate">Check-out date: </label>
          <input type="text" class="f_input" id="checkoutdate" name="checkoutdate" required placeholder="yyyy-mm-dd" oninput="autodatePattern(this)">
          <!--other validation is in <script> -->
        </p>
        <p>
          <label for="contactnumber">Contact number: </label>
          <input type="tel" class="f_input" id="contactnumber" name="contactnumber" required placeholder="Only Enter 10 Numbers" oninput="handleOnInput(this,10)" pattern="(\(\d{3}\)) \d{3}-\d{4}" maxlength="10">
          <!--this is for validation, more condition and auto pattern are in <script> -->
        </p>
        <p>
          <label for="bookingextras">Booking extras: </label>
          <textarea id="bookingextras" class="f_input" name="bookingextras" rows="5" cols="60"></textarea>
        </p>
        <p>
          <label for="breakfastbrowser">Breakfast: </label>
          <select id="breakfastbrowser" name="breakfastbrowser">
            <?php
            $breakfasts = array("cooked", "continental", "none");
            foreach ($breakfasts as $breakfast) {
              echo "<option value='" . $breakfast . "'>" . $breakfast . " breakfast</option>";
            }
            ?>
          </select>
        </p>
        <input type="submit" name="submit" value="Add">
      </form>
    <?php
    } else {
      echo "<h2>signin as an admin is required for booking at this stage.</h2>"; // error message
    }
    ?>
    <br>
    <br>
    <br>
    <h1>Search for room availability</h1>
    <form id="sform">
      <p>
        <!-- Start date datapicker -->
        <label for="startdate">Start date:&nbsp;</label>
        <input type="text" id="startdate" name="startdate">

        <!-- End date datapicker -->
        <label for="enddate"> &nbsp;&nbsp;&nbsp;&nbsp;End date:&nbsp;</label>
        <input type="text" id="enddate" name="enddate">

        <input type="button" id="btn_search" value="Search availability">
      </p>
    </form>

    <table id="tblavailability">
      <thead>
        <tr>
          <th>Room#</th>
          <th>Roomname</th>
          <th>Roomtype</th>
          <th>Beds</th>
        </tr>
      </thead>
    </table>

    <div id="listmsg"></div>

    <?php
    mysqli_close($DBC); // close the connection once done

    require_once "../footer.php";
    ?>