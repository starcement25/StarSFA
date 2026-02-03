<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$mode = $_REQUEST['mode'];
	

	disphtml("main();");
ob_end_flush();
function main()
{
	if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one='';
		$emp_upper_hierarchy='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition_one=' AND EM.emp_code IN('.$emp_hierarchy.')';
		$emp_upper_hierarchy=return_employee_upper_hierarchy($_SESSION['admin_login']);
		if(strpos($emp_upper_hierarchy,',')==false){
			$emp_upper_hierarchy=str_replace("'","",$emp_upper_hierarchy);
		}
	}
?>
<script language="javascript">

function GetXmlHttpObject()
{
	var xmlHttp=null;
	try
	{
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}
function RdswiseDataDownload()
{
	//alert(val);
	if (document.frmSearch.branch_name.value==0) 
	{
		alert('Please select a branch.');
		document.frmSearch.branch_name.focus();
		return false;
	}
	/*if (document.frmSearch.rds_name.value==0) 
	{
		alert('Please select a depot.');
		document.frmSearch.rds_name.focus();
		return false;
	}*/
	document.frmSearch.submit();
 }
function showDetailsRdsWiseSales()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		if(val!="")
		 {
			document.getElementById('productwisesalesdisplay').style.display='none';
			document.getElementById('rdswisesalesdisplay').style.display='';
		 	document.getElementById('rdswisesalesdisplay').innerHTML=val;
			document.getElementById('loader').style.display='none';
		 }
	}
	else
	{
		document.getElementById('loader').style.display='';
	}
 }
function showProductwisesalesDeatails(val1,val2,val3,val4,val5,val6)
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
  
	var url="selectProductWiseSales.php?emp_code="+val2+"&rds_code="+val1+"&from_date="+val3+"&to_date="+val4+"&radio_type="+val5+"&prod_code="+val6;
	xmlHttp.onreadystatechange=showDetailsProductWiseSales;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
  }
function showDetailsProductWiseSales()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		if(val!="")
		 {
			//document.getElementById('rdswisesalesdisplay').style.display='none';
			document.getElementById('productwisesalesdisplay').style.display='';
		 	document.getElementById('productwisesalesdisplay').innerHTML=val;
			document.getElementById('loader').style.display='none';
		 }
	}
	else
	{
		document.getElementById('loader').style.display='';
	}
 }
function search_att(val)
{
	if(val=='yourchoice')
	{
		document.getElementById('datedropdown').style.display='';
	}
	else
	{
		document.getElementById('datedropdown').style.display='none';
	}
}
function select_depot(branch_code_val)
	{
	  document.frmdepot.branch_code.value=branch_code_val;
	  document.frmdepot.submit();
	}
function select_sku(brand_code_val)
	{
	  var branch_code=document.frmSearch.branch_name.value;
	 var rds_code=document.frmSearch.rds_name.value;
	  document.frmsku.brand_code_val.value=brand_code_val;
	  document.frmsku.branch_code.value=branch_code;
	  document.frmsku.rds_name.value=rds_code;
	  document.frmsku.submit();
	}

