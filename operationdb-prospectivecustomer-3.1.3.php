<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);
$body=file_get_contents('php://input');

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><prospective_customer><prospective_customer_header><emp_code>4458</emp_code><trans_id>DM445820130522125651</trans_id><datetime>2013-05-22 12:56:51</datetime><latt>0.0</latt><longi>0.0</longi><address>kolkata</address><pin>700001</pin><area>Lalbazar</area><phone_no>4356789</phone_no><garrage_name>N.M motors</garrage_name><customer_name>Neel Mohan</customer_name></prospective_customer_header>
<prospective_customer_details><trans_id>DM445820130522125651</trans_id><sku_code>GFATFDX2-500ML</sku_code></prospective_customer_details>
<prospective_customer_details><trans_id>DM445820130522125651</trans_id><sku_code>GFATFTYPA-210LT</sku_code></prospective_customer_details>
</prospective_customer></root>";

$body="<?xml version='1.0' encoding='UTF-8'?><root><prospective_customer><location><emp_code></emp_code><trans_id></trans_id><date></date><latt></latt><longi></longi></location>
<prospective_customer_header><trans_id></trans_id><address>kolkata</address><pin>700001</pin><area></area><phone_no></phone_no><remarks></remarks><tagged_customer_code></tagged_customer_code><customer_name>Neel Mohan</customer_name></prospective_customer_header>
<prospective_customer_details><trans_id></trans_id><product_code></product_code></prospective_customer_details>
<prospective_customer_details><trans_id></trans_id><product_code></product_code></prospective_customer_details>
</prospective_customer></root>";*/

$emp_code = "*ROOT*PROSPECTIVE_CUSTOMER*LOCATION*EMP_CODE";
$trans_id = "*ROOT*PROSPECTIVE_CUSTOMER*LOCATION*TRANS_ID";
$prospect_date = "*ROOT*PROSPECTIVE_CUSTOMER*LOCATION*DATE";
$latt = "*ROOT*PROSPECTIVE_CUSTOMER*LOCATION*LATT";
$longi = "*ROOT*PROSPECTIVE_CUSTOMER*LOCATION*LONGI";
$prospectiveheader_trans_id = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*TRANS_ID";
$customer_name = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*CUSTOMER_NAME";
$address = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*ADDRESS";
$pin = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*PIN";
$area = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*AREA";
$area_name = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*AREA_NAME";
$phone_no = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*PHONE_NO";
$remarks="*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*REMARKS";
$tagged_customer_code="*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*TAGGED_CUSTOMER_CODE";

$prospectivedetails_trans_id = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_DETAILS*TRANS_ID";
$prospectivedetails_product_code = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_DETAILS*PRODUCT_CODE";

$prospective_customer_array=array();
$prospective_customer_details_array=array();

$counter = 0;
$counterdetails=0;

class xml_customer_header{
    var $emp_code, $trans_id,$prospect_date,$latt,$longi,$prospectiveheader_trans_id,$customer_name,$address,$pin,$area,$area_name,$phone_no,$remarks,$tagged_customer_code;
}
class xml_customer_details{
	var $prospectivedetails_trans_id,$prospectivedetails_product_code;
}

function startTag($parser, $data){
    global $current_tag;
    $current_tag .= "*$data";
}

function endTag($parser, $data){
    global $current_tag;
    $tag_key = strrpos($current_tag, '*');
    $current_tag = substr($current_tag, 0, $tag_key);
}

