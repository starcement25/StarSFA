<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];

$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);
$isNT="no";

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="emp_code='".$emp_code."'";
	$emp_upper_hierarchy_condition="emp_code='".$emp_code."'";
}
if($incremental_download=='no')
{
	if(strtoupper(substr($emp_code,0,1))=='E')
	{
		$login_condition=" AND acedns!='N'";
	}
	if(strtoupper(substr($emp_code,0,1))=='B')
	{
		$login_condition=" AND acedns!='N'";
	}
	if(strtoupper(substr($emp_code,0,1))=='C')
	{
		$login_condition=" AND CRR.acedns!='N'";
	}
	
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
if(strtoupper($nick_name)=='STAR')
	{
		$sqlsaleaccess="SELECT sale_access,branch_code,level FROM employee_master WHERE emp_code='".$emp_code."'";
		$rssaleaccess=mysqli_query($link,$sqlsaleaccess);
		$rowsaleaccess=mysqli_fetch_assoc($rssaleaccess);
		$sale_access_emp=strtoupper($rowsaleaccess['sale_access']);
		$isNT=strtoupper($rowsaleaccess['level']);
	}
if(sale=='yes' ||(strtoupper($nick_name)=='STAR' && (strtoupper($sale_access_emp)=='BD' || strtoupper($sale_access_emp)=='PRIMARY')))
{
//$sqlbranch="SELECT branch_code FROM employee_master WHERE ".$emp_hierarchy_condition."";
$sqlbranch="SELECT branch_code FROM employee_master WHERE emp_code='".$emp_code."'";	
$rsbranch=mysqli_query($link,$sqlbranch);
$rowbranch=mysqli_fetch_assoc($rsbranch);
$branch_code=$rowbranch['branch_code'];	
$branc_code_array=array();
/*while($rowbranch=mysqli_fetch_assoc($rsbranch))
{
	$branch_code=$rowbranch['branch_code'];
	if(!in_array($branch_code,$branc_code_array))
	{
		$branch_code_list=$branch_code_list.$branch_code.',';
		array_push($branc_code_array,$branch_code);
	}
}
$branch_code_list=substr($branch_code_list,0,-1);*/
	
	$branch_code_value_array=explode(',',$branch_code);
	$condition_one=" AND (";
	$condition_two='';
	foreach($branch_code_value_array as $branch_code_val)
	{
		$condition_two.=" FIND_IN_SET( '".$branch_code_val."',branch_code) OR";
	}
	$condition_two=substr($condition_two,0,-2);
	$condition_one.=$condition_two.")";

$sqlquery="SELECT emp_code,emp_name,sale_access,reporting_to,designation,vertical_value,branch_code,state,zone,acedns,lower_leaves,email,region 
			FROM employee_master WHERE 1 ".$condition_one." ORDER BY emp_name ASC";
}
else if(strtoupper($nick_name)=='MAITHAN')
{
	$sqlquery="SELECT emp_code,emp_name,sale_access,reporting_to,designation,vertical_value,branch_code,state,zone,acedns,lower_leaves,email,region 
				FROM employee_master WHERE 1 ".$login_condition." ORDER BY emp_name ASC";
}
else if(strtoupper($nick_name)=='DURO')
{
	$employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code);
	$employee_upper_hierarchy=$employee_upper_hierarchy.','."'".$emp_code."'";
	$emp_upper_hierarchy_condition='emp_code IN('.$employee_upper_hierarchy.')';
	/*$sqlquery="SELECT emp_code,emp_name,sale_access,reporting_to,designation,vertical_value,branch_code,state,zone,acedns,lower_leaves,email,region 
				FROM employee_master WHERE ".$emp_upper_hierarchy_condition.$login_condition." AND emp_code <> '".$emp_code."' ORDER BY emp_name ASC";*/
	$sqlquery="SELECT emp_code,emp_name,sale_access,reporting_to,designation,vertical_value,branch_code,state,zone,acedns,lower_leaves,email,region 
				FROM employee_master WHERE ".$emp_upper_hierarchy_condition.$login_condition."  ORDER BY emp_name ASC";		
}
else if(strtoupper($nick_name)=='NIMBUS')
{
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$sqlselreporting="SELECT reporting_to FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsselreporting=mysqli_query($link,$sqlselreporting);
	$rowselreporting=mysqli_fetch_assoc($rsselreporting);
	$reporting_to=$rowselreporting['reporting_to'];
	$employee_hierarchy=$employee_hierarchy.','."'".$reporting_to."'";
	$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
	$sqlquery="SELECT emp_code,emp_name,sale_access,reporting_to,designation,vertical_value,branch_code,state,zone,acedns,lower_leaves,email,region 
				FROM employee_master WHERE ".$emp_hierarchy_condition.$login_condition." ORDER BY emp_name ASC";
}
else
{
	if(strtoupper(substr($emp_code,0,1))=='E')
	{	
     $sqlquery="SELECT emp_code,emp_name,sale_access,reporting_to,designation,vertical_value,branch_code,state,zone,acedns,lower_leaves,email,region 
				FROM employee_master WHERE 
			".$emp_hierarchy_condition.$login_condition." ORDER BY emp_name ASC";
	}
	if(strtoupper(substr($emp_code,0,1))=='B')
	{
		$sqlquery="SELECT broker_id,broker_name,'','','','','','','',acedns,'',mail_id 
				FROM broker_master WHERE broker_id='".$emp_code."' ".$login_condition." ORDER BY broker_name ASC";
	}
	if(strtoupper(substr($emp_code,0,1))=='C')
	{
		$sqlquery="SELECT DISTINCT CM.customer_code,CM.customer_name,'','','','',CM.branch_code,CM.state_code,CM.zone,CM.acedns,'',CM.email 
				FROM customer_master CM,customer_route_emp_relation CRR WHERE CM.customer_code=CRR.customer_code
			AND CM.customer_code='".$emp_code."' ".$login_condition." ORDER BY CM.customer_name ASC";
	}
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'15';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowemp = mysqli_fetch_assoc($result))
		{
				if(retailer_app=='yes')
				{
					$employee_upper_hierarchy=return_employee_upper_hierarchy($rowemp['emp_code']);
					$emp_upper_hierarchy_condition='emp_code IN('.$employee_upper_hierarchy.')';
					$sqldesignation="SELECT DISTINCT designation FROM employee_master WHERE emp_code IN(".$employee_upper_hierarchy.") 
								AND emp_code <> '".$rowemp['emp_code']."'";
					$rsdesignation=mysqli_query($link,$sqldesignation);
					$countdesignation=mysqli_num_rows($rsdesignation);
					$level=$countdesignation;			
				}
				else
				{
					$sqlemphierarchy="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$rowemp['emp_code']."', reporting_to)";
					$rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
					$cntemphierarchy=mysqli_num_rows($rsemphierarchy);
					if($cntemphierarchy>0)
					{
						$level='2';
					}
					else
					{
						$level='1';
					}
					
					$sqlemphierarchy="SELECT emp_code FROM employee_master WHERE  level='NT_TO' AND emp_code='".$rowemp['emp_code']."'";
					
					$rsemphierarchy1=mysqli_query($link,$sqlemphierarchy);
					$cntemphierarchy1=mysqli_num_rows($rsemphierarchy1);
					if($cntemphierarchy1>0)
					{
						$level='NT_TO';
					}
					//new logic added in 10-04-2026
					$sqlemphierarchy_NT="SELECT emp_code FROM employee_master WHERE  level='NT' AND emp_code='".$rowemp['emp_code']."'";
					
					$rsemphierarchy1_NT=mysqli_query($link,$sqlemphierarchy_NT);
					$cntemphierarchy1_NT=mysqli_num_rows($rsemphierarchy1_NT);
					if($cntemphierarchy1_NT>0)
					{
						$level='NT';
					}
				}
				$sale_access=$rowemp['sale_access'];
				if(strtoupper($nick_name)=='STAR')
				{
					if(strtoupper($sale_access)=='BRANDING' || strtoupper($sale_access)=='TECHNICAL')
					{
						$sale_access='Primary';
					}
					else $sale_access=$sale_access;
				}
				if($emp_code==$rowemp['emp_code'])
				{
					if(strtoupper(substr($emp_code,0,1))=='E')
					{	
						$login_type='employee';
					}
					if(strtoupper(substr($emp_code,0,1))=='B')
					{	
						$login_type='broker';
					}
					if(strtoupper(substr($emp_code,0,1))=='C')
					{	
						$login_type='customer';
					}
				}
				else
				{
					$login_type='';
				}
				if(strtoupper(substr($emp_code,0,1))=='E')
					{
				$contents  = (($rowemp['emp_code']!='')?$rowemp['emp_code']: ' ')."^";
				$contents  .= (($rowemp['emp_name']!='')?$rowemp['emp_name']: ' ')."^";
				$contents  .= (($sale_access!='')?$sale_access: ' ')."^";
				$contents  .= (($rowemp['reporting_to']!='')?$rowemp['reporting_to']: ' ')."^";
				$contents  .= (($level!='')?$level: ' ')."^";
				$contents  .= (($rowemp['designation']!='')?$rowemp['designation']: ' ')."^";
				$contents  .= (($rowemp['vertical_value']!='')?$rowemp['vertical_value']: ' ')."^";
				$contents  .= (($rowemp['branch_code']!='')?$rowemp['branch_code']: ' ')."^";
				$contents  .= (($rowemp['state']!='')?$rowemp['state']: ' ')."^";
				$contents  .= (($rowemp['zone']!='')?$rowemp['zone']: ' ')."^";
				$contents  .= (($rowemp['acedns']!='')?$rowemp['acedns']: ' ')."^";
				$contents  .= (($rowemp['lower_leaves']!='')?$rowemp['lower_leaves']: ' ')."^";
				$contents  .= (($rowemp['email']!='')?$rowemp['email']: ' ')."^";
				$contents  .= (($login_type!='')?$login_type: ' ')."^";
				$contents  .= (($rowemp['region']!='')?$rowemp['region']: ' ');
					}
				if(strtoupper(substr($emp_code,0,1))=='C')
					{
				$contents  = (($rowemp['customer_code']!='')?$rowemp['customer_code']: ' ')."^";
				$contents  .= (($rowemp['customer_name']!='')?$rowemp['customer_name']: ' ')."^";
				$contents  .=' '."^";
				$contents  .= ' '."^";
				$contents  .= ' '."^";
				$contents  .= ' '."^";
				$contents  .= ' '."^";
				$contents  .= (($rowemp['branch_code']!='')?$rowemp['branch_code']: ' ')."^";
				$contents  .= (($rowemp['state_code']!='')?$rowemp['state_code']: ' ')."^";
				$contents  .= (($rowemp['zone']!='')?$rowemp['zone']: ' ')."^";
				$contents  .= (($rowemp['acedns']!='')?$rowemp['acedns']: ' ')."^";
				$contents  .=  ' '."^";
				$contents  .= (($rowemp['email']!='')?$rowemp['email']: ' ')."^";
				$contents  .= 'customer';
				$contents  .= ' ';
					}	
				
				$linecontents  .= $contents."\n";
				
		}
		//echo "tttt".$isNT;
		if($isNT=='NT' || $isNT=='NT_TO1'){
		    
		    
		    $sqlquery1="SELECT emp_code,emp_name,sale_access,reporting_to,designation,vertical_value,branch_code,state,zone,acedns,lower_leaves,email,region 
				FROM employee_master WHERE level='NT_TO' ORDER BY emp_name ASC";
		    //echo $sqlquery1;
		    $result1 = mysqli_query($link,$sqlquery1);
            $count1=mysqli_num_rows($result1);
            
		    $count1=$count1+$count;
		    //echo $count1; echo "--".$count ;exit();
		    $contentsrowcolumn  =$count1.'¥'.'15';
		    while($rowemp1 = mysqli_fetch_assoc($result1))
		        {
		    
		    
		        $sqlemphierarchy="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$rowemp1['emp_code']."', reporting_to)";
					$rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
					$cntemphierarchy=mysqli_num_rows($rsemphierarchy);
					if($cntemphierarchy>0)
					{
						$level='2';
					}
					else
					{
						$level='1';
					}
					
					$sqlemphierarchy="SELECT emp_code FROM employee_master WHERE  level='NT_TO' AND emp_code='".$rowemp1['emp_code']."'";
					
					$rsemphierarchy1=mysqli_query($link,$sqlemphierarchy);
					$cntemphierarchy1=mysqli_num_rows($rsemphierarchy1);
					if($cntemphierarchy1>0)
					{
						$level='NT_TO';
					}
					
		    		//new logic added in 10-04-2026
					$sqlemphierarchy_NT="SELECT emp_code FROM employee_master WHERE  level='NT' AND emp_code='".$rowemp1['emp_code']."'";
					
					$rsemphierarchy1_NT=mysqli_query($link,$sqlemphierarchy_NT);
					$cntemphierarchy1_NT=mysqli_num_rows($rsemphierarchy1_NT);
					if($cntemphierarchy1_NT>0)
					{
						$level='NT';
					}
		    
		    
		    
		    
		    
				$contents  = (($rowemp1['emp_code']!='')?$rowemp1['emp_code']: ' ')."^";
				$contents  .= (($rowemp1['emp_name']!='')?$rowemp1['emp_name']: ' ')."^";
				$contents  .= (($rowemp1['sale_access']!='')?$rowemp1['sale_access']: ' ')."^";
				$contents  .= (($rowemp1['reporting_to']!='')?$rowemp1['reporting_to']: ' ')."^";
				$contents  .= (($level!='')?$level: ' ')."^";
				$contents  .= (($rowemp1['designation']!='')?$rowemp1['designation']: ' ')."^";
				$contents  .= (($rowemp1['vertical_value']!='')?$rowemp1['vertical_value']: ' ')."^";
				$contents  .= (($rowemp1['branch_code']!='')?$rowemp1['branch_code']: ' ')."^";
				$contents  .= (($rowemp1['state']!='')?$rowemp1['state']: ' ')."^";
				$contents  .= (($rowemp1['zone']!='')?$rowemp1['zone']: ' ')."^";
				$contents  .= (($rowemp1['acedns']!='')?$rowemp1['acedns']: ' ')."^";
				$contents  .= (($rowemp1['lower_leaves']!='')?$rowemp1['lower_leaves']: ' ')."^";
				$contents  .= (($rowemp1['email']!='')?$rowemp1['email']: ' ')."^";
				$contents  .= (($login_type!='')?$login_type: ' ')."^";
				$contents  .= (($rowemp1['region']!='')?$rowemp1['region']: ' ');
					
					
					$linecontents  .= $contents."\n";
		        }
		    
		}
		
		
		
		
		
		
		
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		//$datacontents = '0'.'¥'.'0';
		$last_update_time=str_replace('?','',$last_update_time);
		$data_download_time=str_replace('?','',$data_download_time);
		if(strtotime($data_download_time)>=strtotime($last_update_time))
		{
			$datacontents = '0'.'¥'.'0';
		}
		else
		{
			$datacontents = '0'.'¥'.'14';
		}
	}
	
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/emp-master-txt-6.0.9.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=emp_master.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>