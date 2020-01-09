<?php
	$tanggal = "2019-11-19T06:40:49.000Z";
	$tanggal = (explode("T",$tanggal));
	$diff=date_diff(date_create($tanggal[0]),date_create(date("Y-m-d")));
	echo $diff->format("%a days");

?>