function contents($parser, $data){
    global $current_tag, $emp_code,$trans_id,$prospect_date,$latt,$longi,$prospectiveheader_trans_id,$customer_name,$address,$pin,$area,$area_name,$phone_no,$remarks,$prospectiveheader_trans_id,$tagged_customer_code,$counter, $counterdetails,$prospectivedetails_trans_id,$prospectivedetails_product_code,$prospective_customer_array,$prospective_customer_details_array;
	//echo $current_tag.'<br />';
	//echo $data;
	if(substr($current_tag,0,26)=='*ROOT*PROSPECTIVE_CUSTOMER')
	{
		switch($current_tag){
			case $emp_code:
				$prospective_customer_array[$counter] = new xml_customer_header();
				$prospective_customer_array[$counter]->emp_code = $data;
				break;
			case $trans_id:
				$prospective_customer_array[$counter]->trans_id = $data;
				break;
			case $prospect_date:
				$prospective_customer_array[$counter]->prospect_date = $data;
				break;	
			case $latt:
				$prospective_customer_array[$counter]->latt = $data;
				break;
			case $longi:
				$prospective_customer_array[$counter]->longi = $data;
				break;
			case $prospectiveheader_trans_id:
				$prospective_customer_array[$counter]->prospectiveheader_trans_id = $data;
				break;
			case $address:
				$prospective_customer_array[$counter]->address = $data;
				break;
			case $pin:
				$prospective_customer_array[$counter]->pin = $data;
				break;
			case $area:
				$prospective_customer_array[$counter]->area = $data;
				break;
			case $area_name:
				$prospective_customer_array[$counter]->area_name = $data;
				break;	
			case $phone_no:
				$prospective_customer_array[$counter]->phone_no = $data;
				break;
			case $remarks:
				$prospective_customer_array[$counter]->remarks = $data;
				break;
			case $tagged_customer_code:
				$prospective_customer_array[$counter]->tagged_customer_code = $data;
				break;	
			case $customer_name:
				$prospective_customer_array[$counter]->customer_name = $data;
				$counter++;
				break;		
		}
	}
	if(substr($current_tag,0,55)=='*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_DETAILS')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $prospectivedetails_trans_id:
				$prospective_customer_details_array[$counterdetails] = new xml_customer_details();
				$prospective_customer_details_array[$counterdetails]->prospectivedetails_trans_id = $data;
				break;
			case $prospectivedetails_product_code:
				$prospective_customer_details_array[$counterdetails]->prospectivedetails_product_code = $data;
				$counterdetails++;
				break;
		}
	}
}
$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "startTag", "endTag");
xml_set_character_data_handler($xml_parser, "contents");
$data = $body;

if(!(xml_parse($xml_parser, $data, LIBXML_PARSEHUGE))){
    die("Error on line " . xml_get_current_line_number($xml_parser));
}
xml_parser_free($xml_parser);

mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");
$flag=1;
/* ------------------------------------------------START QUERY FOR PROSPECTIVE CUSTOMER---------------------------------------------------------------------*/
$prospectivecustomer_array_trans_id=array();
$prospectivecustomerheader_array_mailbody=array();
$prospectivecustomerdate_array_mailbody=array();
$prospectivecustomerdetails_array_mailbody=array();
$prospectivecustomeremp_array_mailbody=array();
$prospectivecustomertansid_array_mailbody=array();
$remark_array_mailbody=array();

