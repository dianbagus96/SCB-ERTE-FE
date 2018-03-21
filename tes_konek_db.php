 <?php
   $conn = oci_connect('UATRTE', 'U4t_Rt3_t3st', 'IDXDB2');
   if (!$conn) {
   $e = oci_error();
     print htmlentities($e['message']);
  exit;
 }else{
 print 'Koneksi PHP Ke Oracle Berhasil Bung...';
  }
 ?>

