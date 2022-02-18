<?php
include "../config.php"; //load in any variables

$DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit;
}

$name = isset($_GET['name']) ? $_GET['name'] : null;
$condition = '';

//if there is a search, add a query search to $condition
if (!empty($name)) {
    $condition = "WHERE CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $name . "%' ";
}

//prepare a query 
$query = "SELECT c.customerID,
                 CONCAT(c.firstname, ' ', c.lastname) AS customer_column
            FROM customer c " . 
    $condition .
    "ORDER BY customerID";
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
