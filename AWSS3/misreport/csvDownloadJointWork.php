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
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>Download Joint Work</strong></td>
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
                                     //$sqlqueryemp="SELECT emp_code,emp_name FROM employee_master WHERE 1 ".$emp_hierarchy_condition." ORDER BY emp_name ASC";
									 $sqlqueryemp="SELECT DISTINCT EM.dns_emp_code,EM.emp_name,RP.emp_code FROM route_plan RP,employee_master EM
													WHERE RP.emp_code=EM.emp_code AND RP.working_with!=''
													ORDER BY EM.emp_name ASC";
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
		$emp_condition_one=" AND RP.emp_code='".$emp_name."'";
	}
	else
	{
		$emp_condition='';
		$emp_condition_one='';
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

	//For Joint work 
		if($from_date!='' && $to_date!='')
		{
			 $date_condition_one=" AND DATE_FORMAT(RP.visit_date,'%Y-%m-%d') >='".$from_date."' AND 
							DATE_FORMAT(RP.visit_date,'%Y-%m-%d') <='".$to_date."'";
		}
	
	$arr_header = array('Visit Date','Employee code','Employee name','Joint work employee name','Customer remarks','Employee remarks','No of call');
	$headerjointwork = "";
	foreach($arr_header AS $header_value)
	{
		$headerjointwork .= $header_value. ",";
	}

	$sql_joint_work_download = "SELECT EM.dns_emp_code,EM.emp_name,RP.emp_code,RP.working_with,RP.route_code,RP.visit_date AS visit_date_original,
								DATE_FORMAT(RP.visit_date,'%d/%m/%Y') AS visit_date
								FROM route_plan RP,employee_master EM
								WHERE RP.emp_code=EM.emp_code AND RP.working_with!=''
								".$emp_condition_one.$date_condition_one." 
								ORDER BY DATE_FORMAT(RP.visit_date,'%Y-%m-%d'),EM.emp_name ASC";
	$rs_joint_work_download = mysql_query($sql_joint_work_download) or die(mysql_error()." Error in joint work download: ".$sql_joint_work_download);
	$linejointwork = ''; 
	while($rec_joint_work_download = mysql_fetch_array($rs_joint_work_download)) 
	{ 
		$emp_code=$rec_joint_work_download['emp_code'];
		$dns_emp_code=$rec_joint_work_download['dns_emp_code'];
		$emp_name=$rec_joint_work_download['emp_name'];
		$working_with=$rec_joint_work_download['working_with'];
		$visit_date=$rec_joint_work_download['visit_date'];
		$visit_date_original=$rec_joint_work_download['visit_date_original'];
		$route_code=$rec_joint_work_download['route_code'];
			
			$sqlworkingwithname="SELECT emp_name FROM employee_master WHERE emp_code='".$working_with."'";
			$rsworkingwithname=mysql_query($sqlworkingwithname);
			$rowworkingwithname=mysql_fetch_array($rsworkingwithname);
			$working_with_name=$rowworkingwithname['emp_name'];
			
			$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route_code."'";
			$rsroutename=mysql_query($sqlroutename);
			$rowroutename=mysql_fetch_array($rsroutename);
			$route_name=$rowroutename['route_name'];
			
			$sqlobservation="SELECT observation_on_emp,observation_on_customer FROM  joint_work_observation 
							WHERE route_code='".$route_code."' AND emp_code='".$working_with."' 
							AND SUBSTRING(joint_work_id,3,5)='".$emp_code."' AND SUBSTRING(joint_work_id,-14,8)='".str_replace("-","",$visit_date_original)."'"; 
			$rsobservation=mysql_query($sqlobservation);
			$rowobservation=mysql_fetch_array($rsobservation);
			$employee_observation=$rowobservation['observation_on_emp'];
			$customer_observation=$rowobservation['observation_on_customer'];
			
			$sqlcall="SELECT COUNT(customer_code) AS no_of_call FROM  joint_work_observation 
							WHERE  emp_code='".$working_with."' AND SUBSTRING(joint_work_id,3,5)='".$emp_code."' 
							AND SUBSTRING(joint_work_id,-14,8)='".str_replace("-","",$visit_date_original)."'"; 
			$rscall=mysql_query($sqlcall);
			$rowcall=mysql_fetch_array($rscall);
			$no_of_call=$rowcall['no_of_call'];
			
			
		$valuejointwork = $visit_date.",";	
		$valuejointwork .= $dns_emp_code.",";
		$valuejointwork .= $emp_name.' - '.$route_name.",";
		$valuejointwork .= $working_with_name.",";
		$valuejointwork .= $customer_observation.",";
		$valuejointwork .= $employee_observation.",";
		$valuejointwork .= $no_of_call.",";
		$linejointwork  .= $valuejointwork."\r\n"; 
		/*$sqlupdateorderheader="UPDATE order_header SET dwnld_transferred='YES' WHERE order_no='".$order_no."'";
		$rsupdateorderheader=mysql_query($sqlupdateorderheader)or die(mysql_error()." Error in order header update: ".$sqlupdateorderheader);*/
	}
	//exit();
	//$dataorderheader = str_replace("\r\n","",$lineorderheader);
	$datajointwork = $linejointwork;	
	if ($datajointwork == "")
	{ 
		$datajointwork = "\r\n(0) Records Found!\n";                         
    } 
	
		$file_content4= "$headerjointwork\r\n$datajointwork";
		$file_name4="Joint Work Details.csv";
	//$zipname = 'csv_files.zip';
	//$zip = new ZipFile();
	//$zip->open($zipname, ZipFile::CREATE);
	//add files to the zip, passing file contents, not actual files
	//$zip->addFile($file_content1, $file_name1);
	//$zip->addFile($file_content1, $file_name1);
	//$zip->addFile($file_content2, $file_name2);
	//$zip->addFile($file_content3, $file_name3);
	//if(strtoupper($_SESSION['nick_name'])=='DNVFOODS' || strtoupper($_SESSION['nick_name'])=='PALSONS')
	//{
		//$zip->addFile($file_content4, $file_name4);
	//}
	

	header("Content-type: application/csv");
	header("Content-Disposition: inline; filename=joint_work.csv");
	
    //echo $zip->file();
	echo $file_content4;
	
	exit();
	

	
	$successval=1;
	if($successval!=1){
		mysql_query("ROLLBACK");
	}
}
?>