<?php
	ob_start();
	session_start();
	if(strtoupper($_SESSION['admin_login'])=='ADMIN' ||strtoupper($_SESSION['admin_login'])=='SUPERVISOR' ||strtoupper($_SESSION['admin_login'])=='E0042'){
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	ob_end_flush();
	$mode = $_REQUEST['mode'];

	if($mode =='csv_download')		csvDownload();
	else  disphtml("main();");
ob_end_flush();

function main()
{
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
                        	  <tr >
                                <td align="left" width="15%">State:</td>
                                <td align="left" width="30%" style="vertical-align:top;">
                                	<?php
									$sql_state = "SELECT DISTINCT SUBSTRING_INDEX(state, ',', 1) AS state FROM
												employee_master WHERE acedns='Y' ORDER BY state ASC";
									$res_state = mysql_query($sql_state);
									$state_total = mysql_num_rows($res_state);
										$state_select_control = "<select name=\"state\" id=\"state\">";
										$state_select_control .= "<option value=\"\">Select</option>";

										$res_state = mysql_query($sql_state);
										while($row_state = mysql_fetch_array($res_state)){
											$state = $row_state['state'];
											$state_string .= "'".$state."',";
											$state_select_control_option .= "<option value=\"'".$state."'\">".$state."</option>";
										}
										$state_string = rtrim($state_string,",");
										$state_select_control .= "<option value=\"all\">All</option>";
										$state_select_control .= $state_select_control_option;
										$state_select_control .= "</select>";
										echo $table_data = $state_select_control;
									?>
                                </td>
                            </tr>
                            <tr id="datedropdown" >
                                <td align="left" width="15%">From Date:</td>
                                <td align="left" width="30%" style="vertical-align:top;">
                                		<?php $from_date=$_REQUEST['from_date'];?>
                                      <input id="textinput3" type="text" value="<?php echo str_replace('/','-',$from_date);?>" name="from_date"></input>&nbsp;
                                        <a href="javascript:cal5.popup();"><img style="cursor:hand;position:absolute;bsauda:0;" bsauda="0" src="images/cal.gif" width="20" height="18" ></a>
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
                                        <a href="javascript:cal6.popup();"><img style="cursor:hand;position:absolute;bsauda:0;" bsauda="0" src="images/cal.gif" width="20" height="18" ></a>
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
	$from_date=$_REQUEST['from_date'];
	$state=$_REQUEST['state'];
	if($state=='all')
	{
		$state_condition='';
	}
	else
	{
		$state_condition=" AND state=".$state;
	}
	if(strtoupper($_SESSION['admin_login'])=='ADMIN')
	{
		$vertical_condition="";
	}
	else
	{
		if(strtoupper($_SESSION['admin_login'])=='HBC') 		$vertical_condition=" AND vertical='HBC:Rasoi:BIB'";
		else if(strtoupper($_SESSION['admin_login'])=='SFATS') $vertical_condition=" AND vertical='Specialty Fats'";
		else													$vertical_condition=" AND vertical='".$_SESSION['vertical_value']."'";										
	}
	$to_date=$_REQUEST['to_date'];
	$from_date=date('Y-m-d',strtotime($from_date));
	$to_date=date('Y-m-d',strtotime($to_date));
	
	$url="http://salesmpower.acedns.in/sauda-download-log-preperation-latest.php?from_date=$from_date&to_date=$to_date";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_exec($ch);
	 //$response = curl_exec($ch); 
	if($from_date!='' && $to_date!='')
	{
		 $date_condition=" sauda_date >='".$from_date."' AND sauda_date <='".$to_date."'";
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

 //For sauda csv download
	$arr_header = array('Customer Code','Customer Name','Route Name','Broker Code','Broker Name','App Contract No','App Contract Date','App Contract Time','Contract Valid From',
		'Contract Valid To','Material Code','Material Qty(case)','Material Qty(MT)','UOM (case)','Product Group','Pack Size','Material Description','Depot Code','Depot Name','State','Material Cost','Primary Freight','Packing Cost','Honeycomb cost','Brokerage cost','Detention charges','Depot Cost','Margin Cost TP',
		'Margin Cost RA','Secondary Freight','TD','LTD','Premium','PR00','FRC1','Total Value','Incoterms',
		'Employee Name','Payment Due On','CM-Credit Limit','Remarks','Vertical','Realization Per case','Realization Per MT','Sale Rate','Actual Packing Cost','Plant Name','Sauda Type');
	$headersauda_header = "";
	foreach($arr_header AS $header_value)
	{
		$headersauda_header .= $header_value. ",";
	}

	$sql_sauda_header_download = "SELECT *,DATE_FORMAT(sauda_date,'%d/%m/%Y') AS sauda_date,DATE_FORMAT(contract_valid_from,'%d/%m/%Y') 
									AS sauda_valid_from FROM sauda_download_log WHERE 
								 ".$date_condition.$state_condition.$vertical_condition."  AND sauda_type <> 'RAA' ORDER BY DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') ASC,product_group_name,state  ASC";
	$rs_sauda_header_download = mysql_query($sql_sauda_header_download) or die(mysql_error()." Error in sauda data download: ".$sql_sauda_header_download);
	$linesaudaheader = '';
	while($rec_sauda_header_download = mysql_fetch_array($rs_sauda_header_download))
	{
		$customer_code=$rec_sauda_header_download['customer_code'];
		$customer_name=$rec_sauda_header_download['customer_name'];
		$route_name=$rec_sauda_header_download['route_name'];
		$broker_id=$rec_sauda_header_download['broker_id'];
		$product_group_code = $rec_sauda_header_download['product_group_code'];
		//$emp_code = $rec_sauda_header_download['emp_code'];
		$product_group_name = $rec_sauda_header_download['product_group_name'];
		$state = $rec_sauda_header_download['state'];
		$customer_code = $rec_sauda_header_download['customer_code'];
		$branch_code = $rec_sauda_header_download['branch_code'];
		$vertical_value = $rec_sauda_header_download['vertical'];
		$sauda_time = $rec_sauda_header_download['sauda_time'];
		$liquid_TD = $rec_sauda_header_download['liquid_TD'];

		$broker_name=$rec_sauda_header_download['broker_name'];
		$sauda_no=$rec_sauda_header_download['sauda_no'];
		$sauda_date=$rec_sauda_header_download['sauda_date'];
		if($rec_sauda_header_download['payment_due_on']=='0000-00-00'){
			$payment_due_on='0';
		}
		else{
		$payment_due_on=date('d/m/Y',strtotime($rec_sauda_header_download['payment_due_on']));
		}
		
		$sauda_valid_from=date('d/m/Y',strtotime($rec_sauda_header_download['contract_valid_from']));
		$valid_upto = date('d/m/Y',strtotime($rec_sauda_header_download['contract_valid_to']));
		$dns_prod_code=$rec_sauda_header_download['prod_code'];
		//$emp_code=$rec_sauda_header_download['emp_code'];
		$qty=$rec_sauda_header_download['qty'];
		$qty=round($qty,2);
		$convert_qty_two=$rec_sauda_header_download['convert_qty_two'];
		$UOM=$rec_sauda_header_download['UOM'];
		$prod_desc=$rec_sauda_header_download['prod_desc'];
		$dns_branch_code=$rec_sauda_header_download['dns_branch_code'];
		$branch_name=$rec_sauda_header_download['branch_name'];
		$sale_rate=$rec_sauda_header_download['sale_rate'];
		$freight_charge=$rec_sauda_header_download['freight_charge'];
		$TD=$rec_sauda_header_download['TD'];
		$premium=$rec_sauda_header_download['premium'];
		if($premium=='')  $premium=0;
		$employee_name=$rec_sauda_header_download['emp_name'];
		if($freight_charge=='')  $freight_charge=0;
		if(strpos($branch_code,'RP')!=false){
			$incoterms='EXR';
		}
		else{
			if($freight_charge!=0) $incoterms='FOR';
			else  					$incoterms='Exw';
		}
		$d_instruction = preg_replace('/[\r\n]+/', '',$rec_sauda_header_download['remarks']);
		$d_instruction =str_replace(',','',$d_instruction);
		$sauda_date=str_replace('-','.',$sauda_date);
		$sauda_date=str_replace('/','.',$sauda_date);
		$sauda_valid_from=str_replace('-','.',$sauda_valid_from);
		$sauda_valid_from=str_replace('/','.',$sauda_valid_from);
		$valid_upto=str_replace('-','.',$valid_upto);
		$valid_upto=str_replace('/','.',$valid_upto);

		$payment_due_on=str_replace('-','.',$payment_due_on);
		$payment_due_on=str_replace('/','.',$payment_due_on);

		//$amount=round(($qty*(($sale_rate+$freight_charge+$premium)-$TD)),2);
		$amount=$rec_sauda_header_download['amount'];
		if($CMTD=='')
		{
			$CMTD=0;
		}
		$credit_limit=$rec_sauda_header_download['cm_credit_limit'];
		$packing_cost=$rec_sauda_header_download['packing_cost'];
		$packing_realization=$rec_sauda_header_download['packing_realization'];
		if($packing_cost=='')   $packing_cost=0;
		if($packing_realization=='')   $packing_realization=0;
		$honeycomb_cost=$rec_sauda_header_download['honeycomb_cost'];
		$detention_charges=$rec_sauda_header_download['detention_charges'];
		$brokerage_cost=$rec_sauda_header_download['brokerage_cost'];
		$depot_cost=$rec_sauda_header_download['depot_cost'];
		if($depot_cost=='')  		$depot_cost=0;
		$freight_cost=$rec_sauda_header_download['primary_freight'];
		if($freight_cost=='')      $freight_cost=0;
		$margin_cost=$rec_sauda_header_download['margin_cost'];
		if($margin_cost=='')       $margin_cost=0;
		$margin_cost_RA=$rec_sauda_header_download['margin_cost_RA'];
		if($margin_cost_RA=='')       $margin_cost_RA=0;
		$material_cost=$rec_sauda_header_download['material_cost'];
		$realization_per_case=$rec_sauda_header_download['realization_per_case'];
		$realization_per_MT=$rec_sauda_header_download['realization_per_MT'];
		$PR00=$rec_sauda_header_download['PR00'];
		$FRC1=$rec_sauda_header_download['FRC1'];
		$csv_sale_rate=$sale_rate;
		$sauda_type=$rec_sauda_header_download['sauda_type'];
		
		$sqlpacksize="SELECT pack_size FROM product_master WHERE dns_prod_code='".$dns_prod_code."'";
		$rspacksize=mysql_query($sqlpacksize);
		$rowpacksize=mysql_fetch_array($rspacksize);
		$packsize=$rowpacksize['pack_size'];
		
		$sqlplant="SELECT plant_name FROM branch_master WHERE dns_branch_code='".$branch_code."'";
		$rsplant=mysql_query($sqlplant);
		$rowplant=mysql_fetch_array($rsplant);
		$plant_name = $rowplant['plant_name'];

		$final_amount=$amount;
		$valuesaudaheader  = $customer_code.",";
		$valuesaudaheader .= $customer_name.",";
		$valuesaudaheader .=$route_name.",";
		$valuesaudaheader .= $broker_id.",";
		$valuesaudaheader .= $broker_name.",";
		$valuesaudaheader .= $sauda_no.",";
		$valuesaudaheader .= $sauda_date.",";
		$valuesaudaheader .= $sauda_time.",";
		$valuesaudaheader .= $sauda_valid_from.",";
		$valuesaudaheader .= $valid_upto.",";
		$valuesaudaheader .= $dns_prod_code.",";
		$valuesaudaheader .= $qty.",";
		$valuesaudaheader .= $convert_qty_two.",";
		$valuesaudaheader .= $UOM.",";
		$valuesaudaheader .= $product_group_name.",";
		$valuesaudaheader .= $packsize.",";
		$valuesaudaheader .= $prod_desc.",";
		$valuesaudaheader .= $branch_code.",";
		$valuesaudaheader .= $branch_name.",";
		$valuesaudaheader .= $state.",";
		$valuesaudaheader .= $material_cost.",";
		$valuesaudaheader .= $freight_cost.",";
		$valuesaudaheader .= $packing_cost.",";
		$valuesaudaheader .= $honeycomb_cost.",";
		$valuesaudaheader .= $brokerage_cost.",";
		$valuesaudaheader .= $detention_charges.",";
		$valuesaudaheader .= $depot_cost.",";
		$valuesaudaheader .= $margin_cost.",";
		$valuesaudaheader .= $margin_cost_RA.",";
		//$valuesaudaheader .= $sale_rate.",";
		$valuesaudaheader .= $freight_charge.",";
		$valuesaudaheader .= $TD.",";
		$valuesaudaheader .= $liquid_TD.",";
		$valuesaudaheader .= $premium.",";
		$valuesaudaheader .= $PR00.",";
		$valuesaudaheader .= $FRC1.",";
		$valuesaudaheader .= $final_amount.",";
		$valuesaudaheader .= $incoterms.",";
		$valuesaudaheader .= $employee_name.",";
		$valuesaudaheader .= $payment_due_on.",";
		$valuesaudaheader .= $credit_limit.",";
		$valuesaudaheader .= $d_instruction.",";
		$valuesaudaheader .= $vertical_value.",";
		$valuesaudaheader .= $realization_per_case.",";
		$valuesaudaheader .= $realization_per_MT.",";
		$valuesaudaheader .= $csv_sale_rate.",";
		$valuesaudaheader .= $packing_realization.",";
		$valuesaudaheader .= $plant_name.",";
		$valuesaudaheader .= $sauda_type.",";

		$linesaudaheader  .= $valuesaudaheader."\r\n";
		/*$sqlupdatesaudaheader="UPDATE sauda_header SET dwnld_transferred='YES' WHERE sauda_no='".$sauda_no."'";
		$rsupdatesaudaheader=mysql_query($sqlupdatesaudaheader)or die(mysql_error()." Error in sauda header update: ".$sqlupdatesaudaheader);*/
	}
	//$datasaudaheader = str_replace("\r\n","",$linesaudaheader);
	$datasaudaheader = $linesaudaheader;
	if ($datasaudaheader == "")
	{
		$datasaudaheader = "\r\n(0) Records Found!\n";
    }
	//$file_content1= "$headersauda_header\r\n$datasaudaheader";
	//$file_name1="Sauda Details.csv";
	/*$zip = new ZipFile();
	//add files to the zip, passing file contents, not actual files
	//$zip->addFile($file_content1, $file_name1);
	$zip->addFile($file_content1, $file_name1);*/

	//$nick_name=$_SESSION['nick_name'];
	//$nick_name=$_REQUEST['nick_name'];
	//$fullPath = "/home/acedns/public_html/acednsproduct/csvdownload/$nick_name/csv_files.zip";

	//prepare the proper content type
	//header("Content-type: application/octet-stream");
	/*header("Content-Disposition: inline; filename=csv_files.zip");
	echo $zip->file();*/
	header("Content-type: application/octet-stream");
	header("Content-Disposition: inline; filename=Sauda_Details.csv");
	print "$headersauda_header\n$datasaudaheader";

	$successval=1;
}
?>
