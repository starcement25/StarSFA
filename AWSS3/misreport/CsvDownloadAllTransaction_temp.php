<?php
	ob_start();
	session_start();
	require("adminUtils.php");
	//if($_SESSION['admin_login']=="")  		header("location:index.php");
	ob_end_flush();
	$mode = $_REQUEST['mode'];

	if($mode =='csv_download')		csvDownload();
	else  disphtml("main();");
ob_end_flush();

function main()
{
	if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' AND emp_code IN('.$emp_hierarchy.')';
	}
?>
<script language="javascript">
function check()
{
	if (document.frmSearch.emp_name.value==0) 
	{
		alert('Please select a employee.');
		document.frmSearch.emp_name.focus();
		return false;
	}
	if(document.frmSearch.from_date.value.search(/\S/)==0)
	{
		if(document.frmSearch.to_date.value.search(/\S/)==-1)
		{
			alert('Please input a vlaue for To Date.');
			document.frmSearch.to_date.focus();
			return false;
		}
	}
	return true;
}
</script>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>Download Transaction CSV</strong></td>
	</tr>
    
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
                <table width="80%" align="center" border="0" cellpadding="5" cellspacing="1" >
                    <tr> 
                        <td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);?></td>
                        <td align="right" colspan="2"></td>
                    </tr>
                </table>
               
                <!--------------------------------Start Table for first time page loading---------------------------------!-->
                
                <table width="65%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
                	<form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="javascript:return check();">
					<input type="hidden" name="mode" value="csv_download">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="15%" align="center"></td>
                        <table width="65%" align="center" border="0" cellpadding="5" cellspacing="1"  class="border">
                         <tr>
                                <td align="right" width="45%" colspan="2">Employee:</td>
                                <td align="left" width="" style="vertical-align:top;" colspan="2">
                                   <?php $emp_name=$_REQUEST['emp_name'];?>
                                    <select name="emp_name" id="emp_name" >
                                    <option value="0">SELECT</option>
                                    <?php if($_SESSION['admin_login']=="admin"){?>
                                    <option value="all">ALL</option>
                                     <?php
									}
                                     $sqlqueryemp="SELECT emp_code,emp_name FROM employee_master WHERE 1 ".$emp_hierarchy_condition." ORDER BY emp_name ASC";
                                     $resultqueryemp = mysql_query($sqlqueryemp);
                                     $count=mysql_num_rows($resultqueryemp);
                                     $cnt=1;
                                        if($count>0){
                                        while($rowqueryemp = mysql_fetch_array($resultqueryemp))
                                        {
                                      ?>
                                         <option value="<?php echo $rowqueryemp['emp_code'];?>" <?php if($emp_name==$rowqueryemp['emp_name'] || 
                                         $_REQUEST['emp_code']==$rowqueryemp['emp_code']){echo 'selected';}?>><?php echo $rowqueryemp['emp_name'];?></option>
                                       <?php
                                        }
                                      }
                                        ?>	
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
                                    <input type="submit" value="Submit" class="inplogin" onclick="javascript:showRdswisesalesDeatails();">
                                    <!--input name="btnShowAll" type="button" class="inplogin" value="Show All" onClick="javascript:show_all();"--> 
                                </td>
                            </tr>
                		</table> 
                      </tr>
                      </form>
                     </table> 
                      
               		<br />
