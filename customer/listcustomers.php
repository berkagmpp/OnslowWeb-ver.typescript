<?php
include "../header.php";
include "../menu.php";
loginStatus(); //show the current login status

echo '<div id="site_content">';
include "../sidebar.php";

echo '<div id="content">';

include "../config.php"; //load in any variables

?>

    <script defer src="../script/listcustomers.js"></script>

    <h1>Customer List Search by Name</h1>

    <form action="./listcustomers.php" method="get">
        <P>
            <label for="input_name">Customer name:&nbsp; </label>
            <input id="input_name" type="text" size="30" placeholder="Start typing a name">
        </p>
        <div id="msg"></div>
        <p>
            <input id="refresh" type="button" value="REFRESH">
        </P>
    </form>

    <table id="tblcustomers" border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>actions</th>
            </tr>
        </thead>
    </table>

    <h3><a href='registercustomer.php'>[Create new Customer]</a><a href="/onslow-ts/">[Return to main page]</a></h3>

<?php
require_once "../footer.php";
?>