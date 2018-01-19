<?php 

if(isset($_POST['month'])){


  $_mo = $_POST['month'] ;

  $year = date("Y");

  $start = 1;
  $ends = date('t', strtotime($_mo.'/'.$year));




}

  


?>