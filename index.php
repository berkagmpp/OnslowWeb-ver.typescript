<?php
include "header.php";

include "menu.php";
loginStatus(); //show the current login status

echo '<div id="site_content">';
include "sidebar.php";

echo '<div id="content">';
include "content.php";

echo '</div></div>';
include "footer.php";
?>