<?php }//End of main()
function csvDownload()
{
	$emp_name=$_REQUEST['emp_name'];
	$from_date=$_REQUEST['from_date'];
	$to_date=$_REQUEST['to_date'];
	$from_date=date('Y-m-d',strtotime($from_date));
	$to_date=date('Y-m-d',strtotime($to_date));
	if($from_date!='' && $to_date!='')
	{
		 $date_condition=" AND DATE_FORMAT(LO.date,'%Y-%m-%d') >='".$from_date."' AND 
					  	DATE_FORMAT(LO.date,'%Y-%m-%d') <='".$to_date."'";
	}
	if($emp_name!='all')
	{
		$emp_condition=" AND LO.emp_code='".$emp_name."'";
	}
	else
	{
		$emp_condition='';
	}
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

 //For order header csv download
	$arr_header = array('Transaction Id','Employee name','WS Name','Route Name','Customer name','Order date','Category','Sku name','Qty','Sale Rate/Mrp','Order Value','Remarks');
	$headerorder_header = "";
	foreach($arr_header AS $header_value)
	{
		$headerorder_header .= $header_value. ",";
	}
	if(TD=='yes' && TD_type=='order value wise')
	{
		$TD_condition=",OH.TD";
	}
	else if(TD=='yes' && TD_type=='sku wise')
	{
		$TD_condition=",OD.TD";
	}
	else if(TD=='no')
	{
		$TD_condition="";
	}
		
	echo $sql_order_header_download = "SELECT OH.order_no,OH.customer_code,OH.d_instruction,OH.sale_type,LO.emp_code,OH.tag_distributor_code,
								DATE_FORMAT(LO.date,'%d/%m/%Y') AS order_date,OD.sku_code,OD.qty,OD.amount,PM.prod_desc,PM.product_group_code,
								OD.sale_rate".$TD_condition."
								FROM order_header OH,location LO,order_details OD,product_master PM
								WHERE LO.trans_id=OH.order_no AND OH.order_no=OD.order_no 
								AND (LO.trans_id LIKE 'O%' OR LO.trans_id LIKE 'NO%') AND LO.emp_code!='C0007' AND OD.sku_code=PM.prod_code
								 ".$emp_condition.$date_condition." 
								ORDER BY DATE_FORMAT(LO.date,'%Y-%m-%d'),LO.trans_id DESC,PM.prod_desc ASC";die;

	$rs_order_header_download = mysql_query($sql_order_header_download) or die(mysql_error()." Error in order header download: ".$sql_order_header_download);
	$lineorderheader = ''; 
	while($rec_order_header_download = mysql_fetch_array($rs_order_header_download)) 
	{ 
		$customer_code=$rec_order_header_download['customer_code'];
		$emp_code=$rec_order_header_download['emp_code'];
		$order_date=$rec_order_header_download['order_date'];
		$sku_code=$rec_order_header_download['sku_code'];
		$product_group_code=$rec_order_header_download['product_group_code'];
		$qty=$rec_order_header_download['qty'];
		$sale_rate=$rec_order_header_download['sale_rate'];
		$order_no=$rec_order_header_download['order_no'];
		$d_instruction=preg_replace('/\s+/', ' ',$rec_order_header_download['d_instruction']);
		$sale_type=$rec_order_header_download['sale_type'];
		$tag_distributor_code=$rec_order_header_download['tag_distributor_code'];
		if($sale_type=='CASH')	$sale_type_value='CS';
		if($sale_type=='CREDIT')	$sale_type_value='CR';
		if($sale_type=='COD')	$sale_type_value='COD';	
		
		$amount=$rec_order_header_download['amount'];
		/*if(substr($customer_code,0,1)=='N')
		{
			$sqlcustomername="SELECT customer_name FROM prospective_customer_master WHERE customer_code='".$customer_code."'";
			$rscustomername=mysql_query($sqlcustomername);
			$rowcustomername=mysql_fetch_array($rscustomername);
			$customer_name=$rowcustomername['customer_name'];
		}
		else
		{*/
			$sqlcustomername="SELECT customer_name,TD,route_code FROM customer_master WHERE customer_code='".$customer_code."'";
			$rscustomername=mysql_query($sqlcustomername);
			$rowcustomername=mysql_fetch_array($rscustomername);
			$customer_name=str_replace(",","",$rowcustomername['customer_name']);
			$route_code=$rowcustomername['route_code'];
		//}
			$sqldistributorname="SELECT customer_name FROM customer_master WHERE customer_code='".$tag_distributor_code."'";
			$rsdistributorname=mysql_query($sqldistributorname);
			$rowdistributorname=mysql_fetch_array($rsdistributorname);
			$distributor_name=str_replace(",","",$rowdistributorname['customer_name']);
			
			$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route_code."'";
			$rsroutename=mysql_query($sqlroutename);
			$rowroutename=mysql_fetch_array($rsroutename);
			$route_name=$rowroutename['route_name'];
			
			if(strtoupper($_SESSION['nick_name']) == 'PARLE'){
				$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route_code."'";
				$rsroutename=mysql_query($sqlroutename);
				$total_row_check = mysql_num_rows($rsroutename);
				if($total_row_check>0){
					$rsroutename=mysql_query($sqlroutename);
					$rowroutename=mysql_fetch_array($rsroutename);
					$route_name=$rowroutename['route_name'];
				}
				else{
					$sql_route_code = "SELECT route_code FROM customer_master WHERE customer_code = '".$tag_distributor_code."'";
					$res_route_code = mysql_query($sql_route_code);
					$row_route_code = mysql_fetch_array($res_route_code);
					$route_code = $row_route_code['route_code'];
					
					$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route_code."'";
					$rsroutename=mysql_query($sqlroutename);
					$rowroutename=mysql_fetch_array($rsroutename);
					$route_name=$rowroutename['route_name'];
				}
			}
			
			$sqlproductgroup="SELECT product_group_name FROM product_group_master WHERE product_group_code='".$product_group_code."'";
			$rsproductgroup=mysql_query($sqlproductgroup);
			$rowproductgroup=mysql_fetch_array($rsproductgroup);
			$product_group_name=$rowproductgroup['product_group_name'];

		if(TD=='yes' && TD_type=='customer wise')
		{
			$TD_value=$rowcustomername['TD'];
		}
		else if(TD=='yes' && (TD_type=='order value wise' || TD_type=='sku wise'))
		{
			$TD_value=$rec_order_header_download['TD'];
		}
		else
		{
			$TD_value='';
		}
		
		$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
		$rsempname=mysql_query($sqlempname);
		$rowempname=mysql_fetch_array($rsempname);
		$emp_name=$rowempname['emp_name'];
		
		/*$sqlproductdetails="SELECT prod_desc FROM product_master WHERE prod_code='".$sku_code."'";
		$rsproductdetails=mysql_query($sqlproductdetails);
		$rowproductdetails=mysql_fetch_array($rsproductdetails);*/
		$prod_desc=$rec_order_header_download['prod_desc'];
		
		$valueorderheader  = $order_no.",";
		$valueorderheader .= $emp_name.",";
		$valueorderheader .= $distributor_name.",";
		$valueorderheader .= $route_name.",";
		$valueorderheader .= $customer_name.",";
		$valueorderheader .= $order_date.",";
		if(substr($order_no,0,1)=='O')
		{
			$valueorderheader .= $product_group_name.",";
			$valueorderheader .= $prod_desc.",";
			$valueorderheader .= $qty.",";
			$valueorderheader .= $sale_rate.",";
			$valueorderheader .= $amount.",";
		}
		else
		{
			$valueorderheader .= ''.",";
			$valueorderheader .= 'NO SKU'.",";
			$valueorderheader .= '0'.",";
			$valueorderheader .= '0'.",";
			$valueorderheader .= '0'.",";
		}
		$valueorderheader .= $TD_value.';'.$sale_type_value.';'.$d_instruction.",";
		//$valueorderheader .= stripslashes($rec_order_header_download['one_time_password']).",";
		//$valueorderheader .= stripslashes($rec_order_header_download['is_confirmed']).",";

		$lineorderheader  .= $valueorderheader."\r\n"; 
		/*$sqlupdateorderheader="UPDATE order_header SET dwnld_transferred='YES' WHERE order_no='".$order_no."'";
		$rsupdateorderheader=mysql_query($sqlupdateorderheader)or die(mysql_error()." Error in order header update: ".$sqlupdateorderheader);*/
	}
	//$dataorderheader = str_replace("\r\n","",$lineorderheader);
	$dataorderheader = $lineorderheader;	
	if ($dataorderheader == "")
	{ 
		$dataorderheader = "\r\n(0) Records Found!\n";                         
    } 
	
	//For payment header csv download
	
	$arr_header = array('Transaction Id','Employee name','Customer name','Payment date','Cash_cheque','Cheque no','Cheque date','Bank','Invoice id','Amount','Remark');
	$headerpayment_header = "";
	foreach($arr_header AS $header_value)
	{
		$headerpayment_header .= $header_value. ",";
	}
	$sql_payment_header_download = "SELECT LO.emp_code,PH.receipt_id,PH.customer_code,DATE_FORMAT(LO.date,'%d/%m/%Y') AS date,
									PH.cash_cheque,PH.cheque_no,DATE_FORMAT(PH.date,'%d/%m/%Y') AS cheque_date,PH.bank,PD.invoice_id,PD.amount,PH.p_remark
									FROM payment_header PH,location LO,payment_details PD WHERE LO.trans_id=PH.receipt_id 
									AND (LO.trans_id LIKE 'P%' OR LO.trans_id LIKE 'NC%')AND PH.receipt_id=PD.receipt_id 
									AND LO.emp_code!='C0007' ".$emp_condition.$date_condition." 
									ORDER BY DATE_FORMAT(LO.date,'%Y-%m-%d') DESC";
	$rs_payment_header_download = mysql_query($sql_payment_header_download) or die(mysql_error()." Error in payment header download: ".$sql_payment_header_download);
	$linepaymentheader = ''; 
	while($rec_payment_header_download = mysql_fetch_array($rs_payment_header_download)) 
	{ 
		$customer_code=$rec_payment_header_download['customer_code'];
		$emp_code=$rec_payment_header_download['emp_code'];
		$receipt_id=$rec_payment_header_download['receipt_id'];

		/*if(substr($customer_code,0,1)=='N')
		{
			$sqlcustomername="SELECT customer_name FROM prospective_customer_master WHERE customer_code='".$customer_code."'";
			$rscustomername=mysql_query($sqlcustomername);
			$rowcustomername=mysql_fetch_array($rscustomername);
			$customer_name=$rowcustomername['customer_name'];
		}
		else
		{*/
			$sqlcustomername="SELECT customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
			$rscustomername=mysql_query($sqlcustomername);
			$rowcustomername=mysql_fetch_array($rscustomername);
			$customer_name=str_replace(",","",$rowcustomername['customer_name']);
		//}
		
		$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
		$rsempname=mysql_query($sqlempname);
		$rowempname=mysql_fetch_array($rsempname);
		$emp_name=$rowempname['emp_name'];
		
		$valuepaymentheader = $receipt_id.",";
		$valuepaymentheader .= $emp_name.",";
		$valuepaymentheader .= $customer_name.",";
		if(substr($receipt_id,0,1)=='P')
		{
			$cheque_date=$rec_payment_header_download['cheque_date'];
			if($cheque_date=='00/00/0000')
			{
				$cheque_date='';
			}
			$valuepaymentheader .= stripslashes($rec_payment_header_download['date']).",";
			$valuepaymentheader .= stripslashes($rec_payment_header_download['cash_cheque']).",";
			$valuepaymentheader .= stripslashes($rec_payment_header_download['cheque_no']).",";
			$valuepaymentheader .= $cheque_date.",";
			$valuepaymentheader .= stripslashes($rec_payment_header_download['bank']).",";
			$valuepaymentheader .= stripslashes($rec_payment_header_download['invoice_id']).",";
			$valuepaymentheader .= stripslashes($rec_payment_header_download['amount']).",";
		}
		else
		{
			$valuepaymentheader .= stripslashes($rec_payment_header_download['date']).",";
			$valuepaymentheader .= ''.",";
			$valuepaymentheader .= ''.",";
			$valuepaymentheader .= ''.",";
			$valuepaymentheader .= ''.",";
			$valuepaymentheader .= ''.",";
			$valuepaymentheader .= 'No Amount'.",";
		}
		$p_remark=str_replace(',','',$rec_payment_header_download['p_remark']);
		$p_remark=preg_replace('/\s+/', ' ',$p_remark);
		$valuepaymentheader .= stripslashes($p_remark).",";

		$linepaymentheader  .= $valuepaymentheader."\r\n"; 
		/*$sqlupdatepaymentheader="UPDATE payment_header SET dwnld_transferred='YES' WHERE receipt_id='".$receipt_id."'";
		$rsupdatepaymentheader=mysql_query($sqlupdatepaymentheader)or die(mysql_error()." Error in payment header update: ".$sqlupdatepaymentheader);*/
	} 

	//$datapaymentheader = str_replace("\r\n","",$linepaymentheader);
	$datapaymentheader = $linepaymentheader;		
	if ($datapaymentheader == "")
	{ 
		$datapaymentheader = "\r\n(0) Records Found!\n";                         
    } 
	
	//For Prospective customer csv download
	
	$arr_header = array('Transaction Id','Employee name','Customer name','Address','Pin','Phone no','Visit Date','Sku name','Remarks');
	$headerprospect = "";
	foreach($arr_header AS $header_value)
	{
		$headerprospect .= $header_value. ",";
	}
	$sql_prospect_download = "SELECT LO.emp_code,PCH.trans_id,PCH.customer_name,DATE_FORMAT(LO.date,'%d/%m/%Y') AS date,
							PCH.address,PCH.pin,PCH.phone_no,PCD.product_code,PCH.remarks
							FROM prospective_customer_header PCH,location LO,prospective_customer_details PCD 
							WHERE LO.trans_id=PCH.trans_id AND PCH.trans_id=PCD.trans_id AND LO.trans_id LIKE 'D%'
								AND LO.emp_code!='C0007' ".$emp_condition.$date_condition." ORDER BY DATE_FORMAT(LO.date,'%Y-%m-%d') DESC";
	$rs_prospect_download = mysql_query($sql_prospect_download) or die(mysql_error()." Error in prospect download: ".$sql_prospect_download);
	$lineprospect = ''; 
	while($rec_prospect_download = mysql_fetch_array($rs_prospect_download)) 
	{ 
		$emp_code=$rec_prospect_download['emp_code'];
		$sku_code=$rec_prospect_download['product_code'];
		$trans_id=$rec_prospect_download['trans_id'];
		$remarks=preg_replace('/\s+/', ' ',$remarks);

		$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
		$rsempname=mysql_query($sqlempname);
		$rowempname=mysql_fetch_array($rsempname);
		$emp_name=$rowempname['emp_name'];
		
		$sqlproductdetails="SELECT prod_desc FROM product_master WHERE prod_code='".$sku_code."'";
		$rsproductdetails=mysql_query($sqlproductdetails);
		$rowproductdetails=mysql_fetch_array($rsproductdetails);
		$prod_desc=$rowproductdetails['prod_desc'];
	

		$address=$rec_retail_download['address'];
		$address=str_replace(',','|',$address);
		$valueprospect  = stripslashes($trans_id).",";
		$valueprospect .= stripslashes($emp_name).",";
		$valueprospect .= stripslashes(str_replace(",","",$rec_prospect_download['customer_name'])).",";
		$valueprospect .= stripslashes($address).",";
		$valueprospect .= stripslashes($rec_prospect_download['pin']).",";
		$valueprospect .= stripslashes($rec_prospect_download['phone_no']).",";
		$valueprospect .= stripslashes($rec_prospect_download['date']).",";
		$valueprospect .= stripslashes($prod_desc).",";
		$valueprospect .= stripslashes($remarks).",";

		$lineprospect  .= $valueprospect."\r\n"; 
		/*$sqlupdateprospectheader="UPDATE prospective_customer_header SET dwnld_transferred='YES' WHERE  trans_id ='".$trans_id."'";
		$rsupdateprospectheader=mysql_query($sqlupdateprospectheader)or die(mysql_error()." Error in prospect header update: ".$sqlupdateprospectheader);*/

	} 

	//$dataprospect = str_replace("\r\n","",$lineprospect);	
	$dataprospect = $lineprospect;		
	if ($dataprospect == "")
	{ 
		$dataprospect = "\r\n(0) Records Found!\n";                         
    } 
	
	$file_content1= "$headerorder_header\r\n$dataorderheader";
	$file_name1="Order Details.csv";
	$file_content2= "$headerpayment_header\r\n$datapaymentheader";
	$file_name2="Payment Details.csv";
	$file_content3= "$headerprospect\r\n$dataprospect";
	$file_name3="Business Prospect Details.csv";
	
	$zip = new ZipFile();
	
	//add files to the zip, passing file contents, not actual files
	//$zip->addFile($file_content1, $file_name1);
	$zip->addFile($file_content1, $file_name1);
	$zip->addFile($file_content2, $file_name2);
	$zip->addFile($file_content3, $file_name3);
	
	//$nick_name=$_SESSION['nick_name'];
	//$nick_name=$_REQUEST['nick_name'];
	//$fullPath = "/home/acedns/public_html/acednsproduct/csvdownload/$nick_name/csv_files.zip";
	
	//prepare the proper content type
	header("Content-type: application/octet-stream");
	header("Content-Disposition: inline; filename=csv_files.zip");
	echo $zip->file();
	exit();
	$folderName = $nick_name;
	if ( !file_exists("../csvdownload/$folderName")){
		mkdir("../csvdownload/$folderName");
		chmod("../csvdownload/$folderName", 0777);
	}
	
	//header("Content-type: application/octet-stream");
	//header('Content-Disposition: attachment; filename="' . basename($fullPath) . '"');
	
	/*$path = $zip->file();
	$url = 'http://www.coralindia.com/dev/acednsproduct/csvdownload/$nick_name/csv_files.zip';
	$newfname = $path;
	$file = fopen ($path, "rb");
	if($file) {
		$newf = fopen ($url, "wb");
		if($newf)
			while(!feof($file)) {
				fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );
			}
	}
	if($file) {
		fclose($newf);
	}
	$url = 'http://www.coralindia.com/dev/acednsproduct/csvdownload/$nick_name/csv_files.zip';
	$file = fopen ($url, "wb");
	fwrite($file, fread($zip->file(), 1024 * 8 ), 1024 * 8 );
	fclose($file);*/
	$contents=$zip->file();
	$destination = "../csvdownload/$folderName/csv_files.zip";
	$file = fopen($destination, "w+");
	fputs($file, $contents);
	fclose($file);
	//echo $zip->file();
	//echo $zip->file();

	
	$successval=1;
	if($successval!=1){
		mysql_query("ROLLBACK");
	}
}
?>	