if(count($prospective_customer_array)>0)
{
	$prospective_array_trans_id=array();
	for($x=0;$x<count($prospective_customer_array);$x++){
		$emp_code=$prospective_customer_array[$x]->emp_code;
		$trans_id=$prospective_customer_array[$x]->trans_id;
		$prospect_date=$prospective_customer_array[$x]->prospect_date;
		$latt=$prospective_customer_array[$x]->latt;
		$longi=$prospective_customer_array[$x]->longi;
		$prospectiveheader_trans_id=$prospective_customer_array[$x]->prospectiveheader_trans_id;
		$address=$prospective_customer_array[$x]->address;
		$pin=$prospective_customer_array[$x]->pin;
		$area=$prospective_customer_array[$x]->area;
		$area_name=$prospective_customer_array[$x]->area_name;
		$phone_no=$prospective_customer_array[$x]->phone_no;
		$tagged_customer_code=$prospective_customer_array[$x]->tagged_customer_code;
		$customer_name=$prospective_customer_array[$x]->customer_name;	
		$remarks=$prospective_customer_array[$x]->remarks;	
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($latt>0 && $longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$latt."',longi='".$longi."' WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}
		
		//For checking that trans id exist or not
		$sqlchkattlocation="SELECT * FROM location WHERE trans_id='".$trans_id."'";
		$reschkattlocation = mysqli_query($link,$sqlchkattlocation) or die(mysqli_error()." Error in check prospective location: ".$sqlchkattlocation); 
		$rowchkattlocation = mysqli_fetch_assoc($reschkattlocation);
		$countchkattlocation=mysqli_num_rows($reschkattlocation);
		
		//For update the location table for existing trans id
		if($countchkattlocation>0)
		{
			if(!in_array($trans_id,$prospective_array_trans_id))
			{
				array_push($prospective_array_trans_id,$trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$emp_code."',
									latt='".$latt."',
									longi='".$longi."'
									WHERE trans_id='".$trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update prospective customer location: ".$sqlupdateorlocation);
			if($rsupdateorlocation)
			{
				$flag=6;
			}
			else
			{
				echo $flag=0;
			}
		}
		else
		{
			// create the data for prospective customer header table date field , by checking the current date and time and the actual date and time of occurrence
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));
			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			
			//For Insert into the location table for new trans id regarding prospective customer
			$sqlinsertprolocation="INSERT INTO location SET emp_code='".$emp_code."',
									trans_id='".$trans_id."',
									latt='".$latt."',
									longi='".$longi."',
									date='".$prospect_date."',
									updatetime='".$location_date."'"; 

			//For Insert into the prospective customer header table for new trans id regarding prospective customer
			$sqlinsertproheader="INSERT INTO prospective_customer_header SET trans_id='".$prospectiveheader_trans_id."',
									customer_name='".$customer_name."',
									address='".$address."',
									pin='".$pin."',
									area='".$area."',
									phone_no='".$phone_no."',
									remarks='".$remarks."',
									tagged_customer_code='".$tagged_customer_code."'";
			
			if(mysqli_query($link,$sqlinsertprolocation) && mysqli_query($link,$sqlinsertproheader))
				{
					$flag=5;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
				if(substr($area,0,1)=='N'){
				$sqlroute="SELECT route_name FROM route_master WHERE route_code='".$area."'";
				$rsroute=mysqli_query($link,$sqlroute);
				$countroute=mysqli_num_rows($rsroute);
				if($countroute<1)
				{
					$sqlroute  = "insert into route_master ";
					$sqlroute .= " SET route_code='".$area."'";
					$sqlroute .= " ,dns_route_code=''";
					$sqlroute .= " ,route_name='".$area_name."'";
					$sqlroute .= " , emp_code='".$emp_code."'";
					$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
					if(mysqli_query($link,$sqlroute))
					{
						$flag=5;
					}
					else
					{
						mysqli_query($link,"ROLLBACK");
						echo $flag=0;
						return;
					}
				}
			}
				//Regarding Email Sending
				$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
				$rsempname=mysqli_query($link,$sqlempname);
				$rowempname=mysqli_fetch_assoc($rsempname);
				$emp_name=$rowempname['emp_name'];
				$sqlcustomername="SELECT customer_name FROM customer_master WHERE customer_code='".$tagged_customer_code."'";
				$rscustomername=mysqli_query($link,$sqlcustomername);
				$rowcustomername=mysqli_fetch_assoc($rscustomername);
				$tagged_customer_name=$rowcustomername['customer_name'];
				$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$area."'";
				$rsroutename=mysqli_query($link,$sqlroutename);
				$rowroutename=mysqli_fetch_assoc($rsroutename);
				$route_name=$rowroutename['route_name'];
				//For constructing the email body for PROSPECTIVE CUSTOMER HEADER if Customer addition has performed
				$prospectivecustomermailbody = "<html><head><title>Prospective Customer</title></head>
							<body>Auto generated mail for <b>".$nick_name." aceDNS</b> mobile application from<b>"
							.$emp_name. "</b><br><br><br><table border=1 style=background-color:AliceBlue cell cellpadding=0 cellspacing=4>
							<tr>
							<th style='width:260px;min-height:21px;text-align:center' colspan='2'>
							<strong><span style='font-size:10pt;font-family:Arial 
							CE'>Prospect Name</span></strong></th>
							<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Date & Time</span></strong></th>
							<th style='width:250px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Tagged To Customer</span></strong></th>
							</tr><tr><td style='width:260px;text-align:left;min-height:21px;background-color:white' colspan='2'>
							<span style='font-family:Arial CE;font-size:10pt' >
							".strtoupper($customer_name)."</span>&nbsp;</td><td style='width:165px;text-align:right;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".date('d-m-Y H:i:s',strtotime($prospect_date))."</span>&nbsp;</td>
							<td style='width:250px;text-align:right;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".$tagged_customer_name."</span>&nbsp;</td>
							</tr>
							<tr>
							<th style='width:260px;min-height:21px;text-align:center'>
							<strong><span style='font-size:10pt;font-family:Arial 
							CE'>Address</span></strong></th>
							<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Pin</span></strong></th>
							<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Area</span></strong></th>
							</tr><tr><td style='width:260px;text-align:left;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>
							".strtoupper($address)."</span>&nbsp;</td><td style='width:165px;text-align:right;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".$pin."</span>&nbsp;</td>
							<td style='width:165px;text-align:left;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".strtoupper($route_name)."</span>&nbsp;</td></tr>
							<tr>
							<th style='width:260px;min-height:21px;text-align:center'>
							<strong><span style='font-size:10pt;font-family:Arial 
							CE'>Phone no</span></strong></th></tr>
							<tr><td style='width:260px;text-align:left;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>
							+91-".$phone_no."</span>&nbsp;</td></tr></table>";
							
							array_push($prospectivecustomerheader_array_mailbody,$prospectivecustomermailbody);
							$remarkmailbody="Remark: <b>".strtoupper($remarks)."</b>";
							array_push($remark_array_mailbody,$remarkmailbody);
							array_push($prospectivecustomerdate_array_mailbody,$prospect_date);
							array_push($prospectivecustomeremp_array_mailbody,$emp_name);
							array_push($prospectivecustomertansid_array_mailbody,$trans_id);
					}//End of else
		 }// End for loop
	}//End of if
	//For Insert into the Prospective Customer Details table for new trans id
		if(count($prospective_customer_details_array)>0)
		{
			for($i=0;$i<count($prospective_customer_details_array);$i++){

				$prospectivedetails_trans_id=$prospective_customer_details_array[$i]->prospectivedetails_trans_id;
				$prospectivedetails_product_code=$prospective_customer_details_array[$i]->prospectivedetails_product_code;
				
					if(no_of_filter==1){
						$sqlproductdetails="SELECT prod_desc FROM product_master WHERE prod_code='".$prospectivedetails_product_code."'";
					}
					if(no_of_filter==2){
						$sqlproductdetails="SELECT PGM.product_group_name,PM.prod_desc FROM product_master PM,product_group_master PGM 
											WHERE PM.product_group_code=PGM.product_group_code AND PM.prod_code='".$prospectivedetails_product_code."'";
					}
					if(no_of_filter==3){
						$sqlproductdetails="SELECT PGM.product_group_name,PSGM.product_sub_group_name,PM.prod_desc FROM 
											product_master PM,product_group_master PGM,product_sub_group_master PSGM
											WHERE PM.product_group_code=PGM.product_group_code AND PM.product_sub_group_code=PSGM.product_sub_group_code 
											AND PM.prod_code='".$prospectivedetails_product_code."'";
					}
					if(no_of_filter==4){
						$sqlproductdetails="SELECT PGM.product_group_name,PSGM.product_sub_group_name,PBM.product_brand_name,PM.prod_desc
											FROM  product_master PM,product_group_master PGM,product_sub_group_master PSGM,product_brand_master PBM
											WHERE PM.product_group_code=PGM.product_group_code AND PM.product_sub_group_code=PSGM.product_sub_group_code
											AND PM.product_brand_code=PBM.product_brand_code AND PM.prod_code='".$prospectivedetails_product_code."'";
					}
					$rsproductdetails=mysqli_query($link,$sqlproductdetails);
					$rowproductdetails=mysqli_fetch_assoc($rsproductdetails);
					$prod_desc=$rowproductdetails['prod_desc'];
					$product_group_name=$rowproductdetails['product_group_name'];
					$product_brand_name=$rowproductdetails['product_brand_name'];
					$product_sub_group_name=$rowproductdetails['product_sub_group_name'];

					
					if(!in_array($prospectivedetails_trans_id,$prospective_array_trans_id))
					{
						$sqlinsertprodetails="INSERT INTO prospective_customer_details SET trans_id='".$prospectivedetails_trans_id."',
												product_code 	='".$prospectivedetails_product_code."'";
						if(mysqli_query($link,$sqlinsertprodetails))
						{
							$flag=5;
						}
						else
						{
							mysqli_query($link,"ROLLBACK");
							echo $flag=0;
							return;
						}
						
						if(no_of_filter==1){
							$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
						}
						if(no_of_filter==2){
							$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$product_group_name."</span>&nbsp;</td>
											<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
						}
						if(no_of_filter==3){
							$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$product_group_name."</span>&nbsp;</td>
											<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$product_sub_group_name."</span>&nbsp;</td>
											<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
						}
						if(no_of_filter==4){
							$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$product_group_name."</span>&nbsp;</td>
											<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$product_sub_group_name."</span>&nbsp;</td>
											<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$product_brand_name."</span>&nbsp;</td>
											<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
						}

						//For constructing the email body for PROSPECTIVE CUTOMER DETAILS
						 ${a.$prospectivedetails_trans_id} .="
								<tr>".$product_details_TD."</tr>";
					}
			}//End for loop
		}
		//End Insert into the Prospective Customer Details table for new trans id
		//For sending email for Prospective customer
		if(count($prospectivecustomerheader_array_mailbody)>0)
		{
			for($countarr=0;$countarr<count($prospectivecustomerheader_array_mailbody);$countarr++)
			{
				if (strpos($prospectivecustomerheader_array_mailbody[$countarr],'Garage Name') !== false) {
					$table_header_variable='Mechanic visited by';
				}
				else
				{
					$table_header_variable='New prospect visited by ';
				}
				if(no_of_filter==1){
					$product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";
				}
				if(no_of_filter==2){
					$product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col1."</span></strong></th>
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";
				}
				if(no_of_filter==3){
					$product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col1."</span></strong></th>
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col2."</span></strong></th>
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";
				}
				if(no_of_filter==4){
					$product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col1."</span></strong></th>
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col2."</span></strong></th>
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col3."</span></strong></th>
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";
				}

				$prospectivecustomeremailsubj=$table_header_variable.$prospectivecustomeremp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($prospectivecustomerdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($prospectivecustomerdate_array_mailbody[$countarr])).' hrs.';
				$prospectivecustomermailbody = $prospectivecustomerheader_array_mailbody[$countarr]."<br><table border=1 style=background-color:AliceBlue>
							<tr>".$product_details_TH."</tr>".${a.$prospectivecustomertansid_array_mailbody[$countarr]}."</table>
				<br><br><table>".$remark_array_mailbody[$countarr]."</table><br /><br /><br />Powered By <b>aceDNS</b><br></body></html>";
				$headers  = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8\n";
				$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
							"Reply-To:".FROMEMAIL." \r\n" .
							"Bcc: ".BCCEMAIL." \r\n" .
							'X-Mailer: PHP/' . phpversion();
				if(mail(PROSPECTEMAILRECIPENTS, $prospectivecustomeremailsubj, $prospectivecustomermailbody, $headers,'-facedns@coral.in'))
				{
					$flag=5;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
			}
}

 /* --------------------END QUERY FOR PROSPECTIVE CUSTOMER---------------------------------------------------------------------------------------------*/
if($flag==5)
{
	 mysqli_query($link,"COMMIT");
	 if($countdatarefresh >0)
	 {
		 echo $flag=2;
	 }
	 else
	 {
	 	echo $flag=1;
	 }
}
if($flag==6)
{
	mysqli_query($link,"COMMIT");
	 if($countdatarefresh >0)
	 {
		 echo $flag=2;
	 }
	 else
	 {
	 	echo $flag=1;
	 }
}
?>
