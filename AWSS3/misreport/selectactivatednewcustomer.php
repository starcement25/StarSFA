<?php
require("include/config.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
$state=$_REQUEST['state'];
$tableval='
<table width="57%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 250px;overflow-y: scroll;display:block;">
    <tr class="TDHEAD" > 
        <td colspan="8" align="center"><strong>Emp Name: '.$emp_name.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Emp Code:'. $emp_code.'</strong></td>
    </tr>
    <tr class="TDHEAD_SUB"> 
        <td width="5%" align="center">Sl</td>
        <td width="25%" align="left" style="padding-left:20px;">Customer name</td>
        <td width="8%" align="left" style="padding-left:20px;">>Phone no</td>
		 <td width="20%" align="left" style="padding-left:20px;">Employee</td>
        <td width="12%" align="left" style="padding-left:20px;">Route</td>
		<td width="10%" align="left" style="padding-left:20px;">State</td>
		<td width="8%" align="left" style="padding-left:20px;">Cust typ</td>
		<td width="12%" align="left" style="padding-left:20px;">Mapped distributor</td>
    </tr> ';
        
    $sqlinformation="SELECT DISTINCT CM.customer_code,CM.customer_name,EM.emp_name,CM.cust_type,CM.rds_tag,CM.phone_no,CRR.route_code,CM.state_code
	 		FROM customer_master CM,customer_route_emp_relation CRR,employee_master EM 
			WHERE CM.customer_code=CRR.customer_code AND CRR.emp_code=EM.emp_code AND CM.activated='yes' AND CM.activated_by='".$emp_code."' 
				AND CM.state_code='".$state."' ORDER BY CM.phone_no ASC ";
    $resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select transaction information: ".$sqlinformation);
    $count=mysql_num_rows($resinformation);
        $cnt=$GLOBALS[start]+1;
        while($rowinformation=mysql_fetch_array($resinformation))
        {
			$customer_code=$rowinformation['customer_code'];
			$customer_name=$rowinformation['customer_name'];
			$emp_name=$rowinformation['emp_name'];
			$cust_type=$rowinformation['cust_type'];
			$rds_tag=$rowinformation['rds_tag'];
			$phone_no=$rowinformation['phone_no'];
			$route_code=$rowinformation['route_code'];
			$state_code=$rowinformation['state_code'];
			$sqlroute="SELECT route_name FROM route_master WHERE route_code='".$route_code."'";
			$rsroute=mysql_query($sqlroute);
			$rowroute=mysql_fetch_array($rsroute);
			$route_name=$rowroute['route_name'];
			
			$sqldistributor="SELECT customer_name FROM customer_master WHERE customer_code='".$rds_tag."'";
			$rsdistributor=mysql_query($sqldistributor);
			$rowdistributor=mysql_fetch_array($rsdistributor);
			$distributor_name=$rowdistributor['customer_name'];
			$rowval.='<tr> 
                    <td valign="top" align="center">'.$cnt++.'</td>
                    <td align="left" valign="top" style="padding-left:20px;">'.$customer_name.'</td>
                    <td align="left" valign="top" style="padding-left:20px;">'.$phone_no.'</td>
					<td align="left" valign="top" style="padding-left:20px;">'.$emp_name.'</td>
					<td align="left" valign="top" style="padding-left:20px;">'.$route_name.'</td>
					<td align="left" valign="top" style="padding-left:20px;">'.$state_code.'</td>
					<td align="left" valign="top" style="padding-left:20px;">'.$cust_type.'</td>
					<td align="left" valign="top" style="padding-left:20px;">'.$distributor_name.'</td>
              </tr>';
        }
$tablevalend='</table>';				
$finalval=$tableval.$rowval.$tablevalend;

echo $finalval;

mysql_close($link);
?>