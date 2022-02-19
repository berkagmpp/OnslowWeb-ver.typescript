<?php
include "checksession.php";
?>
<div id="header">
  <div id="logo">
    <div id="logo_text">
      <!-- class="logo_colour", allows you to change the colour of the text -->
      <h1><a href="/onslow/index.php""><span class="logo_colour">Ongaonga Bed & Breakfast</span></a></h1>
      <h2>Make yourself at home is our slogan. We offer some of the best beds on the east coast. Sleep well and rest well.</h2>
    </div>
  </div>
  <div id="menubar">
    <ul id="menu">
      <!-- put class="selected" in the li tag for the selected page - to highlight which page you're on -->
      <li class="selected"><a href="/onslow/index.php">Home</a></li>

      <?php
      // Only admin can see and access Rooms, Customers and Bookings menu
      if (isAdmin()) {
      ?>
        <li><a href="/onslow/room/listrooms.php">Rooms</a></li>
        <li><a href="/onslow/customer/listcustomers.php">Customers</a></li>
        <li><a href="/onslow/booking/listbookings.php">Bookings</a></li>
      <?php
      }
      ?>
      <li><a href="/onslow/login.php">Login</a></li>
    </ul>
  </div>
</div>