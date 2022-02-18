<?php
include "../config.php"; //load in any variables

$DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit;
}

$name = isset($_GET['name']) ? $_GET['name'] : null;
$n_condition = '';

$room = isset($_GET['room']) ? $_GET['room'] : null;
$r_condition = '';

$checkin = isset($_GET['checkin']) ? $_GET['checkin'] : null;
$checkout = isset($_GET['checkout']) ? $_GET['checkout'] : null;
$d_condition = '';

//if there is a search, add a query search
if (!empty($name)) {
    $n_condition = "AND CONCAT(c.lastname, ' ', c.firstname) LIKE '%" . $name . "%' ";
}

if (!empty($room)) {
    $r_condition = "AND CONCAT(r.roomID, ', ', r.roomname) LIKE '%" . $room . "%' ";
}

if (!empty($checkin) && !empty($checkout)) {
    $d_condition = "AND ((b.checkindate <= '".$checkin."' AND (b.checkoutdate BETWEEN '".$checkin."' AND '".$checkout."'))
                       OR (b.checkindate <= '".$checkin."' AND b.checkoutdate >= '".$checkout."')
                       OR (b.checkindate >= '".$checkin."' AND b.checkoutdate <= '".$checkout."')
                       OR ((b.checkindate BETWEEN '".$checkin."' AND '".$checkout."') AND b.checkoutdate >= '".$checkout."')) ";
}

//prepare a query
$query = "SELECT b.bookingID, b.checkindate, b.checkoutdate, b.breakfast, c.customerID,
                 CONCAT(c.lastname, ' ', c.firstname) AS customer_column,
                 CONCAT(r.roomID, ', ', r.roomname) AS room_column
          FROM booking b
          INNER JOIN room r ON b.roomID = r.roomID
          INNER JOIN customer c ON b.customerID = c.customerID 
          WHERE 1 = 1 " . 
    $n_condition .
    $r_condition .
    $d_condition .
    "ORDER BY bookingID";
$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);
$data = [];

if ($rowcount > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}
echo json_encode($data);

mysqli_free_result($result);
mysqli_close($DBC);