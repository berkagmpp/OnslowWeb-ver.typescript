<?php
include "header.php";
include "menu.php";
loginStatus(); //show the current login status

echo '<div id="site_content">';
include "sidebar.php";

echo '<div id="content">';

include "config.php"; //load in any variables
$DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

//insert DB code from here onwards
//check if the connection was good
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit; //stop processing the page further
}

// if there are variables $_POST 'btn_sighin', execute the login function
if (array_key_exists('btn_signin', $_POST)) {
    // $id = $_POST['id'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) && empty($password)) {
        echo "<h2><font color='red'>Please enter ID and PASSWORD.</font><h2>";
    } else {
        if(!login($username, $password)){
            echo "<h2><font color='red'>Your log on attempt was unsuccessful.</font><h2>";
        }
    }
    // if there are variables $_POST 'btn_logout', execute the logout function
} else if (array_key_exists('btn_logout', $_POST)) {
    logout();
}
?>
<h1>Login</h1>

<form method="post">
    <?php
    // show login form if not loggingin
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 1) {
        ?>
            <input type='submit' id='btn_logout' name='btn_logout' value='LOGOUT'>
        <?php
        } else { // show messege and logout button if already logedin
        ?>
            <p>
                <label for="name'">USER NAME: </label>
                <input type="text" name="username">
            </p>
            <p>
                <label for="password">USER PASSWORD: </label>
                <input type="password" name="password">
            </p>
            <input type="submit" id="btn_signin" name="btn_signin" value="SIGN IN">
        <?php
        }
    ?>
</form>

<?php
require_once "footer.php";
?>