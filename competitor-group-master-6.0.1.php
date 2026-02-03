<?php
require("include/config.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
		$employee_hierarchy=return_employee_hierarchy($emp_code);
		$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="emp_code='".$emp_code."'";
}

if($nick_name=='START' || $nick_name=='STAR')
{
	$sqlempbranch="SELECT branch_code FROM employee_master WHERE ".$emp_hierarchy_condition;
	$rsempbranch=mysqli_query($link,$sqlempbranch);
	while($rowempbranch=mysqli_fetch_assoc($rsempbranch))
	{
		$branch_value=$rowempbranch['branch_code'];
		$branch_code=$branch_code.$branch_value.',';
	}
	$branch_value_array=explode(',',$branch_code);
	$branch_value_final = "'".implode("','", $branch_value_array)."'";
	$condition_branch=" AND branch_code IN (".$branch_value_final.")";

	$sqlquery="SELECT DISTINCT competitor_name,group_name,UOM FROM competitor_group_master 
	WHERE acedns='yes' ".$condition_branch." AND branch_code <>''  UNION ALL SELECT DISTINCT competitor_name,group_name,UOM 
	FROM competitor_group_master WHERE acedns='yes' AND group_name='WSP' ORDER BY competitor_name ASC";
	//$sqlquery="SELECT DISTINCT * FROM competitor_group_master WHERE 1 ORDER BY competitor_name ASC";
}
else
{
	$sqlquery="SELECT DISTINCT * FROM competitor_group_master WHERE 1 ORDER BY competitor_name ASC";
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'3';
	if($count>0){
		/*$date=date('Y-m-d');
		$time=date('h:i:s');
		$contentsdatetime = $date.'€'.$time."\n";*/
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		
		while($rowcompetitorgroup = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowcompetitorgroup['group_name']!='')?$rowcompetitorgroup['group_name']: ' ')."^";
			$contents  .= (($rowcompetitorgroup['competitor_name']!='')?$rowcompetitorgroup['competitor_name']: ' ')."^";
			$contents  .= (($rowcompetitorgroup['UOM']!='')?$rowcompetitorgroup['UOM']: ' ');
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$last_update_time=str_replace('?','',$last_update_time);
		$data_download_time=str_replace('?','',$data_download_time);
		if(strtotime($data_download_time)>=strtotime($last_update_time))
		{
			$datacontents = '0'.'¥'.'0';
		}
		else
		{
			$datacontents = '0'.'¥'.'3';
		}
	}

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=competitor_group_master.txt");
	print "$datacontents"; 	
?>
