<?php
	ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	ob_end_flush();
	$mode = $_REQUEST['mode'];

	if($mode =='excel_download')		excelDownload();
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
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>Download Survey Excel</strong></td>
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
					<input type="hidden" name="mode" value="excel_download">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="15%" align="center"></td>
                        <table width="65%" align="center" border="0" cellpadding="5" cellspacing="1"  class="border">
                        <?php 
						$sql_check_surveytype = "SELECT survey_type, survey_type_details FROM acedns_acednsproduct.survey_form_details WHERE nick_name='".$_SESSION['nick_name']."'";
						$res_check_surveytype = mysqli_query($link,$sql_check_surveytype);
						$row_check_surveytype = mysqli_fetch_assoc($res_check_surveytype);
						$survey_type = $row_check_surveytype['survey_type'];
						$survey_type_details = $row_check_surveytype['survey_type_details'];
						$survey_type_details_array = explode(",",$survey_type_details);
						if($survey_type == 'yes'){?>
                        	<tr>
                            	<td colspan="4" align="center">Select Type:
                                <select name="survey_type">
                                <?php
									foreach($survey_type_details_array as $survey_type_value){
										if($survey_type_value == 'mall'){
											$name = 'Mall';
											$value = $survey_type_value;
										}
										else if($survey_type_value == 'hi-street'){
											$name = 'Hi Street';
											$value = $survey_type_value;
										}
										echo "<option value=\"$value\">".$name."</option>";
									}
								?>
                                </select>
                                </td>
                            </tr>
                        <?php } ?>
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
                            	<td colspan="4" align="center">Select Employee:<select name="search_emp_name">
                                <option value="">Select</option>
                                <option value="all">All</option>                                
                                <?php
								$sql_select_emp = "SELECT emp_code, emp_name FROM employee_master";
								$res_select_emp = mysqli_query($link,$sql_select_emp);
								while($row_select_emp = mysqli_fetch_assoc($res_select_emp))
								{
									if($_POST['search_emp_name'] == $row_select_emp['emp_code'])
										echo "<option value='$row_select_emp[emp_code]' selected>".$row_select_emp['emp_name']."</option>";
									else
										echo "<option value='$row_select_emp[emp_code]'>".$row_select_emp['emp_name']."</option>";
								}
								?>
                                </select>
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
function excelDownload()
{
	$from_date=$_REQUEST['from_date'];
	$to_date=$_REQUEST['to_date'];
	$from_date=date('Y-m-d',strtotime($from_date));
	$to_date=date('Y-m-d',strtotime($to_date));
	$survey_type = $_REQUEST['survey_type'];
	if($from_date!='' && $to_date!='')
	{
	  $date_condition=" AND DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') >='".$from_date."' AND 
					  	DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') <='".$to_date."'";
	}
	$search_emp_name = $_REQUEST['search_emp_name'];
	if($search_emp_name != '' && $search_emp_name != 'all')
		$emp_condition = " AND EM.emp_code = '".$search_emp_name."' ";
	else if($search_emp_name == 'all')
		$emp_condition = '';
	else
		$emp_condition = "";
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

	//For survey excel download
		$sql_check_surveytype = "SELECT survey_type, survey_type_details,survey_menu FROM acedns_acednsproduct.survey_form_details WHERE 
								nick_name='".strtoupper($_SESSION['nick_name'])."'";
		$res_check_surveytype = mysqli_query($link,$sql_check_surveytype);
		$row_check_surveytype = mysqli_fetch_assoc($res_check_surveytype);
		$survey_type = $row_check_surveytype['survey_type'];
		$survey_type_details = $row_check_surveytype['survey_type_details'];
		$survey_menu = $row_check_surveytype['survey_menu'];
		if($survey_type == 'no'){
			if($survey_menu=='yes')
			{
			  $sql_get_menu = "SELECT layout_name,menu_id,survey_sub_menu FROM survey_input WHERE type='menu'  ORDER BY display_order ASC";
			  $res_get_menu = mysqli_query($link,$sql_get_menu);
			  $count_menu=mysqli_num_rows($res_get_menu);
			  $menu_no=1;
			  $menu_id_array=array();
			  $menu_name_array=array();
			  $survey_sub_menu_array=array();
			 
			 while($row_get_menu = mysqli_fetch_assoc($res_get_menu))
			 {
				 $menu_name=$row_get_menu['layout_name'];
				 $menu_id=$row_get_menu['menu_id'];
				 $survey_sub_menu=$row_get_menu['survey_sub_menu'];
				 array_push($menu_id_array,$menu_id);
				 array_push($menu_name_array,$menu_name);
				 array_push($survey_sub_menu_array,$survey_sub_menu);
				 ${excelheader.$menu_id}=''."\t".''."\t".''."\t"."\t"."\t";
				 ${excelsubheader.$menu_id}='Sr. No'."\t".'Unique Store ID'."\t".'Emp Name'."\t".'Survey Date'."\t".'Lattitude'."\t".'Longitude'."\t";
				 ${sl_no.$menu_id}=1;
				 $row_id_string='';
				 $row_id_string_SET='';
				 $valueexcel='';
				 ${survey_id_array.$menu_id}=array();
				 					
				   $sql_get_display="SELECT display_name,row_id,action FROM survey_input WHERE  type!='menu' AND menu_id='".$menu_id."'  AND acedns='Y'
				   					ORDER BY display_order ASC";
					$res_get_display = mysqli_query($link,$sql_get_display);
					$count_display=mysqli_num_rows($res_get_display);
					for($k=0;$k<$count_display;$k++)
					{
						//echo 'A';
						${excelheader.$menu_id}.="\t";
					}
					$display_no=1;
					while($row_get_display = mysqli_fetch_assoc($res_get_display))
					{
						$display_name=$row_get_display['display_name'];
						/*$action=$row_get_display['action'];
						if($action!='')
						{
							$display_name_array=explode('#',$action);
							$display_name=$display_name_array[0];
						}*/
						$display_id=$row_get_display['row_id'];
						${excelsubheader.$menu_id}.=$display_name."\t";
						$row_id_string.="'".$display_id."'".',';
						$row_id_string_SET.=$display_id.',';
						$display_no++;
					}
				  $row_id_string=substr($row_id_string,0,-1);
				  $row_id_string_SET=substr($row_id_string_SET,0,-1);
				  
				  $sql_survey_output="SELECT *,DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%d-%m-%Y') AS survey_date FROM survey_output SO, employee_master EM WHERE SO.row_id IN(".$row_id_string.") ".$date_condition." AND SUBSTRING(SO.survey_id,3,5)=EM.emp_code".$emp_condition." 
									  ORDER BY SO.survey_id DESC,FIND_IN_SET(SO.row_id,'".$row_id_string_SET."')";
				  $rs_survey_output=mysqli_query($link,$sql_survey_output);
				  $output_no=1;
				  while($row_survey_output=mysqli_fetch_assoc($rs_survey_output))
				  {
					 $survey_id= $row_survey_output['survey_id'];
					 $survey_date= $row_survey_output['survey_date'];
					 $value=$row_survey_output['value'];
					 if(strpos($value,"#") == true){
						 $valuearray = explode("#",$value);
						 $value = $valuearray[0];
					 }
					 if(!in_array($survey_id, ${survey_id_array.$menu_id}))
					 {
						 $sqllatlong="SELECT EM.emp_name,LO.latt,LO.longi FROM location LO,employee_master EM 
						 			WHERE LO.emp_code=EM.emp_code AND LO.trans_id='".$survey_id."'";
						 $rslatlong=mysqli_query($link,$sqllatlong);
						 $rowlatlong=mysqli_fetch_assoc($rslatlong);
						 $lattitude=$rowlatlong['latt'];
						 $longitude=$rowlatlong['longi'];
						 $emp_name=$rowlatlong['emp_name'];
						 //if($output_no>1)  ${valueexcel.$survey_id}.="\n";
						 ${valueexcel.$survey_id.$menu_id}.=${sl_no.$menu_id}."\t";
						 ${valueexcel.$survey_id.$menu_id}.=$survey_id."\t";
						 ${valueexcel.$survey_id.$menu_id}.=$emp_name."\t";
						 ${valueexcel.$survey_id.$menu_id}.=$survey_date."\t";
						 ${valueexcel.$survey_id.$menu_id}.=$lattitude."\t";
						 ${valueexcel.$survey_id.$menu_id}.=$longitude."\t";
						 array_push(${survey_id_array.$menu_id},$survey_id);
						 ${sl_no.$menu_id}++;
					 }
					$sqlmasterview="SELECT type,display_table_name FROM survey_input WHERE  row_id='".$row_survey_output['row_id']."'";
					$resmasterview = mysqli_query($link,$sqlmasterview); 
					$rowmasterview=mysqli_fetch_assoc($resmasterview);
					$type=$rowmasterview['type'];
					if($type=='masterview')
					{
						$display_table_name=$rowmasterview['display_table_name'];
						$dispaly_table_name_partsone=explode('#',$display_table_name);
						$dispaly_table_name_partstwo=explode('%',$dispaly_table_name_partsone[1]);
						if($dispaly_table_name_partsone[0]=='emp_master') $dispaly_table_name_partsone[0]='employee_master';
						$sqlfetchval="SELECT $dispaly_table_name_partstwo[1] FROM $dispaly_table_name_partsone[0] WHERE $dispaly_table_name_partstwo[0]='".$value."'";
						$rsfetchval=mysqli_query($link,$sqlfetchval);
						$rowfetchval=mysqli_fetch_assoc($rsfetchval);
						$value=$rowfetchval[$dispaly_table_name_partstwo[1]];	
					}
					if($value =='') $value=$row_survey_output['value'];
					if(strtoupper($_SESSION['nick_name'])=='DURO' && ($row_survey_output['row_id']=='RA016' || $row_survey_output['row_id']=='RA017' || $row_survey_output['row_id']=='RA096' || $row_survey_output['row_id']=='RA097'))
					{
						$value=str_replace('YES:','',$value);
						$value=str_replace('NO','',$value);
					}
					 ${valueexcel.$survey_id.$menu_id}.=$value."\t";
					 //${datavalue.$menu_id}.=${valueexcel.$survey_id};
					 $output_no++;
				  }
				$menu_no++;
			 }
			 //print_r($survey_id_array);
			 //$menu_id_stat='RA115';
			 //echo ${excelheader.$menu_id_stat};
			 //echo  ${datavalue.$menu_id_stat};
			$zip = new ZipFile();
			for($i=0;$i<count($menu_id_array);$i++)
			{
				//${dataorderheader.$rds_code_array[$i]} = ${lineorderheader.$rds_code_array[$i]}.${lineorderheader_freight.$rds_code_array[$i]} ;
				for($m=0;$m<count(${survey_id_array.$menu_id_array[$i]});$m++)
				{
					${datavalue.$menu_id_array[$i]}.=${valueexcel.${survey_id_array.$menu_id_array[$i]}[$m].$menu_id_array[$i]}."\n";
				}
				if (${datavalue.$menu_id_array[$i]} == "")
				{ 
					${datavalue.$menu_id_array[$i]} = "\r\n(0) Records Found!\n";                         
				} 
				
				$date=gmdate('d',strtotime('+330 minute'));
				$month=gmdate('m',strtotime('+330 minute'));
				$year=gmdate('Y',strtotime('+330 minute'));
				$hour=gmdate('H',strtotime('+330 minute'));
				$minute=gmdate('i',strtotime('+330 minute'));
				$second=gmdate('s',strtotime('+330 minute'));
		
				${file_content.$menu_id_array[$i]}= ${excelheader.$menu_id_array[$i]}."\n".${excelsubheader.$menu_id_array[$i]}."\n".${datavalue.$menu_id_array[$i]};
				${file_name.$menu_id_array[$i]}="$menu_name_array[$i]_$survey_sub_menu_array[$i]_$date$month$year$hour$minute$second.xls";
				
				//add files to the zip, passing file contents, not actual files
				//$zip->addFile($file_content1, $file_name1);
				$zip->addFile(${file_content.$menu_id_array[$i]}, ${file_name.$menu_id_array[$i]});
			}
	
			header("Content-type: application/octet-stream");
			header("Content-Disposition: inline; filename=excel_files_survey.zip");
			echo $zip->file();
			exit();
			}
			else
			{
			//$sql_get_menu = "SELECT layout_name,menu_id FROM survey_input WHERE type='menu' ORDER BY display_order ASC";
			$sql_get_menu = "SELECT DISTINCT survey_sub_menu FROM survey_input ORDER BY survey_sub_menu ASC";
			$res_get_menu = mysqli_query($link,$sql_get_menu);
			$count_menu=mysqli_num_rows($res_get_menu);
			$menu_no=1;
			//$menu_id_array=array();
			//$menu_name_array=array();
			$survey_sub_menu_array=array();
			 while($row_get_menu = mysqli_fetch_assoc($res_get_menu))
			 {
				 //$menu_name=$row_get_menu['layout_name'];
				 //$menu_id=$row_get_menu['menu_id'];
				 $survey_sub_menu=$row_get_menu['survey_sub_menu'];
				 //array_push($menu_id_array,$menu_id);
				 //array_push($menu_name_array,$menu_name);
				 array_push($survey_sub_menu_array,$survey_sub_menu);
				 ${excelheader.$survey_sub_menu}=''."\t".''."\t".''."\t"."\t"."\t";
				 ${excelsubheader.$survey_sub_menu}='Sr. No'."\t".'Unique Store ID'."\t".'Survey Date'."\t".'Employee'."\t";
				 ${sl_no.$survey_sub_menu}=1;
				 $row_id_string='';
				 $row_id_string_SET='';
				 $valueexcel='';
				 ${survey_id_array.$survey_sub_menu}=array();
				 
				// ${array_header.$menu_id}=array();
					//$sql_get_display="SELECT display_name,row_id,action FROM survey_input WHERE type!='menu' AND menu_id='".$menu_id."' ORDER BY display_order ASC";
					    $sql_get_display="SELECT display_name,row_id,action FROM survey_input WHERE 
								type!='menu' AND survey_sub_menu='".$survey_sub_menu."' ORDER BY display_order ASC";
						$res_get_display = mysqli_query($link,$sql_get_display);
						$count_display=mysqli_num_rows($res_get_display);
						for($k=0;$k<$count_display;$k++)
						{
							//echo 'A';
							${excelheader.$survey_sub_menu}.="\t";
						}
						
						$display_no=1;
						while($row_get_display = mysqli_fetch_assoc($res_get_display))
						{
							$display_name=$row_get_display['display_name'];
							$action=$row_get_display['action'];
							/*if($action!='')
							{
								$display_name_array=explode('#',$action);
								$display_name=$display_name_array[0];
							}*/
							$display_id=$row_get_display['row_id'];
							${excelsubheader.$survey_sub_menu}.=$display_name."\t";
							$row_id_string.="'".$display_id."'".',';
							$row_id_string_SET.=$display_id.',';
							$display_no++;
						}
					//$layer_no++;
				 
				  $row_id_string=substr($row_id_string,0,-1);
				  $row_id_string_SET=substr($row_id_string_SET,0,-1);
				  $sql_survey_output="SELECT *,DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%d-%m-%Y') AS survey_date FROM survey_output SO, employee_master EM WHERE SO.row_id IN(".$row_id_string.") ".$date_condition." AND SUBSTRING(SO.survey_id,3,5)=EM.emp_code".$emp_condition." 
									  ORDER BY SO.survey_id DESC,FIND_IN_SET(SO.row_id,'".$row_id_string_SET."')";
				  $rs_survey_output=mysqli_query($link,$sql_survey_output);
				  $output_no=1;
				  while($row_survey_output=mysqli_fetch_assoc($rs_survey_output))
				  {
					 $survey_id= $row_survey_output['survey_id'];
					 $survey_date= $row_survey_output['survey_date'];
					 $value=$row_survey_output['value'];
					 /*if(strpos($value,"#") == true){
						 $valuearray = explode("#",$value);
						 $value = $valuearray[0];
					 }*/
					 if(!in_array($survey_id, ${survey_id_array.$survey_sub_menu}))
					 {
						 $sqllatlong="SELECT emp_code FROM location WHERE trans_id='".$survey_id."'";
						 $rslatlong=mysqli_query($link,$sqllatlong);
						 $rowlatlong=mysqli_fetch_assoc($rslatlong);
						 $emp_code = $rowlatlong['emp_code'];
						 $sql_empname = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
						 $res_empname = mysqli_query($link,$sql_empname);
						 $row_empname = mysqli_fetch_assoc($res_empname);
						 $emp_name = $row_empname['emp_name'];
						 //if($output_no>1)  ${valueexcel.$survey_id}.="\n";
						 ${valueexcel.$survey_id.$survey_sub_menu}.=${sl_no.$survey_sub_menu}."\t";
						 ${valueexcel.$survey_id.$survey_sub_menu}.=$survey_id."\t";
						 ${valueexcel.$survey_id.$survey_sub_menu}.=$survey_date."\t";
						 ${valueexcel.$survey_id.$survey_sub_menu}.=$emp_name."\t";
						 array_push(${survey_id_array.$survey_sub_menu},$survey_id);
						 ${sl_no.$survey_sub_menu}++;
					 }
					 
					 ${valueexcel.$survey_id.$survey_sub_menu}.=$value."\t";
					 //${datavalue.$menu_id}.=${valueexcel.$survey_id};
					 $output_no++;
				  }
				$menu_no++;
			 }
			 //print_r($survey_id_array);
			 //$menu_id_stat='RA115';
			 //echo ${excelheader.$menu_id_stat};
			 //echo  ${datavalue.$menu_id_stat};
			$zip = new ZipFile();
			for($i=0;$i<count($survey_sub_menu_array);$i++)
			{
				//${dataorderheader.$rds_code_array[$i]} = ${lineorderheader.$rds_code_array[$i]}.${lineorderheader_freight.$rds_code_array[$i]} ;
				for($m=0;$m<count(${survey_id_array.$survey_sub_menu_array[$i]});$m++)
				{
					${datavalue.$survey_sub_menu_array[$i]}.=${valueexcel.${survey_id_array.$survey_sub_menu_array[$i]}[$m].$survey_sub_menu_array[$i]}."\n";
				}
				if (${datavalue.$survey_sub_menu_array[$i]} == "")
				{ 
					${datavalue.$survey_sub_menu_array[$i]} = "\r\n(0) Records Found!\n";                         
				} 
				
				$date=gmdate('d',strtotime('+330 minute'));
				$month=gmdate('m',strtotime('+330 minute'));
				$year=gmdate('Y',strtotime('+330 minute'));
				$hour=gmdate('H',strtotime('+330 minute'));
				$minute=gmdate('i',strtotime('+330 minute'));
				$second=gmdate('s',strtotime('+330 minute'));
		
				${file_content.$survey_sub_menu_array[$i]}= ${excelheader.$survey_sub_menu_array[$i]}."\n".${excelsubheader.$survey_sub_menu_array[$i]}."\n".${datavalue.$survey_sub_menu_array[$i]};
				${file_name.$survey_sub_menu_array[$i]}="$survey_sub_menu_array[$i]_$date$month$year$hour$minute$second.xls";
				
				//add files to the zip, passing file contents, not actual files
				//$zip->addFile($file_content1, $file_name1);
				$zip->addFile(${file_content.$survey_sub_menu_array[$i]}, ${file_name.$survey_sub_menu_array[$i]});
			}
	
			header("Content-type: application/octet-stream");
			header("Content-Disposition: inline; filename=excel_files_survey.zip");
			echo $zip->file();
			exit();
			}
		}
		else{
			$sql_get_menu = "SELECT layout_name,menu_id FROM survey_input WHERE type='menu' AND survey_type='".$survey_type."' ORDER BY display_order ASC";
			$res_get_menu = mysqli_query($link,$sql_get_menu);
			$count_menu=mysqli_num_rows($res_get_menu);
			$menu_no=1;
			$menu_id_array=array();
			$menu_name_array=array();
			 
			 while($row_get_menu = mysqli_fetch_assoc($res_get_menu))
			 {
				 $menu_name=$row_get_menu['layout_name'];
				 $menu_id=$row_get_menu['menu_id'];
				 array_push($menu_id_array,$menu_id);
				 array_push($menu_name_array,$menu_name);
				 ${excelheader.$menu_id}=''."\t".''."\t".''."\t"."\t"."\t";
				 ${excelsubheader.$menu_id}='Sr. No'."\t".'Unique Store ID'."\t".'Survey Date'."\t".'Lattitude'."\t".'Longitude'."\t";
				 ${sl_no.$menu_id}=1;
				 $row_id_string='';
				 $row_id_string_SET='';
				 $valueexcel='';
				 ${survey_id_array.$menu_id}=array();
				 
				// ${array_header.$menu_id}=array();
				$sql_layer_check = "SELECT row_id FROM survey_input WHERE type = 'layer'";
				$res_layer_check = mysqli_query($link,$sql_layer_check);
				$total_layer_check = mysqli_num_rows($res_layer_check);
				
				
				 $sql_get_layer = "SELECT layout_name,row_id FROM survey_input WHERE type='layer' AND 
										menu_id='".$menu_id."' ORDER BY display_order ASC";			 
				 $res_get_layer = mysqli_query($link,$sql_get_layer);
				 $count_layer=mysqli_num_rows($res_get_layer);
				 $layer_no=1;
				 while($row_get_layer = mysqli_fetch_assoc($res_get_layer))
				 {
						$layout_name=$row_get_layer['layout_name'];
						$layer_id=$row_get_layer['row_id'];
						${excelheader.$menu_id}.=$layout_name;
						 
						$sql_get_display="SELECT display_name,row_id,action FROM survey_input WHERE layout_name='".$layout_name."' 
											AND type!='layer' AND type!='menu' AND menu_id='".$menu_id."' ORDER BY display_order ASC";
						
						$res_get_display = mysqli_query($link,$sql_get_display);
						$count_display=mysqli_num_rows($res_get_display);
						for($k=0;$k<$count_display;$k++)
						{
							//echo 'A';
							${excelheader.$menu_id}.="\t";
						}
						
						$display_no=1;
						while($row_get_display = mysqli_fetch_assoc($res_get_display))
						{
							$display_name=$row_get_display['display_name'];
							$action=$row_get_display['action'];
							if($action!='')
							{
								$display_name_array=explode('#',$action);
								$display_name=$display_name_array[0];
							}
							$display_id=$row_get_display['row_id'];
							${excelsubheader.$menu_id}.=$display_name."\t";
							$row_id_string.="'".$display_id."'".',';
							$row_id_string_SET.=$display_id.',';
							$display_no++;
						}
					$layer_no++;
				 }
				 
				  $row_id_string=substr($row_id_string,0,-1);
				  $row_id_string_SET=substr($row_id_string_SET,0,-1);
				  $sql_survey_output="SELECT *,DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%d-%m-%Y') AS survey_date FROM survey_output SO, employee_master EM WHERE SO.row_id IN(".$row_id_string.") ".$date_condition." AND SUBSTRING(SO.survey_id,3,5)=EM.emp_code".$emp_condition." 
									  ORDER BY SO.survey_id DESC,FIND_IN_SET(SO.row_id,'".$row_id_string_SET."')";
				  $rs_survey_output=mysqli_query($link,$sql_survey_output);
				  $output_no=1;
				  while($row_survey_output=mysqli_fetch_assoc($rs_survey_output))
				  {
					 $survey_id= $row_survey_output['survey_id'];
					 $survey_date= $row_survey_output['survey_date'];
					 $value=$row_survey_output['value'];
					 if(strpos($value,"#") == true){
						 $valuearray = explode("#",$value);
						 $value = $valuearray[0];
					 }
					 if(!in_array($survey_id, ${survey_id_array.$menu_id}))
					 {
						 $sqllatlong="SELECT latt,longi FROM location WHERE trans_id='".$survey_id."'";
						 $rslatlong=mysqli_query($link,$sqllatlong);
						 $rowlatlong=mysqli_fetch_assoc($rslatlong);
						 $lattitude=$rowlatlong['latt'];
						 $longitude=$rowlatlong['longi'];
						 //if($output_no>1)  ${valueexcel.$survey_id}.="\n";
						 ${valueexcel.$survey_id.$menu_id}.=${sl_no.$menu_id}."\t";
						 ${valueexcel.$survey_id.$menu_id}.=$survey_id."\t";
						 ${valueexcel.$survey_id.$menu_id}.=$survey_date."\t";
						 ${valueexcel.$survey_id.$menu_id}.=$lattitude."\t";
						 ${valueexcel.$survey_id.$menu_id}.=$longitude."\t";
						 array_push(${survey_id_array.$menu_id},$survey_id);
						 ${sl_no.$menu_id}++;
					 }
					 ${valueexcel.$survey_id.$menu_id}.=$value."\t";
					 //${datavalue.$menu_id}.=${valueexcel.$survey_id};
					 $output_no++;
				  }
				$menu_no++;
			 }
			 //print_r($survey_id_array);
			 //$menu_id_stat='RA115';
			 //echo ${excelheader.$menu_id_stat};
			 //echo  ${datavalue.$menu_id_stat};
			$zip = new ZipFile();
			for($i=0;$i<count($menu_id_array);$i++)
			{
				//${dataorderheader.$rds_code_array[$i]} = ${lineorderheader.$rds_code_array[$i]}.${lineorderheader_freight.$rds_code_array[$i]} ;
				for($m=0;$m<count(${survey_id_array.$menu_id_array[$i]});$m++)
				{
					${datavalue.$menu_id_array[$i]}.=${valueexcel.${survey_id_array.$menu_id_array[$i]}[$m].$menu_id_array[$i]}."\n";
				}
				if (${datavalue.$menu_id_array[$i]} == "")
				{ 
					${datavalue.$menu_id_array[$i]} = "\r\n(0) Records Found!\n";                         
				} 
				
				$date=gmdate('d',strtotime('+330 minute'));
				$month=gmdate('m',strtotime('+330 minute'));
				$year=gmdate('Y',strtotime('+330 minute'));
				$hour=gmdate('H',strtotime('+330 minute'));
				$minute=gmdate('i',strtotime('+330 minute'));
				$second=gmdate('s',strtotime('+330 minute'));
		
				${file_content.$menu_id_array[$i]}= ${excelheader.$menu_id_array[$i]}."\n".${excelsubheader.$menu_id_array[$i]}."\n".${datavalue.$menu_id_array[$i]};
				${file_name.$menu_id_array[$i]}="$menu_name_array[$i]_$date$month$year$hour$minute$second.xls";
				
				//add files to the zip, passing file contents, not actual files
				//$zip->addFile($file_content1, $file_name1);
				$zip->addFile(${file_content.$menu_id_array[$i]}, ${file_name.$menu_id_array[$i]});
			}
	
			header("Content-type: application/octet-stream");
			header("Content-Disposition: inline; filename=excel_files_survey.zip");
			echo $zip->file();
			exit();
		}
		
		//$sql_get_menu = "SELECT layout_name,menu_id FROM survey_input WHERE type='menu' AND survey_type='".$survey_type."' ORDER BY display_order ASC";die;
		
}
?>	