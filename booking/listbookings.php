<?php
include "../header.php";
include "../menu.php";
loginStatus(); //show the current login status

echo '<div id="site_content">';
include "../sidebar.php";

echo '<div id="content">';

include "../config.php"; //load in any variables
?>
<script defer src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script defer src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
<script defer src="../script/listbookings.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
<h1>Current bookings</h1>

<br>
<form action="./listbookings.php" method="get">
    <p>
        <label for="input_name">Customer name:&nbsp; </label>
        <input id="input_name" type="text" placeholder="Start typing a name">
        <label for="input_room">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Room:&nbsp;</label>
        <input id="input_room" type="text" placeholder="Room No. / Name">
    </p>
    <p>
        <label for="input_checkin">Duration:&nbsp;</label>
        <input id="input_checkin" type="text" placeholder="yyyy-mm-dd">
        <label for="input_checkout">&nbsp;-&nbsp;</label>
        <input id="input_checkout" type="text" placeholder="yyyy-mm-dd">
        <input id="date_search" type="button" value="CHECK">
    </p>
    <div id="msg"></div><br>
    <p>
        <input id="refresh" type="button" value="ALL REFRESH">
    </p>
</form>
<br>
<table id="tblbookings" border="1">
    <thead>
        <tr>
            <th>Booking ID</th>
            <th>Room</th>
            <th>Customer</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Breakfast</th>
            <th>Action</th>
        </tr>
    </thead>
</table>

<h2><a href="makebooking.php">[Make a booking]</a><a href="/onslow-ts/">[Return to the main page]</a></h2>

<?php
require_once "../footer.php";
?>