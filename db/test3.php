
<?php
error_reporting(E_ALL);
session_start();

echo 'Welcome to page #1';

$_SESSION["favcolor"] = 'green';
$_SESSION['animal']   = 'cat';
$_SESSION['time']     = time();


$pw = 'dsdsada';

$_SESSION['views']=1;
$_SESSION['username'] = 'user';
$_SESSION['password'] = 'Pw';


echo '<br /><a href="test2.php">page 2</a></br>';
echo $_SESSION['favcolor'];
?>



<?php
   if( isset( $_SESSION['counter'] ) )
   {
      $_SESSION['counter'] += 1;
   }
   else
   {
      $_SESSION['counter'] = 1;
   }
   $msg = "Dalam session ini anda telah mengunjungi halaman ini ".  $_SESSION['counter'] . " kali";
?>
<html>
<head>
<title>PHP session</title>
</head>
<body>
<?php  echo( $msg ); ?>
</body>
</html>

