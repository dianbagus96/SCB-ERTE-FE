
<?php
// page2.php

session_start();

echo 'Welcome to page #2<br />';

echo $_SESSION['favcolor']; // green
echo $_SESSION['animal'];   // cat
echo date('Y-m-d H:i:s', $_SESSION['time']);


echo '<br /><a href="test.php">page 1</a><br/>';

echo "Pageviews=". $_SESSION['pw'];
?>

</body>
</html> 