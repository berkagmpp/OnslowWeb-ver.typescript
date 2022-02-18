<?php
    //Our room search/filtering engine
    include "../config.php"; //load in any variables
    $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE) or die();

    //do some simple validation to check if sq contains a string
    $startdate = $_GET['startdate'];
    $enddate = $_GET['enddate'];
    $orderdata = '';
    if (isset($startdate, $enddate) and !empty($startdate) and !empty($enddate) and strlen($startdate) == 10 and strlen($enddate) == 10) {
        //prepare a query and send it to the server using our startd and endd
        $query = "SELECT roomID, roomname, roomtype, beds
                FROM room
                WHERE roomID NOT IN (SELECT roomID
                                    FROM booking
                                    WHERE (checkindate <= '".$startdate."' AND (checkoutdate BETWEEN '".$startdate."' AND '".$enddate."'))
                                            OR (checkindate <= '".$startdate."' AND checkoutdate >= '".$enddate."')
                                            OR (checkindate >= '".$startdate."' AND checkoutdate <= '".$enddate."')
                                            OR ((checkindate BETWEEN '".$startdate."' AND '".$enddate."') AND checkoutdate >= '".$enddate."')
                                    )";
        $result = mysqli_query($DBC,$query);
        $rowcount = mysqli_num_rows($result); 
            //makes sure we have customers
        if ($rowcount > 0) {  
            $rows=[]; //start an empty array
            //append each row in the query result to our empty array until there are no more results                    
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
            // take the array of our 1 or more rooms and turn it into a JSON text
            $orderdata = json_encode($rows);
            // this line is cruicial for the browser to understand what data is being sent
            header('Content-Type: text/json; charset=utf-8');
        } else echo "<tr><td colspan=3><h2>No available room found!</h2></td></tr>";
    } else echo "<tr><td colspan=3> <h2>Invalid search query</h2>";

    mysqli_free_result($result); //free any memory used by the query
    mysqli_close($DBC); //close the connection once done

    echo  $orderdata;