</script>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>SALES REPORT</strong></td>
	</tr>
    
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
        	<form name ="frmdepot" method="post" action="<?=$_SERVER['PHP_SELF']?>">
				<input type="hidden" name="branch_code" value="">
            </form>
            <form name ="frmsku" method="post" action="<?=$_SERVER['PHP_SELF']?>">
				<input type="hidden" name="brand_code_val" value="">
                <input type="hidden" name="branch_code" value="">
                <input type="hidden" name="rds_name" value="">
            </form>
            </form>
            </form>
        	<form name = "frmAttendence" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<input type="hidden" name="search_mode" value="sales_search">
			<input type="hidden" name="row_id" value="<?=$_REQUEST['row_id']?>">
			<input type="hidden" name="mode" >
			<br><br>
			</form>
                <table width="80%" align="center" border="0" cellpadding="5" cellspacing="1" >
                    <tr> 
                        <td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);?></td>
                        <td align="right" colspan="2"></td>
                    </tr>
                </table>
               
                <!--------------------------------Start Table for first time page loading---------------------------------!-->
                
                <table width="65%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
                	<form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
					<input type="hidden" name="mode" value="data_download">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="15%" align="center"></td>
                        <table width="65%" align="center" border="0" cellpadding="5" cellspacing="1"  class="border">
                         <tr>
                                <td align="right" width="45%" colspan="2">Branch:</td>
                                <td align="left" width="" style="vertical-align:top;" colspan="2">
                                   <?php $branch_name=$_REQUEST['branch_name'];?>
                                    <select name="branch_name" id="branch_name" onChange="javascript:select_depot(this.value);">
                                    <option value="0">SELECT</option>
                                     <?php 
                                     $sqlquerybranch="SELECT DISTINCT BM.branch_code,BM.branch_name FROM branch_master BM,employee_master EM 
									 					WHERE BM.branch_code=EM.branch_code ".$emp_hierarchy_condition_one." ORDER BY BM.branch_name ASC";
                                     $resultbranch = mysql_query($sqlquerybranch);
                                     $count=mysql_num_rows($resultbranch);
                                     $cnt=1;
                                        if($count>0){
                                        while($rowbranch = mysql_fetch_array($resultbranch))
                                        {
                                      ?>
                                         <option value="<?php echo $rowbranch['branch_code'];?>" <?php if($branch_name==$rowbranch['branch_name'] || 
                                         $_REQUEST['branch_code']==$rowbranch['branch_code']){echo 'selected';}?>><?php echo $rowbranch['branch_name'];?></option>
                                       <?php
                                        }
                                      }
                                        ?>	
                                     </select>
                                </td>
                           </tr>
                            <tr>
                                <td align="right" width="45%" colspan="2">Depot:</td>
                                <td align="left" width="" style="vertical-align:top;" colspan="2">
                                <?php 
										$rds_name=$_REQUEST['rds_name'];
										$branch_code=$_REQUEST['branch_code'];
								?>	 
                                	<select name="rds_name" id="rds_name">
                                    <option value="all">ALL</option>
                                     <?php 
									 $sqlqueryrds="SELECT RM.rds_code,RM.rds_name,RM.emp_code FROM rds_master RM,employee_master EM WHERE 
									 			RM.emp_code=EM.emp_code AND EM.branch_code='".$branch_code."' ".$emp_hierarchy_condition_one."";
									 $resultrds = mysql_query($sqlqueryrds);
									 $countrds=mysql_num_rows($resultrds);
									 if($countrds>0){
									while($rowrds = mysql_fetch_array($resultrds))
									{
										//echo $emp_code=$rowrds['emp_code'];
                                     ?>
                                         <option value=<?php echo $rowrds['rds_code']; ?> <?php if( $rds_name==$rowrds['rds_code']){echo 'selected';}?>>
                                         <?php echo $rowrds['rds_name']; ?>
                                         </option>
                                       <?php }?>
                                   <?php }?>   
                                     </select>
                                </td>
                       		</tr>
                            <tr id="datedropdown" >
                                <td align="left" width="15%">From Date:</td>
                                <td align="left" width="30%" style="vertical-align:top;">
                                		<?php $from_date=$_REQUEST['from_date'];?>
                                      <input id="textinput3" type="text" value="<?php echo str_replace('/','-',$from_date);?>" name="from_date"></input>&nbsp;
                                        <a href="javascript:cal5.popup();"><img style="cursor:hand;position:absolute;border:0;" border="0" src="images/cal.gif" width="20" height="18" ></a>
                                    </label>
                                    <script language="JavaScript" type="text/javascript">
                                        <!-- // create calendar object(s) just after form tag closed
                                         // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
                                         // note: you can have as many calendar objects as you need for your application
                                        var cal5 = new calendar3(document.forms['frmSearch'].elements['from_date']);
                                        cal5.year_scroll = true;
                                        cal5.time_comp = false;
                                        //-->
                                    </script>
                                </td>
                                <td width="15%" align="left" style="padding-left:10px;">To Date:</td>
                                <td width="" style="vertical-align:top;">
                                    <?php $to_date=$_REQUEST['to_date'];?>
                                     <input id="textinput3" type="text" value="<?php echo str_replace('/','-',$to_date);?>" name="to_date"></input>&nbsp;
                                        <a href="javascript:cal6.popup();"><img style="cursor:hand;position:absolute;border:0;" border="0" src="images/cal.gif" width="20" height="18" ></a>
                                    </label>
                                    <script language="JavaScript" type="text/javascript">
                                        <!-- // create calendar object(s) just after form tag closed
                                         // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
                                         // note: you can have as many calendar objects as you need for your application
                                        var cal6 = new calendar3(document.forms['frmSearch'].elements['to_date']);
                                        cal6.year_scroll = true;
                                        cal6.time_comp = false;
                                        //-->
                                    </script>
                                </td>
                            </tr>
                            <tr>
                                 <td align="center" width="" style="padding-left:10px;" colspan="4">
                                    <input type="button" value="Download" class="inplogin" onclick="javascript:RdswiseDataDownload();">
                                    <!--input name="btnShowAll" type="button" class="inplogin" value="Show All" onClick="javascript:show_all();"--> 
                                </td>
                            </tr>
                		</table> 
                      </tr>
                      </form>
                     </table> 
                      
               		<br />
                <!-----------------------------------------End of Table for first time page loading---------------------------------------------!-->
				 <?php 
				// Displaying the result of search 
				if($_REQUEST['mode']=='data_download'){
					$branch_name=$_REQUEST['branch_name'];
					$rds_name=$_REQUEST['rds_name'];
					$from_date=$_REQUEST['from_date'];
					$from_date=date('Y-m-d',strtotime($from_date));
					$to_date=$_REQUEST['to_date'];
					$to_date=date('Y-m-d',strtotime($to_date));
					class ZipFile
					{
						/**
						 * Whether to echo zip as it's built or return as string from -> file
						 *
						 * @var  boolean  $doWrite
						 */
						var $doWrite      = false;
					
						/**
						 * Array to store compressed data
						 *
						 * @var  array    $datasec
						 */
						var $datasec      = array();
					
						/**
						 * Central directory
						 *
						 * @var  array    $ctrl_dir
						 */
						var $ctrl_dir     = array();
					
						/**
						 * End of central directory record
						 *
						 * @var  string   $eof_ctrl_dir
						 */
						var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";
					
						/**
						 * Last offset position
						 *
						 * @var  integer  $old_offset
						 */
						var $old_offset   = 0;
					
					
						/**
						 * Sets member variable this -> doWrite to true
						 * - Should be called immediately after class instantiantion
						 * - If set to true, then ZIP archive are echo'ed to STDOUT as each
						 *   file is added via this -> addfile(), and central directories are
						 *   echoed to STDOUT on final call to this -> file().  Also,
						 *   this -> file() returns an empty string so it is safe to issue a
						 *   "echo $zipfile;" command
						 *
						 * @access public
						 *
						 * @return void
						 */
						function setDoWrite()
						{
							$this -> doWrite = true;
						} // end of the 'setDoWrite()' method
					
						/**
						 * Converts an Unix timestamp to a four byte DOS date and time format (date
						 * in high two bytes, time in low two bytes allowing magnitude comparison).
						 *
						 * @param integer $unixtime the current Unix timestamp
						 *
						 * @return integer the current date in a four byte DOS format
						 *
						 * @access private
						 */
						function unix2DosTime($unixtime = 0)
						{
							$timearray = ($unixtime == 0) ? getdate() : getdate($unixtime);
					
							if ($timearray['year'] < 1980) {
								$timearray['year']    = 1980;
								$timearray['mon']     = 1;
								$timearray['mday']    = 1;
								$timearray['hours']   = 0;
								$timearray['minutes'] = 0;
								$timearray['seconds'] = 0;
							} // end if
					
							return (($timearray['year'] - 1980) << 25)
								| ($timearray['mon'] << 21)
								| ($timearray['mday'] << 16)
								| ($timearray['hours'] << 11)
								| ($timearray['minutes'] << 5)
								| ($timearray['seconds'] >> 1);
						} // end of the 'unix2DosTime()' method
					
					
						/**
						 * Adds "file" to archive
						 *
						 * @param string  $data file contents
						 * @param string  $name name of the file in the archive (may contains the path)
						 * @param integer $time the current timestamp
						 *
						 * @access public
						 *
						 * @return void
						 */
						function addFile($data, $name, $time = 0)
						{
							$name     = str_replace('\\', '/', $name);
					
							$dtime    = substr("00000000" . dechex($this->unix2DosTime($time)), -8);
							$hexdtime = '\x' . $dtime[6] . $dtime[7]
									  . '\x' . $dtime[4] . $dtime[5]
									  . '\x' . $dtime[2] . $dtime[3]
									  . '\x' . $dtime[0] . $dtime[1];
							eval('$hexdtime = "' . $hexdtime . '";');
					
							$fr   = "\x50\x4b\x03\x04";
							$fr   .= "\x14\x00";            // ver needed to extract
							$fr   .= "\x00\x00";            // gen purpose bit flag
							$fr   .= "\x08\x00";            // compression method
							$fr   .= $hexdtime;             // last mod time and date
					
							// "local file header" segment
							$unc_len = strlen($data);
							$crc     = crc32($data);
							$zdata   = gzcompress($data);
							$zdata   = substr(substr($zdata, 0, strlen($zdata) - 4), 2); // fix crc bug
							$c_len   = strlen($zdata);
							$fr      .= pack('V', $crc);             // crc32
							$fr      .= pack('V', $c_len);           // compressed filesize
							$fr      .= pack('V', $unc_len);         // uncompressed filesize
							$fr      .= pack('v', strlen($name));    // length of filename
							$fr      .= pack('v', 0);                // extra field length
							$fr      .= $name;
					
							// "file data" segment
							$fr .= $zdata;
					
							// echo this entry on the fly, ...
							if ( $this -> doWrite) {
								echo $fr;
							} else {                     // ... OR add this entry to array
								$this -> datasec[] = $fr;
							}
					
							// now add to central directory record
							$cdrec = "\x50\x4b\x01\x02";
							$cdrec .= "\x00\x00";                // version made by
							$cdrec .= "\x14\x00";                // version needed to extract
							$cdrec .= "\x00\x00";                // gen purpose bit flag
							$cdrec .= "\x08\x00";                // compression method
							$cdrec .= $hexdtime;                 // last mod time & date
							$cdrec .= pack('V', $crc);           // crc32
							$cdrec .= pack('V', $c_len);         // compressed filesize
							$cdrec .= pack('V', $unc_len);       // uncompressed filesize
							$cdrec .= pack('v', strlen($name)); // length of filename
							$cdrec .= pack('v', 0);             // extra field length
							$cdrec .= pack('v', 0);             // file comment length
							$cdrec .= pack('v', 0);             // disk number start
							$cdrec .= pack('v', 0);             // internal file attributes
							$cdrec .= pack('V', 32);            // external file attributes
																// - 'archive' bit set
					
							$cdrec .= pack('V', $this -> old_offset); // relative offset of local header
							$this -> old_offset += strlen($fr);
					
							$cdrec .= $name;
					
							// optional extra field, file comment goes here
							// save to central directory
							$this -> ctrl_dir[] = $cdrec;
						} // end of the 'addFile()' method
					
					
						/**
						 * Echo central dir if ->doWrite==true, else build string to return
						 *
					
						 * @return string  if ->doWrite {empty string} else the ZIP file contents
						 *
						 * @access public
						 */
						function file()
						{
							$ctrldir = implode('', $this -> ctrl_dir);
							$header = $ctrldir .
								$this -> eof_ctrl_dir .
								pack('v', sizeof($this -> ctrl_dir)) . //total #of entries "on this disk"
								pack('v', sizeof($this -> ctrl_dir)) . //total #of entries overall
								pack('V', strlen($ctrldir)) .          //size of central dir
								pack('V', $this -> old_offset) .       //offset to start of central dir
								"\x00\x00";                            //.zip file comment length
					
							if ( $this -> doWrite ) { // Send central directory & end ctrl dir to STDOUT
								echo $header;
								return "";            // Return empty string
							} else {                  // Return entire ZIP archive as string
								$data = implode('', $this -> datasec);
								return $data . $header;
							}
						} // end of the 'file()' method
					
					} // end of the 'ZipFile' class
					if($from_date!='1970-01-01' && $to_date!='1970-01-01')
					{
						$date_condition=" AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') >='".$from_date."' 
										AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') <='".$to_date."'";
						$date_condition_freight=" AND DATE_FORMAT(SUBSTRING(FE.freight_exp_trans_id,8,8),'%Y-%m-%d') >='".$from_date."' 
												AND DATE_FORMAT(SUBSTRING(FE.freight_exp_trans_id,8,8),'%Y-%m-%d') <='".$to_date."'";
					}
					else
					{
						$date_condition="";
						$date_condition_freight="";
					}
					if($rds_name=='all')
					{
						$condition=" AND BM.branch_code='".$branch_name."'";
					}
					else
					{
						$condition=" AND BM.branch_code='".$branch_name."' AND RM.rds_code='".$rds_name."'";
					}
				 //For Transaction csv download
					$arr_header = array('Branch name','Rds name','Employee Nme','Transaction Date','Transaction Id','Customer name','Sku name','Qty','Sale Rate',
					'Amount','VAT','Transaction type','Delivery instruction');
					$headerorder_header = "";
					foreach($arr_header AS $header_value)
					{ 
						$headerorder_header .= $header_value. ",";
					}
					if(modified_customer_emp_route == 'yes'){
						$sql_order_header_download = "SELECT DISTINCT BM.branch_name,RM.rds_name,EM.emp_name,EM.emp_code,
											DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') AS transaction_date,LO.trans_id,
											OD.order_no,CM.customer_name,PM.prod_desc, OD.qty, OD.sale_rate,(OD.qty*OD.sale_rate) AS Amount,
											OD.amount AS input_amount,OH.VAT,
											OH.transaction_type,OH.d_instruction,RM.rds_code FROM order_details OD,customer_master CM,product_master PM, 
											order_header OH,rds_master RM,branch_master BM,employee_master EM,location LO, emp_route_relation ERR 
											WHERE OH.customer_code = CM.customer_code 
											AND OD.sku_code = PM.prod_code and OD.order_no = OH.order_no AND SUBSTRING(OH.order_no,2,1)!='C' AND 
											CM.rds_tag=RM.rds_code AND FIND_IN_SET(BM.branch_code,CM.branch_code) AND ERR.route_code=CM.route_code AND SUBSTRING(OH.order_no,2,5)=EM.emp_code AND LO.trans_id=OH.order_no 
											".$condition.$date_condition;
					}
					else{
						$sql_order_header_download = "SELECT BM.branch_name,RM.rds_name,EM.emp_name,EM.emp_code,
											DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') AS transaction_date,LO.trans_id,
											OD.order_no,CM.customer_name,PM.prod_desc, OD.qty, OD.sale_rate,(OD.qty*OD.sale_rate) AS Amount,
											OD.amount AS input_amount,OH.VAT,
											OH.transaction_type,OH.d_instruction,RM.rds_code FROM order_details OD,customer_master CM,product_master PM, 
											order_header OH,rds_master RM,branch_master BM,employee_master EM,location LO 
											WHERE OH.customer_code = CM.customer_code 
											AND OD.sku_code = PM.prod_code and OD.order_no = OH.order_no AND SUBSTRING(OH.order_no,2,1)!='C' AND 
											CM.rds_tag=RM.rds_code AND CM.branch_code=BM.branch_code AND CM.emp_code=EM.emp_code AND LO.trans_id=OH.order_no 
											".$condition.$date_condition;
					}
					
					$sql_order_header_download .= " UNION ALL
											SELECT BM.branch_name,RM.rds_name,EM.emp_name,EM.emp_code,DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') 
											AS transaction_date,LO.trans_id,OD.order_no, 
											VM.vendor_name AS customer_name,PM.prod_desc, OD.qty, OD.sale_rate,(OD.qty*OD.sale_rate) AS Amount,
											OD.amount AS input_amount,OH.VAT,
											OH.transaction_type, OH.d_instruction,RM.rds_code FROM order_details OD,vendor_master VM,product_master PM,
											order_header OH,
											rds_master RM,branch_master BM,employee_master EM,location LO WHERE OH.customer_code = VM.vendor_code 
											AND OD.sku_code = PM.prod_code AND OD.order_no = OH.order_no AND SUBSTRING(OH.order_no,2,1)!='C' AND 
											VM.rds_code=RM.rds_code AND VM.branch_code=BM.branch_code AND VM.emp_code=EM.emp_code AND LO.trans_id=OH.order_no 
											".$condition.$date_condition." 
																		UNION ALL
											SELECT  BM.branch_name,RM.rds_name,EM.emp_name,EM.emp_code,DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') 
											AS transaction_date,LO.trans_id,
											OD.order_no,RM.rds_name AS customer_name,PM.prod_desc, OD.qty, OD.sale_rate,(OD.qty*OD.sale_rate) AS Amount,
											OD.amount AS input_amount,OH.VAT,
											OH.transaction_type, OH.d_instruction,RM.rds_code FROM order_details OD,product_master PM,order_header OH,
											rds_master RM,branch_master BM,employee_master EM,location LO WHERE SUBSTRING(OH.order_no,2,5) = RM.emp_code 
											AND OH.transaction_type IN('ST','BT') AND OD.sku_code = PM.prod_code AND OD.order_no = OH.order_no 
											AND SUBSTRING(OH.order_no,2,1)!='C'  AND EM.branch_code=BM.branch_code AND RM.emp_code=EM.emp_code 
											AND LO.trans_id=OH.order_no ".$condition.$date_condition." 
											ORDER BY rds_name,DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d'),trans_id,transaction_type,prod_desc";
		//exit();
			$rs_order_header_download = mysql_query($sql_order_header_download) or die(mysql_error()." Error in order header download: ".$sql_order_header_download);
			$lineorderheader = '';
			$rds_code_array=array();
			$rds_name_array=array();
			$emp_code_array=array();
			while($rec_order_header_download = mysql_fetch_array($rs_order_header_download)) 
			{ 
				$branch_name=$rec_order_header_download['branch_name'];
				$rds_name=$rec_order_header_download['rds_name'];
				$emp_name=$rec_order_header_download['emp_name'];
				$transaction_date=$rec_order_header_download['transaction_date'];
				$order_no=$rec_order_header_download['order_no'];
				$sale_rate=$rec_order_header_download['sale_rate'];
				$customer_name=$rec_order_header_download['customer_name'];
				$customer_name=str_replace(',','',$customer_name);
				$prod_desc=$rec_order_header_download['prod_desc'];
				$qty=$rec_order_header_download['qty'];
				$Amount=$rec_order_header_download['Amount'];
				$input_amount=$rec_order_header_download['input_amount'];
				if($input_amount >0)
				{
					$Amount=$input_amount;
					//$sale_rate=0;
				}
				$VAT=$rec_order_header_download['VAT'];
				$transaction_type=$rec_order_header_download['transaction_type'];
				$d_instruction=$rec_order_header_download['d_instruction'];
				$d_instruction=preg_replace('/\s+/', '',$d_instruction);
				if($d_instruction=='')
				{
					$d_instruction='; ;';
				}
				$rds_code=$rec_order_header_download['rds_code'];
				$emp_code=$rec_order_header_download['emp_code'];
				if($transaction_type=='ST' || $transaction_type=='BT')
				{
					$sqlrdsname="SELECT RM.rds_name FROM rds_master RM,order_header OH WHERE OH.customer_code=RM.rds_code 
								AND OH.order_no='".$order_no."'";
					$rsrdsname=mysql_query($sqlrdsname) or die(mysql_error()." Error in select rds name: ".$sqlrdsname);
					$rowrdsname=mysql_fetch_array($rsrdsname);
					$customer_name=$rowrdsname['rds_name'];		
				}
				
				if(!in_array($rds_code,$rds_code_array))
				{
					array_push($rds_code_array,$rds_code);
					array_push($rds_name_array,$rds_name);
					array_push($emp_code_array,$emp_code);
				}
				
				${branch_name.$rds_code} =$branch_name;
				${rds_name.$rds_code} =$rds_name;
				${emp_name.$rds_code} =$emp_name;
				${transaction_date.$rds_code} =$transaction_date;
				${order_no.$rds_code} =$order_no;
				${sale_rate.$rds_code} =$sale_rate;
				${customer_name.$rds_code} =$customer_name;
				${prod_desc.$rds_code} =$prod_desc;
				${qty.$rds_code} =$qty;
				${Amount.$rds_code} =$Amount;
				${VATINPUT.$rds_code} =$VAT;
				${transaction_type.$rds_code} =$transaction_type;
				${d_instruction.$rds_code} =$d_instruction;
				//echo ${sale_rate.$rds_code};
		
				${valueorderheader.$rds_code} =${branch_name.$rds_code}.",";
				${valueorderheader.$rds_code} .=${rds_name.$rds_code}.",";
				${valueorderheader.$rds_code} .=${emp_name.$rds_code}.",";
				${valueorderheader.$rds_code} .=${transaction_date.$rds_code}.",";
				${valueorderheader.$rds_code} .=${order_no.$rds_code}.",";
				${valueorderheader.$rds_code} .=${customer_name.$rds_code}.",";
				${valueorderheader.$rds_code} .=${prod_desc.$rds_code}.",";
				${valueorderheader.$rds_code} .=${qty.$rds_code}.",";
				${valueorderheader.$rds_code} .=${sale_rate.$rds_code}.",";
				${valueorderheader.$rds_code} .=${Amount.$rds_code}.",";
				${valueorderheader.$rds_code} .=${VATINPUT.$rds_code}.",";
				${valueorderheader.$rds_code} .=${transaction_type.$rds_code}.",";
				${valueorderheader.$rds_code} .=${d_instruction.$rds_code}.",";
				
				${lineorderheader.$rds_code}  .= ${valueorderheader.$rds_code}."\r\n"; 
			}
			//print_r($rds_code_array);
			//$rds_code='C/0000291';
			//echo ${lineorderheader.$rds_code};
			
			for($k=0;$k<count($emp_code_array);$k++)
			{
				$sql_query_order_header_SA="SELECT BM.branch_name,RM.rds_name,EM.emp_name,DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') 
										AS transaction_date,LO.trans_id,
										OD.order_no,PM.prod_desc, OD.qty, OD.sale_rate,(OD.qty*OD.sale_rate) AS Amount,
										OD.amount AS input_amount,OH.VAT,
										OH.transaction_type, OH.d_instruction,RM.rds_code from order_details OD,product_master PM,order_header OH,
										rds_master RM,branch_master BM,employee_master EM,location LO 
										where SUBSTRING(OH.order_no,2,5) = '".$emp_code_array[$k]."'
										AND SUBSTRING(OH.order_no,2,5)=EM.emp_code AND OH.transaction_type IN('SA','SH')
										AND OD.sku_code = PM.prod_code and OD.order_no = OH.order_no 
										AND EM.branch_code=BM.branch_code AND RM.emp_code=EM.emp_code 
										AND LO.trans_id=OH.order_no ".$date_condition." ORDER BY DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d'),trans_id";
				$rs_order_header_SA = mysql_query($sql_query_order_header_SA) or die(mysql_error()." Error in order header SA download: ".$sql_order_header_download);
							
				while($rec_order_header_SA = mysql_fetch_array($rs_order_header_SA)) 
				{ 
					$branch_name_SA=$rec_order_header_SA['branch_name'];
					$rds_name_SA=$rec_order_header_SA['rds_name'];
					$emp_name_SA=$rec_order_header_SA['emp_name'];
					$transaction_date_SA=$rec_order_header_SA['transaction_date'];
					$order_no_SA=$rec_order_header_SA['order_no'];
					$sale_rate_SA=$rec_order_header_SA['sale_rate'];
					$customer_name_SA='';
					$prod_desc_SA=$rec_order_header_SA['prod_desc'];
					$qty_SA=$rec_order_header_SA['qty'];
					$Amount_SA=$rec_order_header_SA['Amount'];
					$input_amount_SA=$rec_order_header_SA['input_amount'];
					if($input_amount_SA >0)
					{
						$Amount_SA=$input_amount_SA;
						$sale_rate_SA=0;
					}
					$VAT_SA=$rec_order_header_SA['VAT'];
					$transaction_type_SA=$rec_order_header_SA['transaction_type'];
					$d_instruction_SA=$rec_order_header_SA['d_instruction'];
					if($d_instruction_SA=='')
					{
						$d_instruction_SA='; ;';
					}
					$rds_code_SA=$rec_order_header_SA['rds_code'];
					
					${valueorderheader_SA.$rds_code_SA} =$branch_name_SA.",";
					${valueorderheader_SA.$rds_code_SA} .=$rds_name_SA.",";
					${valueorderheader_SA.$rds_code_SA} .=$emp_name_SA.",";
					${valueorderheader_SA.$rds_code_SA} .=$transaction_date_SA.",";
					${valueorderheader_SA.$rds_code_SA} .=$order_no_SA.",";
					${valueorderheader_SA.$rds_code_SA} .=$customer_name_SA.",";
					${valueorderheader_SA.$rds_code_SA} .=$prod_desc_SA.",";
					${valueorderheader_SA.$rds_code_SA} .=$qty_SA.",";
					${valueorderheader_SA.$rds_code_SA} .=$sale_rate_SA.",";
					${valueorderheader_SA.$rds_code_SA} .=$Amount_SA.",";
					${valueorderheader_SA.$rds_code_SA} .=$VAT_SA.",";
					${valueorderheader_SA.$rds_code_SA} .=$transaction_type_SA.",";
					${valueorderheader_SA.$rds_code_SA} .=$d_instruction_SA.",";
					
					${lineorderheader_SA.$rds_code_SA}  .= ${valueorderheader_SA.$rds_code_SA}."\r\n"; 
				}
				$sql_query_freight="SELECT BM.branch_name,RM.rds_name,EM.emp_name,DATE_FORMAT(FE.date,'%d-%m-%Y') AS transaction_date,FE.date,
									FE.freight_exp_trans_id,FE.amount,FE.trans_type,
									FE.remarks,RM.rds_code from freight_expenses FE,rds_master RM,branch_master BM,employee_master EM 
									where SUBSTRING(FE.freight_exp_trans_id,3,5) = '".$emp_code_array[$k]."' AND 
									SUBSTRING(FE.freight_exp_trans_id,3,5)=EM.emp_code 
									AND EM.branch_code=BM.branch_code AND RM.emp_code=EM.emp_code ".$date_condition_freight." ORDER BY DATE_FORMAT(SUBSTRING(FE.date,8,8),'%Y-%m-%d'),freight_exp_trans_id";
				$rs_query_freight = mysql_query($sql_query_freight) or die(mysql_error()." Error in freight download: ".$sql_query_freight);
							
				while($rec_query_freight = mysql_fetch_array($rs_query_freight)) 
				{ 
					$branch_name_freight=$rec_query_freight['branch_name'];
					$rds_name_freight=$rec_query_freight['rds_name'];
					$emp_name_freight=$rec_query_freight['emp_name'];
					$transaction_date_freight=$rec_query_freight['transaction_date'];
					$transaction_date_freight=str_replace('/','_',$transaction_date_freight);
					$order_no_freight=$rec_query_freight['freight_exp_trans_id'];
					$sale_rate_freight='';
					$customer_name_freight='';
					$prod_desc_freight='';
					$qty_freight='';
					$Amount_freight=$rec_query_freight['amount'];
					$VAT_freight='';
					$transaction_type_freight=$rec_query_freight['trans_type'];
					$d_instruction_freight=$rec_query_freight['remarks'];
					if($d_instruction_freight=='')
					{
						$d_instruction_freight='; ;';
					}
					$rds_code_freight=$rec_query_freight['rds_code'];
					
					${valueorderheader_freight.$rds_code_freight} =$branch_name_freight.",";
					${valueorderheader_freight.$rds_code_freight} .=$rds_name_freight.",";
					${valueorderheader_freight.$rds_code_freight} .=$emp_name_freight.",";
					${valueorderheader_freight.$rds_code_freight} .=$transaction_date_freight.",";
					${valueorderheader_freight.$rds_code_freight} .=$order_no_freight.",";
					${valueorderheader_freight.$rds_code_freight} .=$customer_name_freight.",";
					${valueorderheader_freight.$rds_code_freight} .=$prod_desc_freight.",";
					${valueorderheader_freight.$rds_code_freight} .=$qty_freight.",";
					${valueorderheader_freight.$rds_code_freight} .=$sale_rate_freight.",";
					${valueorderheader_freight.$rds_code_freight} .=$Amount_freight.",";
					${valueorderheader_freight.$rds_code_freight} .=$VAT_freight.",";
					${valueorderheader_freight.$rds_code_freight} .=$transaction_type_freight.",";
					${valueorderheader_freight.$rds_code_freight} .=$d_instruction_freight.",";
					
					${lineorderheader_freight.$rds_code_freight}  .= ${valueorderheader_freight.$rds_code_freight}."\r\n"; 
				}
	
			}
			$zip = new ZipFile();
			for($i=0;$i<count($rds_code_array);$i++)
			{
				//$dataorderheader = str_replace("\r\n","",$lineorderheader);
				${dataorderheader.$rds_code_array[$i]} = ${lineorderheader.$rds_code_array[$i]}.${lineorderheader_SA.$rds_code_array[$i]}.${lineorderheader_freight.$rds_code_array[$i]} ;
				//${dataorderheader.$rds_code_array[$i]} = ${lineorderheader.$rds_code_array[$i]};
				//${dataorderheader.$rds_code_array[$i]} = ${lineorderheader.$rds_code_array[$i]}.${lineorderheader_freight.$rds_code_array[$i]} ;
					
				if (${dataorderheader.$rds_code_array[$i]} == "")
				{ 
					${dataorderheader.$rds_code_array[$i]} = "\r\n(0) Records Found!\n";                         
				} 
				
					$date=gmdate('d',strtotime('+330 minute'));
					$month=gmdate('m',strtotime('+330 minute'));
					$year=gmdate('Y',strtotime('+330 minute'));
					$hour=gmdate('H',strtotime('+330 minute'));
					$minute=gmdate('i',strtotime('+330 minute'));
					$second=gmdate('s',strtotime('+330 minute'));
		
				${file_content.$rds_code_array[$i]}= "$headerorder_header\r\n".${dataorderheader.$rds_code_array[$i]};
				${file_name.$rds_code_array[$i]}="$rds_name_array[$i]_$date$month$year$hour$minute$second.csv";
				
				//add files to the zip, passing file contents, not actual files
				//$zip->addFile($file_content1, $file_name1);
				$zip->addFile(${file_content.$rds_code_array[$i]}, ${file_name.$rds_code_array[$i]});
			}
			
			//$nick_name=$_SESSION['nick_name'];
			
			//prepare the proper content type
			$successval=1;
			if($successval!=1){
				mysql_query("ROLLBACK");
			}
			else
			{
				echo 'SUCCESS';
			}
			header("Content-type: application/octet-stream");
			header("Content-Disposition: inline; filename=csv_files_$branch_name.zip");
			echo $zip->file();
			exit();
		}
	 }//End of main()?>