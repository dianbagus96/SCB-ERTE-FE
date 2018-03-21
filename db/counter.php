<?php
session_start();
$counter++;
print "You have visited this page $counter times during this session";
session_register("counter");
?> 
