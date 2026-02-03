<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db("acedns_STAR",$link) or die("could not connect the database for invalid nick name");
	
	$today = date("d"); // Current day
	$year = '2016'; // Current year
	
	for($i=1;$i<=12;$i++)
	{
		if(strlen($i)==1)
		{
			$i='0'.$i;
		}
		$month = date("$i"); // Current month
		$first_date='01'.'-'.$month.'-'.$year;
		$month_abrev=date('M',strtotime($first_date));
		$days = cal_days_in_month(CAL_GREGORIAN,$month,$year); // Days in current month
		for($k=1;$k<=$days;$k++)
		{
			$column_name_tgt=strtolower($month_abrev).'_'.$k.'_target';
			$column_name_ach=strtolower($month_abrev).'_'.$k.'_achievement';
			$sql_tgt="ALTER TABLE self_appraisal ADD $column_name_tgt DOUBLE DEFAULT NULL";
			$sql_ach="ALTER TABLE self_appraisal ADD $column_name_ach DOUBLE DEFAULT NULL";
			//exit();
			mysqli_query($link,$sql_tgt);
			mysqli_query($link,$sql_ach);
		}
		//exit();
	}
	//echo $month.'<br />';
	//echo $days.'<br />';
