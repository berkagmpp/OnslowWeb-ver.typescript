<?php
include "header.php";

include "menu.php";
loginStatus(); //show the current login status

echo '<div id="site_content">';
include "sidebar.php";

echo '<div id="content">';
?>

<h1>Privacy Policy</h1>

<p>We collect personal information from you, including information about your:</p>
<ul>
    <li>name</li>
    <li>contact information</li>
    <li>Location</li>
    <li>interactions with us</li>
</ul>

<p>We collect your personal information in order to:</p>
<ul>
    <li>reserve and book accommodation for you at Ongaonga Bed and Breakfast</li>
    <li>prevent unauthorised access</li>
    <li>conduct our business and market our services</li>
</ul>

<p>We keep your information safe by storing it in encrypted files and allowing only authorised staff to use it.</p>
<p>We keep your information for two years at which point we securely destroy it by erasing digital traces and manual documents are shredded.</p>
<p>You have the right to ask for a copy of any personal information we hold about you, and to ask for it to be corrected if you think it is wrong. If you’d like to ask for a copy of your information, or to have it corrected, please contact us at <a href="mailto:admin@ongaongabnb.nz">admin@ongaongabnb.nz</a>, or 012-345-6789, or 1 Black Street, Ongaonga 0123, New Zealand.</p>
<?php

echo '</div></div>';
include "footer.php";
?>