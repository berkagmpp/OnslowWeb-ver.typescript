<?php
// Changed default domain name from localhost to given domain name by AWS EC2

session_start();

// function to checkCheck whether the account is admin or not
function isAdmin()
{
    if (isset($_SESSION['loggedin'])) {
        if (($_SESSION['loggedin'] == 1) and ($_SESSION['userid'] == 1))
            return TRUE;
        else
            return FALSE;
    }
}

//function to check if the user is logged else send to the login page 
function checkUser()
{
    return true;
    $_SESSION['URI'] = '';

    if ($_SESSION['loggedin'] == 1)
        return TRUE;
    else {
        $_SESSION['URI'] = 'http://ec2-3-25-68-166.ap-southeast-2.compute.amazonaws.com' . $_SERVER['REQUEST_URI']; //save current url for redirect     
        header('Location: http://ec2-3-25-68-166.ap-southeast-2.compute.amazonaws.com/bnb/login.php', true, 303);
    }
}

// function to show we are are logged in
function loginStatus()
{
    $un = $_SESSION['username'];
    if ($_SESSION['loggedin'] == 1)
        echo "<div id='login_status'>Logged in as $un</div>";
    else
        echo "<div id='login_status'>Logged out</div>";
}

// function to log a user in
function login($username, $password)
{
    // Connect to DB
    $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

    // Check error
    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
        exit; //stop processing the page further
    }

    // Select user id and last name by user input
    $query = "SELECT customerid, lastname FROM customer WHERE email = '" . $username . "' and password = '" . $password . "'";

    $result = mysqli_query($DBC, $query);
    $rowcount = mysqli_num_rows($result);

    // If user exists
    if ($rowcount > 0) {
        //simple redirect if a user tries to access a page they have not logged in to
        if ($_SESSION['loggedin'] == 0 and !empty($_SESSION['URI']))
            $uri = $_SESSION['URI'];
        else {
            $_SESSION['URI'] =  'http://ec2-3-25-68-166.ap-southeast-2.compute.amazonaws.com/bnb/customer/listcustomers.php';
            $uri = $_SESSION['URI'];
        }

        $row = mysqli_fetch_assoc($result);
        $_SESSION['loggedin'] = 1;
        $_SESSION['userid'] = $row['customerid']; // Set up the user id from DB
        $_SESSION['username'] = $row['lastname']; // Set up the last name from DB
        $_SESSION['URI'] = '';
        header('Location: ' . $uri, true, 303);
    } else {
        return false;
    }
}

// function to logout
function logout()
{
    $_SESSION['loggedin'] = 0;
    $_SESSION['userid'] = -1;
    $_SESSION['username'] = '';
    $_SESSION['URI'] = '';
    header('Location: http://ec2-3-25-68-166.ap-southeast-2.compute.amazonaws.com/bnb/login.php', true, 303);
}
