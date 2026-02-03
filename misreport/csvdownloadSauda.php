<?php
	ob_start();
	session_start();
	require("adminUtils.php");
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
									$res_state = mysqli_query($link,$sql_state);
									$state_total = mysqli_num_rows($res_state);
										$state_select_control = "<select name=\"state\" id=\"state\">";
										$state_select_control .= "<option value=\"\">Select</option>";

										$res_state = mysqli_query($link,$sql_state);
										while($row_state = mysqli_fetch_assoc($res_state)){
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
		$state_condition=" AND EM.state=".$state;
	}
	$to_date=$_REQUEST['to_date'];
	$from_date=date('Y-m-d',strtotime($from_date));
	$to_date=date('Y-m-d',strtotime($to_date));
	if($from_date!='' && $to_date!='')
	{
		 $date_condition=" AND DATE_FORMAT(LO.date,'%Y-%m-%d') >='".$from_date."' AND
					  	DATE_FORMAT(LO.date,'%Y-%m-%d') <='".$to_date."'";
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
	$arr_header = array('Customer Code','Customer Name','Broker Code','Broker Name','App Contract No','App Contract Date','App Contract Time','Contract Valid From',
		'Contract Valid To','Material Code','Material Qty(case)','Material Qty(MT)','UOM (case)','Product Group','Material Description','Depot Code','Depot Name','State','Material Cost','Primary Freight','Packing Cost','Depot Cost','Margin Cost','Freight','TD','LTD','Premium','PR00','FRC1','Total Value','Incoterms',
		'Employee Name','Payment Due On','CM-Credit Limit','Remarks','Vertical','Realization Per case','Realization Per MT','Sale Rate');
	$headersauda_header = "";
	foreach($arr_header AS $header_value)
	{
		$headersauda_header .= $header_value. ",";
	}

	$sql_sauda_header_download = "SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,CM.credit_days,CM.state_code,SH.broker_id,SH.sauda_no,DATE_FORMAT(LO.date,'%d/%m/%Y') AS sauda_date,DATE_FORMAT(LO.date,'%H:%i:%s') AS sauda_time,
								 LO.date AS sauda_date_time,DATE_FORMAT(SH.sauda_valid_from,'%d/%m/%Y') AS sauda_valid_from,DATE_FORMAT(SH.sauda_valid_from,'%Y-%m-%d') AS sauda_valid_from_calc,
								 DATE_FORMAT(LO.date,'%Y-%m-%d') AS sauda_date_calc,CM.sauda_validity_period,PM.dns_prod_code,PM.vertical_value,SH.customer_code,LO.emp_code,
								 SD.sku_code,SD.qty,SD.convert_qty_two,PM.UOM1,PM.prod_desc,PM.product_group_code,PM.vertical_value,PM.conversion_factor,PM.conversion_factor_two,BM.branch_code,BM.dns_branch_code,BM.branch_name,BM.plant_name,SD.sale_rate,SD.freight_charge,SD.TD,SD.liquid_TD,SD.premium,SD.amount,CM.TD AS CMTD,EM.emp_name,EM.state,
								 PGM.product_group_name,PGM.is_upload,PGM.formulation,SH.d_instruction FROM sauda_header SH,location LO,sauda_details SD,product_master PM,customer_master CM,branch_master BM,employee_master EM, product_group_master PGM
								 WHERE LO.trans_id=SH.sauda_no AND SH.sauda_no=SD.sauda_no AND LO.emp_code=EM.emp_code AND (LO.trans_id LIKE 'FT%') AND LO.emp_code!='C0007' AND SD.sku_code=PM.prod_code $state_condition AND
								 SH.customer_code=CM.customer_code AND SH.branch_code=BM.branch_code AND PM.product_group_code=PGM.product_group_code
								 ".$date_condition." ORDER BY DATE_FORMAT(LO.date,'%Y-%m-%d %H:%i:%s') ASC,PGM.product_group_name,EM.state  ASC";
	$rs_sauda_header_download = mysqli_query($link,$sql_sauda_header_download) or die(mysqli_error()." Error in sauda data download: ".$sql_sauda_header_download);
	$linesaudaheader = '';
	while($rec_sauda_header_download = mysqli_fetch_assoc($rs_sauda_header_download))
	{
		$dns_customer_code=$rec_sauda_header_download['dns_customer_code'];
		$customer_code=$rec_sauda_header_download['customer_code'];
		$customer_name=$rec_sauda_header_download['customer_name'];
		$broker_id=$rec_sauda_header_download['broker_id'];
		$product_group_code = $rec_sauda_header_download['product_group_code'];
		$emp_code = $rec_sauda_header_download['emp_code'];
		$product_group_name = $rec_sauda_header_download['product_group_name'];
		$state = $rec_sauda_header_download['state'];
		$customer_code = $rec_sauda_header_download['customer_code'];
		$branch_code = $rec_sauda_header_download['branch_code'];
		$plant_name = $rec_sauda_header_download['plant_name'];
		$vertical_value = $rec_sauda_header_download['vertical_value'];
		$conversion_one = $rec_sauda_header_download['conversion_factor'];
		$conversion_two = $rec_sauda_header_download['conversion_factor_two'];
		$sauda_date_time = $rec_sauda_header_download['sauda_date_time'];
		$sauda_time = $rec_sauda_header_download['sauda_time'];
		$liquid_TD = $rec_sauda_header_download['liquid_TD'];
		$state_code = $rec_sauda_header_download['state_code'];
		$is_upload = $rec_sauda_header_download['is_upload'];
		$formulation = $rec_sauda_header_download['formulation'];

		$sqlbroker="SELECT dns_broker_id,broker_name FROM broker_master WHERE broker_id='".$broker_id."'";
		$rsbroker=mysqli_query($link,$sqlbroker);
		$recbroker=mysqli_fetch_assoc($rsbroker);
		$dns_broker_id=$recbroker['dns_broker_id'];
		$broker_name=$recbroker['broker_name'];
		$sauda_no=$rec_sauda_header_download['sauda_no'];
		$sauda_date=$rec_sauda_header_download['sauda_date'];
		$credit_days=$rec_sauda_header_download['credit_days'];
		$sauda_date_calc=$rec_sauda_header_download['sauda_date_calc'];
		$payment_due_on=date('d/m/Y',strtotime("+$credit_days days,$sauda_date_calc"));
		$sauda_valid_from=$rec_sauda_header_download['sauda_valid_from'];
		$sauda_valid_from_calc=$rec_sauda_header_download['sauda_valid_from_calc'];
		$sauda_valid_days = $rec_sauda_header_download['sauda_validity_period'];
		$valid_upto = date('d/m/Y',strtotime("+$sauda_valid_days days,$sauda_valid_from_calc"));
		$dns_prod_code=$rec_sauda_header_download['dns_prod_code'];
		$emp_code=$rec_sauda_header_download['emp_code'];
		$sku_code=$rec_sauda_header_download['sku_code'];
		$qty=$rec_sauda_header_download['qty'];
		$qty=round($qty,2);
		$convert_qty_two=$rec_sauda_header_download['convert_qty_two'];
		$UOM1=$rec_sauda_header_download['UOM1'];
		$prod_desc=$rec_sauda_header_download['prod_desc'];
		$vertical_value = $rec_sauda_header_download['vertical_value'];
		$vertical_value =str_replace(',','&',$vertical_value);
		$dns_branch_code=$rec_sauda_header_download['dns_branch_code'];
		$branch_name=$rec_sauda_header_download['branch_name'];
		$sale_rate=$rec_sauda_header_download['sale_rate'];
		$freight_charge=$rec_sauda_header_download['freight_charge'];
		$TD=$rec_sauda_header_download['TD'];
		$premium=$rec_sauda_header_download['premium'];
		if($premium=='')  $premium=0;
		$employee_name=$rec_sauda_header_download['emp_name'];
		$CMTD=$rec_sauda_header_download['CMTD'];
		if($freight_charge=='')  $freight_charge=0;
		if($freight_charge!=0) $incoterms='FOR';
		else  					$incoterms='Exw';
		$d_instruction = preg_replace('/[\r\n]+/', '',$rec_sauda_header_download['d_instruction']);
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
		$sqlstate="SELECT `state` FROM state_master WHERE dns_state_code='".$state_code."'";
		$rsstate=mysqli_query($link,$sqlstate);
		$rowstate=mysqli_fetch_assoc($rsstate);
		$state_name=$rowstate['state'];
		$sqlcustomercreditlimit="SELECT credit_limit FROM customer_vertical_credit_limit WHERE
								customer_code='".$customer_code."' AND branch_code='".$branch_code."' AND vertical_value='".$vertical_value."'";
		$rscustomercreditlimit=mysqli_query($link,$sqlcustomercreditlimit);
		$rowcustomercreditlimit=mysqli_fetch_assoc($rscustomercreditlimit);
		$credit_limit=$rowcustomercreditlimit['credit_limit'];

		/*For Price Generation Parameter*/
		$sqllooserate="SELECT loose_rate_ton FROM pricing_detials WHERE product_group_code='".$product_group_code."' AND
						plant_name='".$plant_name."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rslooserate=mysqli_query($link,$sqllooserate);
		$rowlooserate=mysqli_fetch_assoc($rslooserate);
		$loose_rate_ton=$rowlooserate['loose_rate_ton'];

		//For live

		$sqlpackingprodwise="SELECT packing_cost,packing_realization FROM packing_master WHERE dns_prod_code='".$dns_prod_code."'
							AND plant_name='".$plant_name."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";

		/*$sqlpackingprodwise="SELECT packing_cost FROM packing_master WHERE dns_prod_code='".$dns_prod_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";*/
		$rspackingprodwise=mysqli_query($link,$sqlpackingprodwise);
		$rowpackingprodwise=mysqli_fetch_assoc($rspackingprodwise);
		$packing_cost=round($rowpackingprodwise['packing_cost'],2);
		if($packing_cost=='')   $packing_cost=0;

		if($loose_rate_ton==0 && $formulation=='no')
		 {
		   $packing_cost=0;
	   }
		if($loose_rate_ton==0 && $formulation=='yes'){
			 $packing_cost=$packing_cost;
		 }
		/*$sqldepotcostprodwise="SELECT depot_cost,freight FROM depot_freight_cost WHERE dns_prod_code='".$dns_prod_code."'
							AND branch_code='".$branch_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";*/
		$sqldepotcostprodwise="SELECT depot_cost FROM depot_cost WHERE dns_prod_code='".$dns_prod_code."'
							AND branch_code='".$branch_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rsdepotcostprodwise=mysqli_query($link,$sqldepotcostprodwise);
		$rowdepotcostprodwise=mysqli_fetch_assoc($rsdepotcostprodwise);
		$depot_cost=round($rowdepotcostprodwise['depot_cost'],2);
		if($depot_cost=='')  		$depot_cost=0;


		if($loose_rate_ton==0 && $formulation=='no')
		 {
		   $depot_cost=0;
	   }
		if($loose_rate_ton==0 && $formulation=='yes'){
			 $depot_cost=$depot_cost;
		 }

		$sqlfreightcostprodwise="SELECT freight_cost FROM freight_cost WHERE dns_prod_code='".$dns_prod_code."'
							AND branch_code='".$branch_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rsfreightcostprodwise=mysqli_query($link,$sqlfreightcostprodwise);
		$rowfreightcostprodwise=mysqli_fetch_assoc($rsfreightcostprodwise);
		$freight_cost=round($rowfreightcostprodwise['freight_cost'],2);
		if($freight_cost=='')      $freight_cost=0;
		
		if($loose_rate_ton==0 && $formulation=='no')
		 {
		   $freight_cost=0;
	   }
		if($loose_rate_ton==0 && $formulation=='yes'){
			 $freight_cost=$freight_cost;
		 }

		//Temporary start
		/*$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE prod_code='".$dns_prod_code."'
							AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rsqtytruckload=mysqli_query($link,$sqlqtytruckload);
		$countqtytruckload=mysqli_num_rows($rsqtytruckload);
		if($countqtytruckload >0)
		{
			$rowqtytruckload=mysqli_fetch_assoc($rsqtytruckload);
			${qty_truck_load.$dns_prod_code}=round($rowqtytruckload['qty_truck_load'],2);
		}
		if(${qty_truck_load.$dns_prod_code}=='')    ${qty_truck_load.$dns_prod_code}=0;

		$sqlhirecost="SELECT hire_cost FROM basic_freight WHERE branch_code='".$branch_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rshirecost=mysqli_query($link,$sqlhirecost);
		$counthirecost=mysqli_num_rows($rshirecost);
		if($counthirecost >0)
		{
			$rowhirecost=mysqli_fetch_assoc($rshirecost);
			$hire_cost=round($rowhirecost['hire_cost'],2);
		}
		if($hire_cost=='')    $hire_cost=0;

		if(${qty_truck_load.$dns_prod_code}>0)
		{
			$freight_cost=$hire_cost/${qty_truck_load.$dns_prod_code};
		}
		else
		{
			$freight_cost=0;
		}
		//echo $freight_cost;
		$freight_cost=round($freight_cost,2);*/
		//Temporary end

		$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$dns_prod_code."'
							AND branch_code='".$branch_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rsmargincostprodwise=mysqli_query($link,$sqlmargincostprodwise);
		$rowmargincostprodwise=mysqli_fetch_assoc($rsmargincostprodwise);
		$margin_cost=round($rowmargincostprodwise['margin_cost'],2);
		if($margin_cost=='')       $margin_cost=0;

		if($loose_rate_ton==0 && $formulation=='no')
		 {
		   $margin_cost=0;
	   }
		if($loose_rate_ton==0 && $formulation=='yes'){
			 $margin_cost=$margin_cost;
		 }

		if($formulation=='yes'){
			$material_cost=$sale_rate-$packing_cost-$margin_cost;
		}
		else{
		$material_cost=round(($loose_rate_ton/$conversion_two),2);
		$material_cost=round(($material_cost*$conversion_one),2);
	  }

		if(strpos($prod_desc,'LUP')==true){
			$material_cost=0;
		}
		if($material_cost==0)
		{
			/*$sqlsalerate="SELECT sale_rate FROM sauda_mrp WHERE branch_code='".$branch_code."' AND product_code='".$sku_code."'";
			$rssalerate=mysqli_query($link,$sqlsalerate);
			$rowsalerate=mysqli_fetch_assoc($rssalerate);
			$material_cost=$rowsalerate['sale_rate'];*/
			$material_cost=$sale_rate;
		}
		if($material_cost==0){
			$realization_per_case=0;
			$realization_per_MT=0;
		}
		else
		{
			//For live
		//$packing_realization=$rowpackingprodwise['packing_realization'];
		//$realization_per_case=$material_cost-$packing_realization;
		$realization_per_case=$material_cost-$TD+$premium;
		$realization_per_case=round($realization_per_case,2);
		$realization_per_MT=round((($realization_per_case*$rec_sauda_header_download['conversion_factor_two'])/$rec_sauda_header_download['conversion_factor']),2);
		}

		//$final_amount=$amount-(($amount*$CMTD)/100);


		$PR00=$material_cost+$packing_cost+$margin_cost+$premium-$TD-$liquid_TD;
		$FRC1=$freight_cost+$freight_charge+$depot_cost;
		$csv_sale_rate=$PR00+$FRC1;


		$final_amount=$amount;
		$valuesaudaheader  = $dns_customer_code.",";
		$valuesaudaheader .= $customer_name.",";
		$valuesaudaheader .= $dns_broker_id.",";
		$valuesaudaheader .= $broker_name.",";
		$valuesaudaheader .= $sauda_no.",";
		$valuesaudaheader .= $sauda_date.",";
		$valuesaudaheader .= $sauda_time.",";
		$valuesaudaheader .= $sauda_valid_from.",";
		$valuesaudaheader .= $valid_upto.",";
		$valuesaudaheader .= $dns_prod_code.",";
		$valuesaudaheader .= $qty.",";
		$valuesaudaheader .= $convert_qty_two.",";
		$valuesaudaheader .= $UOM1.",";
		$valuesaudaheader .= $product_group_name.",";
		$valuesaudaheader .= $prod_desc.",";
		$valuesaudaheader .= $dns_branch_code.",";
		$valuesaudaheader .= $branch_name.",";
		$valuesaudaheader .= $state_name.",";
		$valuesaudaheader .= $material_cost.",";
		$valuesaudaheader .= $freight_cost.",";
		$valuesaudaheader .= $packing_cost.",";
		$valuesaudaheader .= $depot_cost.",";
		$valuesaudaheader .= $margin_cost.",";
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

		$linesaudaheader  .= $valuesaudaheader."\r\n";
		/*$sqlupdatesaudaheader="UPDATE sauda_header SET dwnld_transferred='YES' WHERE sauda_no='".$sauda_no."'";
		$rsupdatesaudaheader=mysqli_query($link,$sqlupdatesaudaheader)or die(mysqli_error()." Error in sauda header update: ".$sqlupdatesaudaheader);*/
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
