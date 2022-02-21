<?php
include "../header.php";
include "../menu.php";
loginStatus(); //show the current login status

echo '<div id="site_content">';
include "../sidebar.php";

echo '<div id="content">';

include "../config.php"; //load in any variables
?>
  <script defer src="../script/listrooms.js"></script>

  <h1>Room list</h1>
  
  <table id="tblrooms" border="1">
    <thead>
      <tr>
        <th>ID</th>
        <th>Room Name</th>
        <th>Type</th>
        <th>Action</th>
      </tr>
    </thead>
  </table>

  <h3><a href='addroom.php'>[Add a room]</a><a href="/onslow-ts/">[Return to main page]</a></h3>

  <?php
  require_once "../footer.php";
  ?>