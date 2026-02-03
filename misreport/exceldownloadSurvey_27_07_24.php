<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
//ob_start();
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");

if ($_SESSION['admin_login'] == "")          header("location:index.php");
ob_end_flush();
$mode = $_REQUEST['mode'];

if ($mode == 'excel_download')        excelDownload();
else  disphtml("main();");
ob_end_flush();

function main()
{
	require("include/dbcon.php");
    if (strtoupper($_SESSION['admin_login']) == "ADMIN") {
        //$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
        $emp_hierarchy_condition = "";
        $customer_condition = " 1 ";
    } else {
        $emp_hierarchy = return_employee_hierarchy($_SESSION['admin_login']);
        $emp_hierarchy_condition = " AND emp_code IN (" . $emp_hierarchy . ") AND acedns!='N' ";
        $customer_condition = " CM.emp_code IN (" . $emp_hierarchy . ") ";
    }
?>
    <?php if (strtoupper($_SESSION['nick_name']) != 'DURO') { ?>
        <script>
            function check() {
                if (document.frmSearch.emp_name.value == 0) {
                    alert('Please select a employee.');
                    document.frmSearch.emp_name.focus();
                    return false;
                }
                if (document.frmSearch.from_date.value.search(/\S/) == 0) {
                    if (document.frmSearch.to_date.value.search(/\S/) == -1) {
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
                <td valign="top" bgcolor="#FFFFFF">
                    <table width="80%" align="center" border="0" cellpadding="5" cellspacing="1">
                        <tr>
                            <td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']); ?></td>
                            <td align="right" colspan="2"></td>
                        </tr>
                    </table>

                    <!--------------------------------Start Table for first time page loading---------------------------------!-->

                    <table width="65%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
                        <form name="frmSearch" method="post" action="<?= $_SERVER['PHP_SELF'] ?>" onSubmit="javascript:return check();">
                            <input type="hidden" name="mode" value="excel_download">
                            <tr class="TDHEAD">
                                <td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
                            </tr>
                            <tr class="TDHEAD_SUB">
                                <td width="15%" align="center"></td>
                                <table width="65%" align="center" border="0" cellpadding="5" cellspacing="1" class="border">
                                    <?php
                                    $sql_check_surveytype = "SELECT survey_type, survey_type_details FROM acedns_acednsproduct.survey_form_details WHERE nick_name='" . $_SESSION['nick_name'] . "'";
                                    $res_check_surveytype = mysqli_query($link,$sql_check_surveytype);
                                    $row_check_surveytype = mysqli_fetch_assoc($res_check_surveytype);
                                    $survey_type = $row_check_surveytype['survey_type'];
                                    $survey_type_details = $row_check_surveytype['survey_type_details'];
                                    $survey_type_details_array = explode(",", $survey_type_details);
                                    if ($survey_type == 'yes') { ?>
                                        <tr>
                                            <td colspan="4" align="center">Select Type:
                                                <select name="survey_type">
                                                    <?php
                                                    foreach ($survey_type_details_array as $survey_type_value) {
                                                        if ($survey_type_value == 'mall') {
                                                            $name = 'Mall';
                                                            $value = $survey_type_value;
                                                        } else if ($survey_type_value == 'hi-street') {
                                                            $name = 'Hi Street';
                                                            $value = $survey_type_value;
                                                        }
                                                        echo "<option value=\"$value\">" . $name . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr id="datedropdown">
                                        <td align="left" width="15%">From Date:</td>
                                        <td align="left" width="30%" style="vertical-align:top;">
                                            <?php $from_date = $_REQUEST['from_date']; ?>
                                            <input id="textinput3" type="text" value="<?php echo str_replace('/', '-', $from_date); ?>" name="from_date"></input>&nbsp;
                                            <a href="javascript:cal5.popup();"><img style="cursor:hand;position:absolute;bsauda:0;" bsauda="0" src="images/cal.gif" width="20" height="18"></a>
                                            </label>
                                            <script language="JavaScript" type="text/javascript">
                                                <!-- // create calendar object(s) just after form tag closed
                                                // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
                                                // note: you can have as many calendar objects as you need for your application
                                                var cal5 = new calendar3(document.forms['frmSearch'].elements['from_date']);
                                                cal5.year_scroll = true;
                                                cal5.time_comp = false;
                                                //
                                                -->
                                            </script>
                                        </td>
                                        <td width="15%" align="left" style="padding-left:10px;">To Date:</td>
                                        <td width="" style="vertical-align:top;">
                                            <?php $to_date = $_REQUEST['to_date']; ?>
                                            <input id="textinput3" type="text" value="<?php echo str_replace('/', '-', $to_date); ?>" name="to_date"></input>&nbsp;
                                            <a href="javascript:cal6.popup();"><img style="cursor:hand;position:absolute;bsauda:0;" bsauda="0" src="images/cal.gif" width="20" height="18"></a>
                                            </label>
                                            <script language="JavaScript" type="text/javascript">
                                                <!-- // create calendar object(s) just after form tag closed
                                                // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
                                                // note: you can have as many calendar objects as you need for your application
                                                var cal6 = new calendar3(document.forms['frmSearch'].elements['to_date']);
                                                cal6.year_scroll = true;
                                                cal6.time_comp = false;
                                                //
                                                -->
                                            </script>
                                        </td>                                   </tr>

                                    <tr>
                                        <td colspan="4" align="center">Select Employee:<select name="search_emp_name">
                                                <option value="">Select</option>
                                                <option value="all">All</option>
                                                <?php
                                                $sql_select_emp = "SELECT emp_code, emp_name FROM employee_master  WHERE 1 $emp_hierarchy_condition ORDER BY emp_name ASC";
                                                $res_select_emp = mysqli_query($link,$sql_select_emp);
                                                while ($row_select_emp = mysqli_fetch_assoc($res_select_emp)) {
                                                    if ($_POST['search_emp_name'] == $row_select_emp['emp_code'])
                                                        echo "<option value='$row_select_emp[emp_code]' selected>" . $row_select_emp['emp_name'] . "</option>";
                                                    else
                                                        echo "<option value='$row_select_emp[emp_code]'>" . $row_select_emp['emp_name'] . "</option>";
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
                <?php } else {
                ?>
                    <script language="javascript">
                        function checked_all_branch() {
                            checkboxesbranch = document.getElementsByName('branch_code[]');
                            if (document.getElementById("all_checked_branch").checked == true) {
                                for (var i in checkboxesbranch)
                                    checkboxesbranch[i].checked = true;
                            } else {
                                for (var i in checkboxesbranch)
                                    checkboxesbranch[i].checked = false;
                            }
                        }

                        function checked_all_zone() {
                            checkboxeszone = document.getElementsByName('zone[]');
                            if (document.getElementById("all_checked_zone").checked == true) {
                                for (var i in checkboxeszone)
                                    checkboxeszone[i].checked = true;
                            } else {
                                for (var i in checkboxeszone)
                                    checkboxeszone[i].checked = false;
                            }
                        }

                        function checked_all_emp() {
                            checkboxesemp = document.getElementsByName('emp_code[]');
                            if (document.getElementById("all_checked_emp").checked == true) {
                                for (var i in checkboxesemp)
                                    checkboxesemp[i].checked = true;
                            } else {
                                for (var i in checkboxesemp)
                                    checkboxesemp[i].checked = false;
                            }
                        }
                        // function validation()
                        // {
                        // 	var is_checked=false;
                        // 	for(i=0; i<document.employee_entity.elements.length; i++){
                        // 		if(document.employee_entity.elements[i].type=="checkbox" && document.employee_entity.elements[i].checked==true 
                        // 				&& document.employee_entity.elements[i].name=='zone[]'){
                        // 			is_checked=true;
                        // 			break;
                        // 		}
                        // 	}
                        // 	if(!is_checked){
                        // 		alert("Please check at least one zone");
                        // 		return false;
                        // 	}

                        // 	//return true;
                        // }
                        function GetXmlHttpObject() {
                            var xmlHttp = null;
                            try {
                                // Firefox, Opera 8.0+, Safari
                                xmlHttp = new XMLHttpRequest();
                            } catch (e) {
                                // Internet Explorer
                                try {
                                    xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
                                } catch (e) {
                                    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
                                }
                            }
                            return xmlHttp;
                        }

                        function select_branch_details() {
                            checkboxes = document.getElementsByName('zone[]');
                            var valszone = '';
                            for (var i = 0, n = checkboxes.length; i < n; i++) {
                                if (checkboxes[i].checked == true) {
                                    valszone += "," + checkboxes[i].value;
                                }
                            }
                            if (valszone != '') {
                                valszone = valszone.substr(1);
                            }
                            /*if(valszone=='')
                            {
                            	alert('Please select at least one zone');
                            }*/
                            //alert(valszone);
                            xmlHttp = GetXmlHttpObject()
                            if (xmlHttp == null) {
                                alert("Browser does not support HTTP Request");
                                return
                            }
                            var url = "get_employee_entity_related_data.php?zone=" + valszone + "&data_type=fetch_branch";
                            xmlHttp.onreadystatechange = branchdetails;
                            xmlHttp.open("GET", url, true);
                            xmlHttp.send(null);
                        }

                        function branchdetails() {
                            if (xmlHttp.readyState == 4 || xmlHttp.readyState == "complete") {
                                var val = xmlHttp.responseText;
                                //alert(val);
                                if (val != "") {
                                    document.getElementById("show_branch").style.display = '';
                                    document.getElementById("showbranchdetails").innerHTML = val;
                                } else {
                                    document.getElementById("showbranchdetails").innerHTML = '';
                                    document.getElementById("showempdetails").innerHTML = '';
                                    document.getElementById("show_branch").style.display = 'none';
                                    document.getElementById("show_emp").style.display = 'none';
                                    document.getElementById("show_emp_access").style.display = 'none';
                                    document.getElementById("show_entity").style.display = 'none';
                                }
                            }
                        }

                        function select_emp_details() {
                            checkboxes = document.getElementsByName('branch_code[]');
                            if (document.getElementById("emp_access")) {
                                var emp_access = document.getElementById("emp_access").value;
                            } else {
                                var emp_access = '';
                            }
                            var valsbranch = '';
                            for (var i = 0, n = checkboxes.length; i < n; i++) {
                                if (checkboxes[i].checked == true) {
                                    valsbranch += "," + checkboxes[i].value;
                                }
                            }
                            if (valsbranch != '') {
                                valsbranch = valsbranch.substr(1);
                            }
                            /*if(valszone=='')
                            {
                            	alert('Please select at least one zone');
                            }*/
                            //alert(valszone);
                            xmlHttp = GetXmlHttpObject()
                            if (xmlHttp == null) {
                                alert("Browser does not support HTTP Request");
                                return
                            }
                            var url = "get_employee_entity_related_data.php?branch_code=" + valsbranch + "&data_type=fetch_emp&emp_access=" + emp_access;
                            xmlHttp.onreadystatechange = empdetails;
                            xmlHttp.open("GET", url, true);
                            xmlHttp.send(null);
                        }

                        function empdetails() {
                            if (xmlHttp.readyState == 4 || xmlHttp.readyState == "complete") {
                                var val = xmlHttp.responseText;
                                //alert(val);
                                if (val != "") {
                                    document.getElementById("show_emp").style.display = '';
                                    document.getElementById("show_emp_access").style.display = '';
                                    document.getElementById("showempdetails").innerHTML = val;
                                } else {
                                    document.getElementById("showempdetails").innerHTML = '';
                                    document.getElementById("show_emp").style.display = 'none';
                                    document.getElementById("show_emp_access").style.display = 'none';
                                    document.getElementById("show_entity").style.display = 'none';
                                }
                            }
                        }

                        function check() {
                            var is_checked = false;
                            for (i = 0; i < document.frmSearch.elements.length; i++) {
                                if (document.frmSearch.elements[i].type == "checkbox" && document.frmSearch.elements[i].checked == true &&
                                    document.frmSearch.elements[i].name == 'zone[]') {
                                    is_checked = true;
                                    break;
                                }
                            }
                            if (!is_checked) {
                                alert("Please check at least one zone");
                                return false;
                            }
                            var is_checked_branch = false;
                            for (i = 0; i < document.frmSearch.elements.length; i++) {
                                if (document.frmSearch.elements[i].type == "checkbox" && document.frmSearch.elements[i].checked == true &&
                                    document.frmSearch.elements[i].name == 'branch_code[]') {
                                    is_checked_branch = true;
                                    break;
                                }
                            }
                            if (!is_checked_branch) {
                                alert("Please check at least one branch");
                                return false;
                            }
                            var is_checked_emp = false;
                            for (i = 0; i < document.frmSearch.elements.length; i++) {
                                if (document.frmSearch.elements[i].type == "checkbox" && document.frmSearch.elements[i].checked == true &&
                                    document.frmSearch.elements[i].name == 'emp_code[]') {
                                    is_checked_emp = true;
                                    break;
                                }
                            }
                            if (!is_checked_emp) {
                                alert("Please check at least one employee");
                                return false;
                            } else {
                                checkboxesemp = document.getElementsByName('emp_code[]');
                                var valsemp = '';
                                for (var i = 0, n = checkboxesemp.length; i < n; i++) {
                                    if (checkboxesemp[i].checked == true) {
                                        valsemp += "," + checkboxesemp[i].value;
                                    }
                                }
                                valsemp = valsemp.substr(1);
                                document.getElementById("emp_code_value").value = valsemp;
                            }

                            if (document.frmSearch.from_date.value.search(/\S/) == 0) {
                                if (document.frmSearch.to_date.value.search(/\S/) == -1) {
                                    alert('Please input a vlaue for To Date.');
                                    document.frmSearch.to_date.focus();
                                    return false;
                                }
                            }
                            return true;
                        }
                    </script>

                    <table cellpadding="4px" width="50%" class="border" align="center">
                        <tr class="TDHEAD_SUB">
                            <td align="center">Survey Download</td>
                        </tr>
                        <tr>
                            <td align="center">
                                <form name="frmSearch" method="post" action="<?= $_SERVER['PHP_SELF'] ?>" onSubmit="javascript:return check();">
                                    <input type="hidden" name="mode" value="excel_download">
                                    <input type="hidden" name="emp_code_value" id="emp_code_value" value="" />
                                    <table cellpadding="4px">

                                        <tr>
                                            <td align="right" width="25%" valign="top">Select Zone:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
                                            <td align="left">
                                                <div style="max-height:100px; overflow-y: scroll;">
                                                    <?php $zone = $_REQUEST['zone']; ?>
                                                    <table>
                                                        <tr>
                                                            <!--td align="left">
                                         <input type="checkbox" name="all_checked_zone" id="all_checked_zone" value="allzone" onChange="javascript:checked_all_zone();select_branch_details();"/>ALL
                                    </td-->
                                                        </tr>
                                                        <?php
                                                        $sqlzone = "SELECT DISTINCT zone FROM employee_master WHERE zone!='' AND zone IS NOT NULL ORDER BY zone ASC";
                                                        $rszone = mysqli_query($link,$sqlzone);
                                                        $cnt = 0;
                                                        while ($rowzone = mysqli_fetch_assoc($rszone)) {
                                                            $cnt++;
                                                        ?>
                                                            <tr>
                                                                <td align="left">
                                                                    <input type="checkbox" name="zone[]" value="<?php echo $rowzone['zone']; ?>" onchange="javascript:select_branch_details();" /><?php echo $rowzone['zone']; ?>
                                                                </td>
                                                            </tr>
                                                        <?php
                                                        }
                                                        ?>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right" width="25%" valign="top">Select Branch:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
                                            <td align="left">
                                                <div style="max-height:100px; overflow-y: scroll;display:none;" id="show_branch">
                                                    <?php $branch_code = $_REQUEST['branch_code']; ?>
                                                    <table>
                                                        <tr>
                                                            <!--td align="left">
                                            <input type="checkbox" name="all_checked_branch" id="all_checked_branch" value="allbranch" onChange="javascript:checked_all_branch();select_emp_details();"/>ALL
                                        </td-->
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <table id="showbranchdetails"></table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right" width="25%" valign="top">Employee Access:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
                                            <td align="left">
                                                <div style="max-height:100px; overflow-y: scroll;display:none;" id="show_emp_access">
                                                    <?php $emp_code = $_REQUEST['emp_code']; ?>
                                                    <select name="emp_access" id="emp_access" onchange="javascript:select_emp_details();">
                                                        <option value="">All</option>
                                                        <option value="Y">Active</option>
                                                        <option value="N">In Active</option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right" width="25%" valign="top">Select Employee:<font color="#FF0000">*</font>&nbsp;&nbsp;</td>
                                            <td align="left">
                                                <div style="max-height:100px; overflow-y: scroll;display:none;" id="show_emp">
                                                    <?php $emp_code = $_REQUEST['emp_code']; ?>
                                                    <table>
                                                        <tr>
                                                            <td align="left">
                                                                <input type="checkbox" name="all_checked_emp" id="all_checked_emp" value="allemp" onChange="javascript:checked_all_emp();" />ALL
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <table id="showempdetails"></table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right" width="25%">From Date:</td>
                                            <td align="left">
                                                <?php $from_date = $_REQUEST['from_date']; ?>
                                                <input id="textinput3" type="text" value="<?php echo str_replace('/', '-', $from_date); ?>" name="from_date"></input>&nbsp;
                                                <a href="javascript:cal5.popup();"><img style="cursor:hand;position:absolute;bsauda:0;" bsauda="0" src="images/cal.gif" width="20" height="18"></a>
                                                </label>
                                                <script language="JavaScript" type="text/javascript">
                                                    <!-- // create calendar object(s) just after form tag closed
                                                    // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
                                                    // note: you can have as many calendar objects as you need for your application
                                                    var cal5 = new calendar3(document.forms['frmSearch'].elements['from_date']);
                                                    cal5.year_scroll = true;
                                                    cal5.time_comp = false;
                                                    //
                                                    -->
                                                </script>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right" width="25%">To Date:</td>
                                            <td align="left">
                                                <?php $to_date = $_REQUEST['to_date']; ?>
                                                <input id="textinput3" type="text" value="<?php echo str_replace('/', '-', $to_date); ?>" name="to_date"></input>&nbsp;
                                                <a href="javascript:cal6.popup();"><img style="cursor:hand;position:absolute;bsauda:0;" bsauda="0" src="images/cal.gif" width="20" height="18"></a>
                                                </label>
                                                <script language="JavaScript" type="text/javascript">
                                                    <!-- // create calendar object(s) just after form tag closed
                                                    // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
                                                    // note: you can have as many calendar objects as you need for your application
                                                    var cal6 = new calendar3(document.forms['frmSearch'].elements['to_date']);
                                                    cal6.year_scroll = true;
                                                    cal6.time_comp = false;
                                                    //
                                                    -->
                                                </script>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td align="left"><input name="submit" type="submit" value="Submit" id="submitdata"></td>
                                        </tr>
                                    </table>
                                </form>
                            </td>
                        </tr>
                    </table>
                <?php
            }
                ?>
                <br />
            <?php } //End of main()
        function excelDownload()
        {
			require("include/dbcon.php");
            $from_date = $_REQUEST['from_date'];
            $to_date = $_REQUEST['to_date'];
            $from_date = date('Y-m-d', strtotime($from_date));
            $to_date = date('Y-m-d', strtotime($to_date));
            $survey_type = $_REQUEST['survey_type'];
            if ($from_date != '' && $to_date != '') {
                $date_condition = " AND DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') >='" . $from_date . "' AND 
					  	DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') <='" . $to_date . "'";
            }
            if (strtoupper($_SESSION['nick_name']) != 'DURO') {
                $search_emp_name = $_REQUEST['search_emp_name'];
                if ($search_emp_name != '' && $search_emp_name != 'all')
                    $emp_condition = " AND EM.emp_code = '" . $search_emp_name . "' ";
                else if ($search_emp_name == 'all')
                    $emp_condition = '';
                else
                    $emp_condition = "";
            } else {
                $emp_code = $_REQUEST['emp_code_value'];
                $emp_code_array = explode(",", $emp_code);
                $emp_code_string = '';
                foreach ($emp_code_array as $emp_code_val) {
                    $emp_code_string = $emp_code_string . "'" . $emp_code_val . "'" . ',';
                }
                $emp_code_string = substr($emp_code_string, 0, -1);
                $emp_condition = " AND EM.emp_code IN(" . $emp_code_string . ") ";
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
                    $this->doWrite = true;
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
                    if ($this->doWrite) {
                        echo $fr;
                    } else {                     // ... OR add this entry to array
                        $this->datasec[] = $fr;
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

                    $cdrec .= pack('V', $this->old_offset); // relative offset of local header
                    $this->old_offset += strlen($fr);

                    $cdrec .= $name;

                    // optional extra field, file comment goes here
                    // save to central directory
                    $this->ctrl_dir[] = $cdrec;
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
                    $ctrldir = implode('', $this->ctrl_dir);
                    $header = $ctrldir .
                        $this->eof_ctrl_dir .
                        pack('v', sizeof($this->ctrl_dir)) . //total #of entries "on this disk"
                        pack('v', sizeof($this->ctrl_dir)) . //total #of entries overall
                        pack('V', strlen($ctrldir)) .          //size of central dir
                        pack('V', $this->old_offset) .       //offset to start of central dir
                        "\x00\x00";                            //.zip file comment length

                    if ($this->doWrite) { // Send central directory & end ctrl dir to STDOUT
                        echo $header;
                        return "";            // Return empty string
                    } else {                  // Return entire ZIP archive as string
                        $data = implode('', $this->datasec);
                        return $data . $header;
                    }
                } // end of the 'file()' method

            } // end of the 'ZipFile' class

            //For survey excel download
            $sql_check_surveytype = "SELECT survey_type, survey_type_details,survey_menu FROM acedns_acednsproduct.survey_form_details WHERE 
								nick_name='" . strtoupper($_SESSION['nick_name']) . "'";
            $res_check_surveytype = mysqli_query($link,$sql_check_surveytype);
            $row_check_surveytype = mysqli_fetch_assoc($res_check_surveytype);
            $survey_type = $row_check_surveytype['survey_type'];
            $survey_type_details = $row_check_surveytype['survey_type_details'];
            $survey_menu = $row_check_surveytype['survey_menu'];
            if ($survey_type == 'no') {
                if ($survey_menu == 'yes') {
                    $sql_get_menu = "SELECT layout_name,menu_id,survey_sub_menu FROM survey_input WHERE type='menu'  ORDER BY display_order ASC";
                    $res_get_menu = mysqli_query($link,$sql_get_menu);
                    $count_menu = mysqli_num_rows($res_get_menu);
                    $menu_no = 1;
                    $menu_id_array = array();
                    $menu_name_array = array();
                    $survey_sub_menu_array = array();

                    while ($row_get_menu = mysqli_fetch_assoc($res_get_menu)) {
                        $menu_name = $row_get_menu['layout_name'];
                        $menu_id = $row_get_menu['menu_id'];
                        $survey_sub_menu = $row_get_menu['survey_sub_menu'];
                        array_push($menu_id_array, $menu_id);
                        array_push($menu_name_array, $menu_name);
                        array_push($survey_sub_menu_array, $survey_sub_menu);
                        ${'excelheader' . $menu_id} = '' . "\t" . '' . "\t" . '' . "\t" . "\t" . "\t";
                        ${'excelsubheader' . $menu_id} = 'Sr. No' . "\t" . 'Unique Store ID' . "\t" . 'Emp Name' . "\t" . 'Survey Date' . "\t" . 'Lattitude' . "\t" . 'Longitude' . "\t";
                        if (strtoupper($_SESSION['nick_name']) == 'DURO' && $menu_id == 'RA002') {
                            ${'excelsubheader_one' . $menu_id} = '' . "\t" . '' . "\t" . '' . "\t" . '' . "\t" . '' . "\t" . '' . "\t";
                        }
                        ${'sl_no' . $menu_id} = 1;
                        $row_id_string = '';
                        $row_id_string_SET = '';
                        $valueexcel = '';
                        ${'survey_id_array' . $menu_id} = array();

                        $sql_get_display = "SELECT display_name,row_id,action,display_order FROM survey_input WHERE  type!='menu' AND menu_id='" . $menu_id . "'  AND acedns='Y'
				   					ORDER BY display_order ASC";
                        $res_get_display = mysqli_query($link,$sql_get_display);
                        $count_display = mysqli_num_rows($res_get_display);
                        for ($k = 0; $k < $count_display; $k++) {
                            //echo 'A';
                            ${'excelheader' . $menu_id} .= "\t";
                        }
                        $display_no = 1;
                        while ($row_get_display = mysqli_fetch_assoc($res_get_display)) {
                            $display_name = $row_get_display['display_name'];
                            /*$action=$row_get_display['action'];
						if($action!='')
						{
							$display_name_array=explode('#',$action);
							$display_name=$display_name_array[0];
						}*/
                            $display_id = $row_get_display['row_id'];
                            if (strtoupper($_SESSION['nick_name']) == 'DURO' && $menu_id == 'RA002') {
                                $display_order = $row_get_display['display_order'];
                                if ($display_id == 'RA059') {
                                    ${'display_order' . $display_id} = $row_get_display['display_order'];
                                    ${'excelsubheader' . $menu_id} .= $display_name . "\t" . '' . "\t" . '' . "\t" . '' . "\t" . '' . "\t";
                                    ${'excelsubheader_one' . $menu_id} .= "\t" . 'Category' . "\t" . 'Qty' . "\t" . 'Value' . "\t" . 'Desc' . "\t" . 'Spices' . "\t";
                                } else if ($display_id == 'RA085') {
                                    ${'excelsubheader' . $menu_id} .= $display_name . "\t" . 'Name' . "\t";
                                    ${'excelsubheader_one' . $menu_id} .= '' . "\t";
                                    ${'excelblankcontent' . $menu_id} .= '' . "\t";
                                } else {
                                    ${'excelsubheader' . $menu_id} .= $display_name . "\t";
                                    ${'excelsubheader_one' . $menu_id} .= '' . "\t";
                                    ${'excelblankcontent' . $menu_id} .= '' . "\t";
                                }
                                if (($display_order >  ${'display_order' . $display_id}) && ${'display_order' . $display_id} != '') {
                                    ${'excelblankcontent_next' . $menu_id} .= '' . "\t";
                                }
                            } else if (strtoupper($_SESSION['nick_name']) == 'DURO' && $menu_id == 'RA035') {
                                if ($display_id == 'RA047') {
                                    ${'excelsubheader' . $menu_id} .= 'Facilitator name' . "\t" . 'Type' . "\t";
                                } else {
                                    ${'excelsubheader' . $menu_id} .= $display_name . "\t";
                                }
                            } else {
                                ${'excelsubheader' . $menu_id} .= $display_name . "\t";
                            }
                            $row_id_string .= "'" . $display_id . "'" . ',';
                            $row_id_string_SET .= $display_id . ',';
                            $display_no++;
                        }
                        $row_id_string = substr($row_id_string, 0, -1);
                        $row_id_string_SET = substr($row_id_string_SET, 0, -1);

                        if (strtoupper($_SESSION['nick_name']) == 'DURO') {
							if($row_id_string_SET!=''){
                            $sql_survey_existing_row_id = "SELECT *,DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%d-%m-%Y') AS survey_date FROM survey_output SO, employee_master EM WHERE SO.row_id IN(" . $row_id_string. ") " . $date_condition . " AND SUBSTRING(SO.survey_id,3,5)=EM.emp_code" . $emp_condition . " 
									  ORDER BY SO.survey_id DESC,FIND_IN_SET(SO.row_id,'" . $row_id_string_SET . "') ";
                            $rs_survey_existing_row_id = mysqli_query($link,$sql_survey_existing_row_id);
                            while ($row_survey_existing_row_id = mysqli_fetch_assoc($rs_survey_existing_row_id)) {
                                $survey_id_existing = $row_survey_existing_row_id['survey_id'];
                                ${'row_id_string' . $survey_id_existing} .= "'" . $row_survey_existing_row_id['row_id'] . "'" . ',';
                            }
							}
                        }
                        //echo ${row_id_string.'SUE016320201014100700'};
						if($row_id_string_SET!=''){
                        $sql_survey_output = "SELECT *,DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%d-%m-%Y') AS survey_date FROM survey_output SO, employee_master EM WHERE SO.row_id IN(" . $row_id_string . ") " . $date_condition . " AND SUBSTRING(SO.survey_id,3,5)=EM.emp_code" . $emp_condition . " 
									  ORDER BY SO.survey_id DESC,FIND_IN_SET(SO.row_id,'" . $row_id_string_SET . "') ";
                        $rs_survey_output = mysqli_query($link,$sql_survey_output);
                        $output_no = 1;
                        while ($row_survey_output = mysqli_fetch_assoc($rs_survey_output)) {
                            $survey_id = $row_survey_output['survey_id'];

                            $survey_date = date("d-M-Y", strtotime($row_survey_output['survey_date']));
                            $value = $row_survey_output['value'];
                            if (strpos($value, "#") == true && strtoupper($_SESSION['nick_name']) != 'DURO') {
                                $valuearray = explode("#", $value);
                                $value = $valuearray[0];
                            }
                            if (!in_array($survey_id, ${'survey_id_array' . $menu_id})) {
                                $sqllatlong = "SELECT EM.emp_name,LO.latt,LO.longi FROM location LO,employee_master EM 
						 			WHERE LO.emp_code=EM.emp_code AND LO.trans_id='" . $survey_id . "'";
                                $rslatlong = mysqli_query($link,$sqllatlong);
                                $rowlatlong = mysqli_fetch_assoc($rslatlong);
                                $lattitude = $rowlatlong['latt'];
                                $longitude = $rowlatlong['longi'];
                                $emp_name = $rowlatlong['emp_name'];
                                //if($output_no>1)  ${valueexcel.$survey_id}.="\n";
                                ${'valueexcel' . $survey_id . $menu_id} .= ${'sl_no' . $menu_id} . "\t";
                                ${'valueexcel' . $survey_id . $menu_id} .= $survey_id . "\t";
                                ${'valueexcel' . $survey_id . $menu_id} .= $emp_name . "\t";
                                ${'valueexcel' . $survey_id . $menu_id} .= $survey_date . "\t";
                                ${'valueexcel' . $survey_id . $menu_id} .= $lattitude . "\t";
                                ${'valueexcel' . $survey_id . $menu_id} .= $longitude . "\t";
                                array_push(${'survey_id_array' . $menu_id}, $survey_id);
                                ${'sl_no' . $menu_id}++;
                            }
                            $sqlmasterview = "SELECT type,display_table_name,insert_table_detail FROM survey_input WHERE  row_id='" . $row_survey_output['row_id'] . "'";
                            $resmasterview = mysqli_query($link,$sqlmasterview);
                            $rowmasterview = mysqli_fetch_assoc($resmasterview);
                            $type = $rowmasterview['type'];
                            $insert_table_detail = $rowmasterview['insert_table_detail'];
                            $insert_table_detail_parts = explode("#", $insert_table_detail);
                            if ($type == 'masterview') {
                                $display_table_name = $rowmasterview['display_table_name'];
                                $dispaly_table_name_partsone = explode('#', $display_table_name);
                                $dispaly_table_name_partstwo = explode('%', $dispaly_table_name_partsone[1]);
                                if (strpos($dispaly_table_name_partstwo[1], '&') != false) {
                                    $dispaly_table_name_partstwo_sub = explode('&', $dispaly_table_name_partstwo[1]);
                                    $dispaly_table_name_partstwo[1] = $dispaly_table_name_partstwo_sub[0];
                                }
                                if ($dispaly_table_name_partsone[0] == 'emp_master') $dispaly_table_name_partsone[0] = 'employee_master';
                                //$value = str_replace(";", ",", $value);
								
								if(strpos($value,';')!=false){
									$valueparts=explode(";",$value);
									$value=$valueparts[1];
								}
                                $sqlfetchval = "SELECT GROUP_CONCAT($dispaly_table_name_partstwo[1] SEPARATOR ';') AS fetch_value 
								FROM $dispaly_table_name_partsone[0] WHERE FIND_IN_SET($dispaly_table_name_partstwo[0],'" . $value . "')";
                                $rsfetchval = mysqli_query($link,$sqlfetchval);
                                $rowfetchval = mysqli_fetch_assoc($rsfetchval);
                                $value = $rowfetchval['fetch_value'];
                                /*if($survey_id=='SUE009120201127214438' &&  $row_survey_output['row_id']=='RA024')
						{
							echo $sqlfetchval;
							exit();
						}*/
                            }
                            if ($insert_table_detail_parts[0] == 'insert') {
                                $valueparts = explode(";", $value);
                                $value = $valueparts[0];
                            }
                            /*if($row_survey_output['row_id']=='RA003')
					{
						$valueparts=explode(";",$value);
						$value=$valueparts[0];
					}*/

                            if ($value == '') $value = $row_survey_output['value'];
                            if (strtoupper($_SESSION['nick_name']) == 'DURO') {
                                ${'row_id_string' . $survey_id} = str_replace("'", "", substr(${'row_id_string' . $survey_id}, 0, -1));
                                ${'row_id_array' . $survey_id} = explode(",", ${'row_id_string' . $survey_id});
                                if ($row_survey_output['row_id'] == 'RA016' || $row_survey_output['row_id'] == 'RA017' || $row_survey_output['row_id'] == 'RA096' || $row_survey_output['row_id'] == 'RA097') {
                                    $value = str_replace('YES:', '', $value);
                                    $value = str_replace('NO', '', $value);
                                } else if ($row_survey_output['row_id'] == 'RA006') {
                                    if (!in_array('RA108', ${'row_id_array' . $survey_id})) {
                                        //echo 'a';
                                        $value = $value . "\t" . "";
                                    } else {
                                        $value = $value;
                                    }
                                    //echo 'b';
                                } else if ($row_survey_output['row_id'] == 'RA027') {
                                    if (!in_array('RA109', ${'row_id_array' . $survey_id})) {
                                        //echo 'a';
                                        $value = $value . "\t" . "";
                                    } else {
                                        $value = $value;
                                    }
                                    //echo 'b';
                                } else if ($row_survey_output['row_id'] == 'RA030') {
                                    if (!in_array('RA107', ${'row_id_array' . $survey_id})) {
                                        //echo 'a';
                                        $value = $value . "\t" . "";
                                    } else {
                                        $value = $value;
                                    }
                                    //echo 'b';
                                } else if ($row_survey_output['row_id'] == 'RA037') {
                                    if (!in_array('RA120', ${'row_id_array' . $survey_id})) {
                                        //echo 'a';
                                        $value = $value . "\t" . "";
                                    } else {
                                        $value = $value;
                                    }
                                    //echo 'b';
                                } else if ($row_survey_output['row_id'] == 'RA047') {
                                    $valueparts = explode(";", $value);
                                    $sqlfacilitatortype = "SELECT f_type FROM facilitator_master WHERE f_code='" . $valueparts[1] . "'";
                                    $rsfacilitatortype = mysqli_query($link,$sqlfacilitatortype);
                                    $rowfacilitatortype = mysqli_fetch_assoc($rsfacilitatortype);
                                    $f_type = $rowfacilitatortype['f_type'];
                                    //$value=$valueparts[0].' - '.$f_type;
                                    $value = $valueparts[0] . "\t" . $f_type;
                                } else if ($row_survey_output['row_id'] == 'RA053') {
                                    if (!in_array('RA110', ${'row_id_array' . $survey_id})) {
                                        //echo 'a';
                                        $value = $value . "\t" . "";
                                    } else {
                                        $value = $value;
                                    }
                                    if (!in_array('RA111', ${'row_id_array' . $survey_id})) {
                                        //echo 'a';
                                        $value = $value . "\t" . "";
                                    } else {
                                        $value = $value;
                                    }
                                    //echo 'b';
                                } else if ($row_survey_output['row_id'] == 'RA077') {
                                    $valueparts = explode(";", $value);
                                    $value = $valueparts[0];
                                } else if ($row_survey_output['row_id'] == 'RA085') {
                                    $valueparts = explode(":", $value);
                                    if (strtolower($valueparts[0]) != 'office' && count($valueparts) > 0) {
                                        $sqlfacilitatorname = "SELECT facilitator_name FROM facilitator_master WHERE f_code='" . $valueparts[1] . "'";
                                        $rsfacilitatorname = mysqli_query($link,$sqlfacilitatorname);
                                        $rowfacilitatorname = mysqli_fetch_assoc($rsfacilitatorname);
                                        $facilitator_name = $rowfacilitatorname['facilitator_name'];
                                        $value = $valueparts[0] . "\t" . $facilitator_name;
                                    } else if (strtolower($valueparts[0]) == 'office' && count($valueparts) > 0) {
                                        $value = $valueparts[0] . "\t" . $valueparts[1];
                                    } else {
                                        $value = $value . "\t" . "";
                                    }
                                } else if ($row_survey_output['row_id'] == 'RA098' || $row_survey_output['row_id'] == 'RA073' || $row_survey_output['row_id'] == 'RA075') {
                                    if ($value != '') {
                                        $value = date("d-M-Y", strtotime(substr(str_replace("/", "-", $value), 0, 10)));
                                    } else {
                                        $value = $value;
                                    }
                                } else if ($row_survey_output['row_id'] == 'RA090') {
                                    $sqltableview = "SELECT value FROM table_view WHERE row_id='" . $row_survey_output['row_id'] . "'";
                                    $rstableview = mysqli_query($link,$sqltableview);
                                    $rowtableview = mysqli_fetch_assoc($rstableview);
                                    $table_view_val = explode("/", $rowtableview['value']);
                                    foreach ($table_view_val as $table_view_value) {
                                        if (strpos($value, strtoupper($table_view_value)) !== false) {
                                            $value = strtoupper($table_view_value);
                                        }
                                    }
                                } else if ($row_survey_output['row_id'] == 'RA059' && $value != '') {
                                    $product_cat_first_part = explode("$", $value);
                                    //echo $survey_id;
                                    //echo '<br />';
                                    $countfirstpart = 1;
                                    foreach ($product_cat_first_part as $product_cat_first_part_val) {
                                        $TD_count = 1;
                                        if ($countfirstpart > 1) {
                                            //echo 'aaaaaaaaaaaaaaaaa';
                                            ${'valueexcel' . $survey_id . $menu_id . $row_survey_output['row_id']} .= "\n" . '' . "\t" . '' . "\t" . '' . "\t" . '' . "\t" . '' . "\t" . '' . ${'excelblankcontent' . $menu_id};
                                        }
                                        $product_cat_second_part = explode("#", $product_cat_first_part_val);
                                        //print_r($product_cat_second_part);
                                        $product_cat_second_part_sub_val = explode(":", $product_cat_second_part[0]);
                                        ${'valueexcel' . $survey_id . $menu_id . $row_survey_output['row_id']} .= $product_cat_second_part_sub_val[0] . "\t";
                                        for ($TD_count = 0; $TD_count < 4; $TD_count++) //FOr qty , value , Desc and species
                                        {
                                            if ($TD_count == 0) {
                                                $product_cat_second_part_sub_val = explode(":", $product_cat_second_part[$TD_count]);
                                                ${'valueexcel' . $survey_id . $menu_id . $row_survey_output['row_id']} .= $product_cat_second_part_sub_val[1];
                                            } else {
                                                ${'valueexcel' . $survey_id . $menu_id . $row_survey_output['row_id']} .= $product_cat_second_part[$TD_count];
                                            }
                                            if ($TD_count < 3) {
                                                ${'valueexcel' . $survey_id . $menu_id . $row_survey_output['row_id']} .= "\t";
                                            }
                                        }
                                        //echo $survey_id;
                                        //echo '<br />';
                                        if ($countfirstpart >= 1 && $countfirstpart != count($product_cat_first_part)) {
                                            ${'valueexcel' . $survey_id . $menu_id . $row_survey_output['row_id']} .= "\t" . ${'excelblankcontent_next' . $menu_id};
                                        }
                                        $countfirstpart++;
                                        //${'valueexcel'.$survey_id.$menu_id.$row_survey_output['row_id']}.=''."\t"."\n";
                                    }
                                    $value = ${'valueexcel' . $survey_id . $menu_id . $row_survey_output['row_id']};
                                    //echo '<br />';
                                } else if ($row_survey_output['row_id'] == 'RA059' && $value == '') {
                                    $value = '' . "\t" . '' . "\t" . '' . "\t" . '' . "\t" . '';
                                }
                                //exit();
                            }  //End of Product Category
                            if (strtoupper($_SESSION['nick_name']) == 'SAI' ) {
                                if ($row_survey_output['row_id'] == 'RA017' || $row_survey_output['row_id'] == 'RA023') {
                                    if ($value != '') {
                                        $imageval = $value;
                                        $imageval = ltrim($imageval, " ");
                                        $imageval = rtrim($imageval, " ");
                                        $imageval = rtrim($imageval, ";");
                                        $imageval = str_replace('.JPEG', '.jpeg', $imageval);
                                        //$image_string= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$imageval."\" target=\"_blank\" style=\"color:brown;\">http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$imageval."</a>";

                                        $value = "http://salesmpower.acedns.in/upload/" . strtoupper($_SESSION['nick_name']) . "/" . $imageval;
                                    } else {
                                        $value = $value;
                                    }
                                }
                            }
                            
                            
                                if(strtoupper($_SESSION['nick_name']) == 'SHAKTI'){
                                    if ($row_survey_output['row_id'] == 'RA044' || $row_survey_output['row_id'] == 'RA022') {
                                    if ($value != '') {
                                        $imageval = $value;
                                        $imageval = ltrim($imageval, " ");
                                        $imageval = rtrim($imageval, " ");
                                        $imageval = rtrim($imageval, ";");
                                        $imageval = str_replace('.JPEG', '.jpeg', $imageval);
                                        $site_image_array = explode(";", $imageval);

                                        foreach ($site_image_array as $image) {
                                            $image = ltrim($image, " ");
                                            if ($image != '') {
                                                //$image_string= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$imageval."\" target=\"_blank\" style=\"color:brown;\">http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$imageval."</a>";

                                                $value = "http://salesmpower.acedns.in/upload/" . strtoupper($_SESSION['nick_name']) . "/" . $image;
                                            }
                                        }
                                    }
                                }
                                }

                            ${'valueexcel' . $survey_id . $menu_id} .= $value . "\t";
                            //${datavalue.$menu_id}.=${'valueexcel'.$survey_id};
                            $output_no++;
                        }
						}
                        $menu_no++;
							
                    }
                    //exit();
                    //print_r($survey_id_array);
                    //$menu_id_stat='RA115';
                    //echo ${excelheader.$menu_id_stat};
                    //echo  ${datavalue.$menu_id_stat};
                    $zip = new ZipFile();
                    for ($i = 0; $i < count($menu_id_array); $i++) {
                        //${dataorderheader.$rds_code_array[$i]} = ${lineorderheader.$rds_code_array[$i]}.${lineorderheader_freight.$rds_code_array[$i]} ;
                        for ($m = 0; $m < count(${'survey_id_array' . $menu_id_array[$i]}); $m++) {
                            ${'datavalue' . $menu_id_array[$i]} .= ${'valueexcel' . ${'survey_id_array' . $menu_id_array[$i]}[$m] . $menu_id_array[$i]} . "\n";
                        }
                        if (${'datavalue' . $menu_id_array[$i]} == "") {
                            ${'datavalue' . $menu_id_array[$i]} = "\r\n(0) Records Found!\n";
                        }

                        $date = gmdate('d', strtotime('+330 minute'));
                        $month = gmdate('m', strtotime('+330 minute'));
                        $year = gmdate('Y', strtotime('+330 minute'));
                        $hour = gmdate('H', strtotime('+330 minute'));
                        $minute = gmdate('i', strtotime('+330 minute'));
                        $second = gmdate('s', strtotime('+330 minute'));

                        if (strtoupper($_SESSION['nick_name']) == 'DURO' && $menu_id_array[$i] == 'RA002') {
                            ${'file_content' . $menu_id_array[$i]} = ${'excelheader' . $menu_id_array[$i]} . "\n" . ${'excelsubheader' . $menu_id_array[$i]} . "\n" . ${'excelsubheader_one' . $menu_id_array[$i]} . "\n" . ${'datavalue' . $menu_id_array[$i]};
                        } else {
                            ${'file_content' . $menu_id_array[$i]} = ${'excelheader' . $menu_id_array[$i]} . "\n" . ${'excelsubheader' . $menu_id_array[$i]} . "\n" . ${'datavalue' . $menu_id_array[$i]};
                        }
                        ${'file_name' . $menu_id_array[$i]} = "$menu_name_array[$i]_$survey_sub_menu_array[$i]_$date$month$year$hour$minute$second.xls";

                        //add files to the zip, passing file contents, not actual files
                        //$zip->addFile($file_content1, $file_name1);
                        $zip->addFile(${'file_content' . $menu_id_array[$i]}, ${'file_name' . $menu_id_array[$i]});
                    }

                    //For SAI client menu
                    if (strtoupper($_SESSION['nick_name']) == 'SAI' || strtoupper($_SESSION['nick_name']) == 'SHAKTI') {
                        $sql_get_menu = "SELECT DISTINCT survey_sub_menu FROM survey_input WHERE survey_sub_menu!='KYC' ORDER BY survey_sub_menu ASC";
                        $res_get_menu = mysqli_query($link,$sql_get_menu);
                        $count_menu = mysqli_num_rows($res_get_menu);
                        $menu_no = 1;
                        //$menu_id_array=array();
                        //$menu_name_array=array();
                        $survey_sub_menu_array = array();
                        while ($row_get_menu = mysqli_fetch_assoc($res_get_menu)) {
                            //$menu_name=$row_get_menu['layout_name'];
                            //$menu_id=$row_get_menu['menu_id'];
                            $survey_sub_menu = $row_get_menu['survey_sub_menu'];
                            //array_push($menu_id_array,$menu_id);
                            //array_push($menu_name_array,$menu_name);
                            array_push($survey_sub_menu_array, $survey_sub_menu);
                            ${'excelheader' . $survey_sub_menu} = '' . "\t" . '' . "\t" . '' . "\t" . "\t" . "\t";
                            ${'excelsubheader' . $survey_sub_menu} = 'Sr. No' . "\t" . 'Unique Store ID' . "\t" . 'Survey Date' . "\t" . 'Check In Time' . "\t" . 'Check Out Time' . "\t" . 'Employee' . "\t";
                            ${'sl_no' . $survey_sub_menu} = 1;
                            $row_id_string = '';
                            $row_id_string_SET = '';
                            $valueexcel = '';
                            ${'survey_id_array' . $survey_sub_menu} = array();

                            // ${array_header.$menu_id}=array();
                            //$sql_get_display="SELECT display_name,row_id,action FROM survey_input WHERE type!='menu' AND menu_id='".$menu_id."' ORDER BY display_order ASC";
                            $sql_get_display = "SELECT display_name,row_id,action FROM survey_input WHERE 
								type!='menu' AND survey_sub_menu='" . $survey_sub_menu . "' AND acedns='Y' ORDER BY display_order ASC";
                            $res_get_display = mysqli_query($link,$sql_get_display);
                            $count_display = mysqli_num_rows($res_get_display);
                            for ($k = 0; $k < $count_display; $k++) {
                                //echo 'A';
                                ${'excelheader' . $survey_sub_menu} .= "\t";
                            }

                            $display_no = 1;
                            while ($row_get_display = mysqli_fetch_assoc($res_get_display)) {
                                $display_name = $row_get_display['display_name'];
                                $action = $row_get_display['action'];
                                /*if($action!='')
							{
								$display_name_array=explode('#',$action);
								$display_name=$display_name_array[0];
							}*/
                                $display_id = $row_get_display['row_id'];
                                ${'excelsubheader' . $survey_sub_menu} .= $display_name . "\t";
                                $row_id_string .= "'" . $display_id . "'" . ',';
                                $row_id_string_SET .= $display_id . ',';
                                $display_no++;
                            }
                            //$layer_no++;

                            $row_id_string = substr($row_id_string, 0, -1);
                            $row_id_string_SET = substr($row_id_string_SET, 0, -1);
                            $sql_survey_output = "SELECT *,DATE_FORMAT(SUBSTRING(SO.survey_id,-14,14),'%d-%m-%Y %H:%i:%s') AS survey_date FROM survey_output SO, employee_master EM WHERE SO.row_id IN(" . $row_id_string . ") " . $date_condition . " AND SUBSTRING(SO.survey_id,3,5)=EM.emp_code" . $emp_condition . " 
									  ORDER BY SO.survey_id DESC,FIND_IN_SET(SO.row_id,'" . $row_id_string_SET . "')";
                            $rs_survey_output = mysqli_query($link,$sql_survey_output);
                            $output_no = 1;
                            while ($row_survey_output = mysqli_fetch_assoc($rs_survey_output)) {
                                $survey_id = $row_survey_output['survey_id'];
                                $survey_date = date("d-M-Y", strtotime($row_survey_output['survey_date']));
                                $survey_time = date("H:i:s", strtotime($row_survey_output['survey_date']));
                                $value = $row_survey_output['value'];
                                /*if(strpos($value,"#") == true){
						 $valuearray = explode("#",$value);
						 $value = $valuearray[0];
					 }*/
                                if (!in_array($survey_id, ${'survey_id_array' . $survey_sub_menu})) {
                                    $sqllatlong = "SELECT emp_code FROM location WHERE trans_id='" . $survey_id . "'";
                                    $rslatlong = mysqli_query($link,$sqllatlong);
                                    $rowlatlong = mysqli_fetch_assoc($rslatlong);
                                    $emp_code = $rowlatlong['emp_code'];
                                    $sql_empname = "SELECT emp_name FROM employee_master WHERE emp_code = '" . $emp_code . "'";
                                    $res_empname = mysqli_query($link,$sql_empname);
                                    $row_empname = mysqli_fetch_assoc($res_empname);
                                    $emp_name = $row_empname['emp_name'];

                                    $sqlintime = "SELECT check_in_time FROM survey_header WHERE survey_id='" . $survey_id . "'";
                                    $rsintime = mysqli_query($link,$sqlintime);
                                    $rowintime = mysqli_fetch_assoc($rsintime);
                                    $check_in_time = $rowintime['check_in_time'];
                                    //if($output_no>1)  ${'valueexcel'.$survey_id}.="\n";
                                    ${'valueexcel' . $survey_id . $survey_sub_menu} .= ${'sl_no' . $survey_sub_menu} . "\t";
                                    ${'valueexcel' . $survey_id . $survey_sub_menu} .= $survey_id . "\t";
                                    ${'valueexcel' . $survey_id . $survey_sub_menu} .= $survey_date . "\t";
                                    ${'valueexcel' . $survey_id . $survey_sub_menu} .= $check_in_time . "\t";
                                    ${'valueexcel' . $survey_id . $survey_sub_menu} .= $survey_time . "\t";
                                    ${'valueexcel' . $survey_id . $survey_sub_menu} .= $emp_name . "\t";
                                    array_push(${'survey_id_array' . $survey_sub_menu}, $survey_id);
                                    ${'sl_no' . $survey_sub_menu}++;
                                }
                                if ($row_survey_output['row_id'] == 'RA001') {
                                    $sqlcustname = "SELECT customer_name FROM customer_master WHERE customer_code='" . $value . "'";
                                    $rscustname = mysqli_query($link,$sqlcustname);
                                    $rowcustname = mysqli_fetch_assoc($rscustname);
                                    $customer_name = $rowcustname['customer_name'];
                                    //$value=$valueparts[0].' - '.$f_type;
                                    $value = $customer_name;
                                }
                                if ($row_survey_output['row_id'] == 'RA044') {
                                    $sqlroutename = "SELECT route_name FROM route_master WHERE route_code='" . $value . "'";
                                    $rsroutename = mysqli_query($link,$sqlroutename);
                                    $rowroutename = mysqli_fetch_assoc($rsroutename);
                                    $route_name = $rowroutename['route_name'];
                                    //$value=$valueparts[0].' - '.$f_type;
                                    $value = $route_name;
                                }
                                if (strtoupper($_SESSION['nick_name']) == 'SAI' ) {
                                if ($row_survey_output['row_id'] == 'RA002' || $row_survey_output['row_id'] == 'RA046') {
                                    if ($value != '') {
                                        $imageval = $value;
                                        $imageval = ltrim($imageval, " ");
                                        $imageval = rtrim($imageval, " ");
                                        $imageval = rtrim($imageval, ";");
                                        $imageval = str_replace('.JPEG', '.jpeg', $imageval);
                                        $site_image_array = explode(";", $imageval);

                                        foreach ($site_image_array as $image) {
                                            $image = ltrim($image, " ");
                                            if ($image != '') {
                                                //$image_string= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$imageval."\" target=\"_blank\" style=\"color:brown;\">http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$imageval."</a>";

                                                $value = "http://salesmpower.acedns.in/upload/" . strtoupper($_SESSION['nick_name']) . "/" . $image;
                                            }
                                        }
                                    }
                                }
                                }
                                
                                if(strtoupper($_SESSION['nick_name']) == 'SHAKTI'){
                                    if ($row_survey_output['row_id'] == 'RA077' || $row_survey_output['row_id'] == 'RA044' || $row_survey_output['row_id'] == 'RA022') {
                                    if ($value != '') {
                                        $imageval = $value;
                                        $imageval = ltrim($imageval, " ");
                                        $imageval = rtrim($imageval, " ");
                                        $imageval = rtrim($imageval, ";");
                                        $imageval = str_replace('.JPEG', '.jpeg', $imageval);
                                        $site_image_array = explode(";", $imageval);

                                        foreach ($site_image_array as $image) {
                                            $image = ltrim($image, " ");
                                            if ($image != '') {
                                                //$image_string= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$imageval."\" target=\"_blank\" style=\"color:brown;\">http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$imageval."</a>";

                                                $value = "http://salesmpower.acedns.in/upload/" . strtoupper($_SESSION['nick_name']) . "/" . $image;
                                            }
                                        }
                                    }
                                }
                                }
                                
                                
                                
                                ${'valueexcel' . $survey_id . $survey_sub_menu} .= $value . "\t";
                                //${datavalue.$menu_id}.=${'valueexcel'.$survey_id};
                                $output_no++;
                            }
                            $menu_no++;
                        }
                        //print_r($survey_id_array);
                        //$menu_id_stat='RA115';
                        //echo ${excelheader.$menu_id_stat};
                        //echo  ${datavalue.$menu_id_stat};
                        //$zip = new ZipFile();
                        for ($i = 0; $i < count($survey_sub_menu_array); $i++) {
                            //${dataorderheader.$rds_code_array[$i]} = ${lineorderheader.$rds_code_array[$i]}.${lineorderheader_freight.$rds_code_array[$i]} ;
                            for ($m = 0; $m < count(${'survey_id_array' . $survey_sub_menu_array[$i]}); $m++) {
                                ${'datavalue' . $survey_sub_menu_array[$i]} .= ${'valueexcel' . ${'survey_id_array' . $survey_sub_menu_array[$i]}[$m] . $survey_sub_menu_array[$i]} . "\n";
                            }
                            if (${'datavalue' . $survey_sub_menu_array[$i]} == "") {
                                ${'datavalue' . $survey_sub_menu_array[$i]} = "\r\n(0) Records Found!\n";
                            }

                            $date = gmdate('d', strtotime('+330 minute'));
                            $month = gmdate('m', strtotime('+330 minute'));
                            $year = gmdate('Y', strtotime('+330 minute'));
                            $hour = gmdate('H', strtotime('+330 minute'));
                            $minute = gmdate('i', strtotime('+330 minute'));
                            $second = gmdate('s', strtotime('+330 minute'));

                            ${'file_content' . $survey_sub_menu_array[$i]} = ${'excelheader' . $survey_sub_menu_array[$i]} . "\n" . ${'excelsubheader' . $survey_sub_menu_array[$i]} . "\n" . ${'datavalue' . $survey_sub_menu_array[$i]};
                            ${'file_name' . $survey_sub_menu_array[$i]} = "$survey_sub_menu_array[$i]_$date$month$year$hour$minute$second.xls";


                            //add files to the zip, passing file contents, not actual files
                            //$zip->addFile($file_content1, $file_name1);
                            $zip->addFile(${'file_content' . $survey_sub_menu_array[$i]}, ${'file_name' . $survey_sub_menu_array[$i]});
                        }
                    }
                    //SAI client menu end

                    header("Content-type: application/octet-stream");
                    header("Content-Disposition: inline; filename=excel_files_survey.zip");
                    echo $zip->file();
                    exit();
                } else {
                    //$sql_get_menu = "SELECT layout_name,menu_id FROM survey_input WHERE type='menu' ORDER BY display_order ASC";
                    $sql_get_menu = "SELECT DISTINCT survey_sub_menu FROM survey_input ORDER BY survey_sub_menu ASC";
                    $res_get_menu = mysqli_query($link,$sql_get_menu);
                    $count_menu = mysqli_num_rows($res_get_menu);
                    $menu_no = 1;
                    //$menu_id_array=array();
                    //$menu_name_array=array();
                    $survey_sub_menu_array = array();
                    while ($row_get_menu = mysqli_fetch_assoc($res_get_menu)) {
                        //$menu_name=$row_get_menu['layout_name'];
                        //$menu_id=$row_get_menu['menu_id'];
                        $survey_sub_menu = $row_get_menu['survey_sub_menu'];
                        //array_push($menu_id_array,$menu_id);
                        //array_push($menu_name_array,$menu_name);
                        array_push($survey_sub_menu_array, $survey_sub_menu);
                        ${'excelheader' . $survey_sub_menu} = '' . "\t" . '' . "\t" . '' . "\t" . "\t" . "\t";
                        ${'excelsubheader' . $survey_sub_menu} = 'Sr. No' . "\t" . 'Unique Store ID' . "\t" . 'Survey Date' . "\t" . 'Survey Time' . "\t" . 'Employee' . "\t" . 'Lattitude' . "\t" . 'Longitude' . "\t";
                        ${'sl_no' . $survey_sub_menu} = 1;
                        $row_id_string = '';
                        $row_id_string_SET = '';
                        $valueexcel = '';
                        ${'survey_id_array' . $survey_sub_menu} = array();

                        // ${array_header.$menu_id}=array();
                        //$sql_get_display="SELECT display_name,row_id,action FROM survey_input WHERE type!='menu' AND menu_id='".$menu_id."' ORDER BY display_order ASC";
                        $sql_get_display = "SELECT display_name,row_id,action FROM survey_input WHERE 
								type!='menu' AND survey_sub_menu='" . $survey_sub_menu . "' AND acedns='Y' ORDER BY display_order ASC";
                        $res_get_display = mysqli_query($link,$sql_get_display);
                        $count_display = mysqli_num_rows($res_get_display);
                        for ($k = 0; $k < $count_display; $k++) {
                            //echo 'A';
                            ${'excelheader' . $survey_sub_menu} .= "\t";
                        }

                        $display_no = 1;
                        while ($row_get_display = mysqli_fetch_assoc($res_get_display)) {
                            $display_name = $row_get_display['display_name'];
                            $action = $row_get_display['action'];
                            /*if($action!='')
							{
								$display_name_array=explode('#',$action);
								$display_name=$display_name_array[0];
							}*/
                            $display_id = $row_get_display['row_id'];
                            ${'excelsubheader' . $survey_sub_menu} .= $display_name . "\t";
                            $row_id_string .= "'" . $display_id . "'" . ',';
                            $row_id_string_SET .= $display_id . ',';
                            $display_no++;
                        }
                        //$layer_no++;

                        $row_id_string = substr($row_id_string, 0, -1);
                        $row_id_string_SET = substr($row_id_string_SET, 0, -1);

                        if (empty($row_id_string)) {
                            $sql_survey_output = "SELECT *, DATE_FORMAT(SUBSTRING(SO.survey_id,-14,14),'%d-%m-%Y %H:%i:%s') AS survey_date 
                            FROM survey_output SO, employee_master EM 
                            WHERE DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') >='2024-03-27' 
                              AND DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') <='2024-03-27' 
                              AND SUBSTRING(SO.survey_id,3,5) = EM.emp_code 
                            ORDER BY SO.survey_id DESC, FIND_IN_SET(SO.row_id, '')
                            ";
                       }else{
                            $sql_survey_output = "SELECT *,DATE_FORMAT(SUBSTRING(SO.survey_id,-14,14),'%d-%m-%Y %H:%i:%s') AS survey_date FROM survey_output SO, employee_master EM WHERE SO.row_id IN(" . $row_id_string . ") " . $date_condition . " AND SUBSTRING(SO.survey_id,3,5)=EM.emp_code" . $emp_condition . " 
									  ORDER BY SO.survey_id DESC,FIND_IN_SET(SO.row_id,'" . $row_id_string_SET . "')";
                        }

                        

                        $rs_survey_output = mysqli_query($link,$sql_survey_output);
                        $output_no = 1;

                        $value_array = array();
                        $route_names = array();
                    
                        while ($row_survey_output = mysqli_fetch_assoc($rs_survey_output)) {
                            $survey_id = $row_survey_output['survey_id'];
							$row_id=$row_survey_output['row_id'];
                            $survey_date = date("d-M-Y", strtotime($row_survey_output['survey_date']));
                            $survey_time = date("H:i:s", strtotime($row_survey_output['survey_date']));

                           $sqli = "SELECT `value` FROM `survey_output` WHERE `row_id`='RA008'";

                            $queryi = mysqli_query($link,$sqli);
                            while ($resulti = mysqli_fetch_assoc($queryi)) {
                                $route_id = $resulti['value'];

                                $sql2 = "SELECT * FROM `route_master` WHERE `route_code`='$route_id'";
                                // echo $sql2."<br/>";
                                $query2 = mysqli_query($link,$sql2);
                                $result2 = mysqli_fetch_assoc($query2);
                                $route_name = $result2['route_name'];
                                $route_names[] = $route_name;
                                $route_code = $result2['route_code'];
                                // if($row_survey_output['value'] == $route_code) {
                                if ($row_survey_output['value'] == $route_code) {
                                    $value = $route_names[$i]; // Use the appropriate route name from the array
                                }
                                // echo $value."<br/>";
                                // $value_array=$customer_name;
                                // foreach($value_array as $values){
                                // 	$value=$values;
                                // }

                                // }else{
                                // 	$value=$row_survey_output['value'];
                                // }
                            }
                            // for($i=0;$i<=count($route_names);$i++){
                            // 	$value=$route_names[$i];
                            // 	// echo $value."<br/>";
                            // }
                            $sqli2 = "SELECT `value` FROM `survey_output` WHERE `row_id`='RA001'";
                            $queryi2 = mysqli_query($link,$sqli2);
                            while ($resulti2 = mysqli_fetch_assoc($queryi2)) {

                                $customer_code = $resulti2['value'];

                                $sql2 = "SELECT * FROM `customer_master` WHERE `customer_code`='$customer_code'";

                                $query2 = mysqli_query($link,$sql2);
                                $result2 = mysqli_fetch_assoc($query2);
                                $customer_names[] = $result2['customer_name'];
                                $customer_code = $result2['customer_code'];
                                // if($row_survey_output['value']==$customer_code){
                                if ($row_survey_output['value'] == $customer_code) {
                                    $value = $customer_names[$i]; // Use the appropriate route name from the array
                                }
                                // foreach($value_array as $values){
                                // 	$value=$values;
                                // }
                            }

                           if(strtoupper($_SESSION['nick_name']) == 'CORAL'){
							   if($row_id=='RA005'){
                            $sqli3 = "SELECT * FROM `survey_output` WHERE `row_id`='".$row_id."'";
                            $queryi3 = mysqli_query($link,$sqli3);
                            while ($resulti3 = mysqli_fetch_assoc($queryi3)) {
                                $image = $resulti3['value'];

                                if ($row_survey_output['value'] == $image) {
                                    $imageval = $image;
                                    $imageval = ltrim($imageval, " ");
                                    $imageval = rtrim($imageval, " ");
                                    $imageval = rtrim($imageval, ";");
                                    $imageval = str_replace('.JPEG', '.jpeg', $imageval);
                                    $site_image_array = explode(";", $imageval);

                                    foreach ($site_image_array as $image) {
                                        $image = ltrim($image, " ");
                                        if ($image != '') {
                                            $value = "http://salesmpower.acedns.in/upload/CORAL" . "/" . $image;
                                        }
                                    }
                                }
                            }
							   }
							   else{
							 $value=$row_survey_output['value'];
							   }

                            
                           }

                            // $value=$row_survey_output['value'];

                            /*if(strpos($value,"#") == true){
						 $valuearray = explode("#",$value);
						 $value = $valuearray[0];
					 }*/
                            if (!in_array($survey_id, ${'survey_id_array' . $survey_sub_menu})) {
                                $sqllatlong = "SELECT emp_code FROM location WHERE trans_id='" . $survey_id . "'";
                                $rslatlong = mysqli_query($link,$sqllatlong);
                                $rowlatlong = mysqli_fetch_assoc($rslatlong);
                                $emp_code = $rowlatlong['emp_code'];
                                $sql_empname = "SELECT emp_name FROM employee_master WHERE emp_code = '" . $emp_code . "'";
                                $res_empname = mysqli_query($link,$sql_empname);
                                $row_empname = mysqli_fetch_assoc($res_empname);
                                $emp_name = $row_empname['emp_name'];

                                $sqlLatLong = "SELECT latt,longi FROM location where trans_id='" . $survey_id . "'";
                                $rsLatLong = mysqli_query($link,$sqlLatLong);
                                $rowLatLong = mysqli_fetch_assoc($rsLatLong);
                                $latt = $rowLatLong['latt'];
                                $longi = $rowLatLong['longi'];


                                //if($output_no>1)  ${'valueexcel'.$survey_id}.="\n";
                                ${'valueexcel' . $survey_id . $survey_sub_menu} .= ${'sl_no' . $survey_sub_menu} . "\t";
                                ${'valueexcel' . $survey_id . $survey_sub_menu} .= $survey_id . "\t";
                                ${'valueexcel' . $survey_id . $survey_sub_menu} .= $survey_date . "\t";
                                ${'valueexcel' . $survey_id . $survey_sub_menu} .= $survey_time . "\t";
                                ${'valueexcel' . $survey_id . $survey_sub_menu} .= $emp_name . "\t";
                                ${'valueexcel' . $survey_id . $survey_sub_menu} .= $latt . "\t";
                                ${'valueexcel' . $survey_id . $survey_sub_menu} .= $longi . "\t";
                                if (strtoupper($_SESSION['nick_name']) == 'SUPERSHAKTI') {
                                    // Type
                                    $sqlType = "SELECT row_id FROM survey_input WHERE survey_sub_menu='Site Visit' AND display_name='Type'";
                                    $rsType = mysqli_query($link,$sqlType);
                                    $rowType = mysqli_fetch_assoc($rsType);
                                    $row_id = $rowType['row_id'];
                                    $sqlTypeValue = "SELECT value FROM survey_output WHERE row_id='" . $row_id . "' AND survey_id='" . $survey_id . "'";
                                    $rsTypeValue = mysqli_query($link,$sqlTypeValue);
                                    $rowTypeValue = mysqli_fetch_assoc($rsTypeValue);
                                    $type = $rowTypeValue['value'];

                                    // Area
                                    $sqlArea = "SELECT row_id FROM survey_input WHERE survey_sub_menu='Site Visit' AND display_name='Area'";
                                    $rsArea = mysqli_query($link,$sqlArea);
                                    $rowArea = mysqli_fetch_assoc($rsArea);
                                    $row_id = $rowArea['row_id'];
                                    $sqlAreaValue = "SELECT value FROM survey_output WHERE row_id='" . $row_id . "' AND survey_id='" . $survey_id . "'";
                                    $rsAreaValue = mysqli_query($link,$sqlAreaValue);
                                    $rowAreaValue = mysqli_fetch_assoc($rsAreaValue);
                                    $area = $rowAreaValue['value'];

                                    // Name Of site
                                    $sqlNameOfSite = "SELECT row_id FROM survey_input WHERE survey_sub_menu='Site Visit' AND display_name='Name Of Site'";
                                    $rsNameOfSite = mysqli_query($link,$sqlNameOfSite);
                                    $rowNameOfSite = mysqli_fetch_assoc($rsNameOfSite);
                                    $row_id = $rowNameOfSite['row_id'];
                                    $sqlNameOfSiteValue = "SELECT value FROM survey_output WHERE row_id='" . $row_id . "' AND survey_id='" . $survey_id . "'";
                                    $rsNameOfSiteValue = mysqli_query($link,$sqlNameOfSiteValue);
                                    $rowNameOfSiteValue = mysqli_fetch_assoc($rsNameOfSiteValue);
                                    $name_of_site = $rowNameOfSiteValue['value'];

                                    // Address
                                    $sqlAddress = "SELECT row_id FROM survey_input WHERE survey_sub_menu='Site Visit' AND display_name='Address'";
                                    $rsAddress = mysqli_query($link,$sqlAddress);
                                    $rowAddress = mysqli_fetch_assoc($rsAddress);
                                    $row_id = $rowAddress['row_id'];
                                    $sqlAddressValue = "SELECT value FROM survey_output WHERE row_id='" . $row_id . "' AND survey_id='" . $survey_id . "'";
                                    $rsAddressValue = mysqli_query($link,$sqlAddressValue);
                                    $rowAddressValue = mysqli_fetch_assoc($rsAddressValue);
                                    $address = $rowAddressValue['value'];

                                    // Nearest Dealer
                                    $sqlNearestDealer = "SELECT row_id FROM survey_input WHERE survey_sub_menu='Site Visit' AND display_name='Nearest Dealer'";
                                    $rsNearestDealer = mysqli_query($link,$sqlNearestDealer);
                                    $rowNearestDealer = mysqli_fetch_assoc($rsNearestDealer);
                                    $row_id = $rowNearestDealer['row_id'];
                                    $sqlNearestDealerValue = "SELECT value FROM survey_output WHERE row_id='" . $row_id . "' AND survey_id='" . $survey_id . "'";
                                    $rsNearestDealerValue = mysqli_query($link,$sqlNearestDealerValue);
                                    $rowNearestDealerValue = mysqli_fetch_assoc($rsNearestDealerValue);
                                    $nearest_dealer = $rowNearestDealerValue['value'];
                                    
//$sqlNearestDealerName = "SELECT customer_name FROM customer_master WHERE dealer_code='" . $nearest_dealer . "'";

                                    $sqlNearestDealerName = "SELECT customer_name FROM customer_master WHERE customer_code='" . $nearest_dealer . "'";
                                    
                                    //echo $sqlNearestDealerName;
                                    
                                    $rsNearestDealerName = mysqli_query($link,$sqlNearestDealerName);
                                    $rowNearestDealerName = mysqli_fetch_assoc($rsNearestDealerName);
                                    $nearest_dealer_name = $rowNearestDealerName['customer_name'];

                                    // Total TMT needed
                                    $sqlTotalTMTNeeded = "SELECT row_id FROM survey_input WHERE survey_sub_menu='Site Visit' AND display_name='Total TMT needed'";
                                    $rsTotalTMTNeeded = mysqli_query($link,$sqlTotalTMTNeeded);
                                    $rowTotalTMTNeeded = mysqli_fetch_assoc($rsTotalTMTNeeded);
                                    $row_id = $rowTotalTMTNeeded['row_id'];
                                    $sqlTotalTMTNeededValue = "SELECT value FROM survey_output WHERE row_id='" . $row_id . "' AND survey_id='" . $survey_id . "'";
                                    $rsTotalTMTNeededValue = mysqli_query($link,$sqlTotalTMTNeededValue);
                                    $rowTotalTMTNeededValue = mysqli_fetch_assoc($rsTotalTMTNeededValue);
                                    $total_tmt_needed = $rowTotalTMTNeededValue['value'];

                                    // TMT brands in use
                                    $sqlTMTBrandsInUse = "SELECT row_id FROM survey_input WHERE survey_sub_menu='Site Visit' AND display_name='TMT brands in use'";
                                    $rsTMTBrandsInUse = mysqli_query($link,$sqlTMTBrandsInUse);
                                    $rowTMTBrandsInUse = mysqli_fetch_assoc($rsTMTBrandsInUse);
                                    $row_id = $rowTMTBrandsInUse['row_id'];
                                    $sqlTMTBrandsInUseValue = "SELECT value FROM survey_output WHERE row_id='" . $row_id . "' AND survey_id='" . $survey_id . "'";
                                    $rsTMTBrandsInUseValue = mysqli_query($link,$sqlTMTBrandsInUseValue);
                                    $rowTMTBrandsInUseValue = mysqli_fetch_assoc($rsTMTBrandsInUseValue);
                                    $tmt_brands_in_use = $rowTMTBrandsInUseValue['value'];

                                    // Reason
                                    $sqlReason = "SELECT row_id FROM survey_input WHERE survey_sub_menu='Site Visit' AND display_name='Reason'";
                                    $rsReason = mysqli_query($link,$sqlReason);
                                    $rowReason = mysqli_fetch_assoc($rsReason);
                                    $row_id = $rowReason['row_id'];
                                    $sqlReasonValue = "SELECT value FROM survey_output WHERE row_id='" . $row_id . "' AND survey_id='" . $survey_id . "'";
                                    $rsReasonValue = mysqli_query($link,$sqlReasonValue);
                                    $rowReasonValue = mysqli_fetch_assoc($rsReasonValue);
                                    $reason = $rowReasonValue['value'];

                                    ${'valueexcel' . $survey_id . $survey_sub_menu} .= $type . "\t";
                                    ${'valueexcel' . $survey_id . $survey_sub_menu} .= $area . "\t";
                                    ${'valueexcel' . $survey_id . $survey_sub_menu} .= $name_of_site . "\t";
                                    ${'valueexcel' . $survey_id . $survey_sub_menu} .= $address . "\t";
                                    ${'valueexcel' . $survey_id . $survey_sub_menu} .= $nearest_dealer_name . "\t";
                                    ${'valueexcel' . $survey_id . $survey_sub_menu} .= $total_tmt_needed . "\t";
                                    ${'valueexcel' . $survey_id . $survey_sub_menu} .= $tmt_brands_in_use . "\t";
                                    ${'valueexcel' . $survey_id . $survey_sub_menu} .= $reason . "\t";
                                }


                                array_push(${'survey_id_array' . $survey_sub_menu}, $survey_id);
                                ${'sl_no' . $survey_sub_menu}++;
                            }
                            if (strtoupper($_SESSION['nick_name']) == 'SAI') {
                                if ($row_survey_output['row_id'] == 'RA001') {
                                    $sqlcustname = "SELECT customer_name FROM customer_master WHERE customer_code='" . $value . "'";
                                    $rscustname = mysqli_query($link,$sqlcustname);
                                    $rowcustname = mysqli_fetch_assoc($rscustname);
                                    $customer_name = $rowcustname['customer_name'];
                                    //$value=$valueparts[0].' - '.$f_type;
                                    $value = $customer_name;
                                }
                            }
                            if (strtoupper($_SESSION['nick_name']) == 'SUPERSHAKTI') {



                                if ($row_survey_output['row_id'] == 'RA011' || $row_survey_output['row_id'] == 'RA069' || $row_survey_output['row_id'] == 'RA070' || $row_survey_output['row_id'] == 'RA026' || $row_survey_output['row_id'] == 'RA082' || $row_survey_output['row_id'] == 'RA066' || $row_survey_output['row_id'] == 'RA067' || $row_survey_output['row_id'] == 'RA030') {
                                    $sqlcustname = "SELECT customer_name FROM customer_master WHERE customer_code='" . $value . "'";
                                    $rscustname = mysqli_query($link,$sqlcustname);
                                    $rowcustname = mysqli_fetch_assoc($rscustname);
                                    $customer_name = $rowcustname['customer_name'];
                                    //$value=$valueparts[0].' - '.$f_type;
                                    $value = $customer_name;
                                }
                                if ($row_survey_output['row_id'] == 'RA034') {
                                    if ($value != '') {
                                        $imageval = $value;
                                        $imageval = ltrim($imageval, " ");
                                        $imageval = rtrim($imageval, " ");
                                        $imageval = rtrim($imageval, ";");
                                        $imageval = str_replace('.JPEG', '.jpeg', $imageval);
                                        $site_image_array = explode(";", $imageval);

                                        foreach ($site_image_array as $image) {
                                            $image = ltrim($image, " ");
                                            if ($image != '') {
                                                //$image_string= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$imageval."\" target=\"_blank\" style=\"color:brown;\">http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$imageval."</a>";

                                                $value = "http://salesmpower.acedns.in/upload/" . strtoupper($_SESSION['nick_name']) . "/" . $image;
                                            }
                                        }
                                    } else {
                                        $value = $value;
                                    }
                                }
                            }
                            if (strtoupper($_SESSION['nick_name']) == 'GOLDSTONET') {
                                if ($row_survey_output['row_id'] == 'RA047' || $row_survey_output['row_id'] == 'RA137' || $row_survey_output['row_id'] == 'RA038') {
                                    $imageval = $value;
                                    $imageval = ltrim($imageval, " ");
                                    $imageval = rtrim($imageval, " ");
                                    $imageval = rtrim($imageval, ";");
                                    $imageval = str_replace('.JPEG', '.jpeg', $imageval);
                                    $site_image_array = explode(";", $imageval);
                                    $image_string = '';
                                    foreach ($site_image_array as $image) {
                                        $image = ltrim($image, " ");
                                        if ($image != '') {
                                            //$image_string= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$imageval."\" target=\"_blank\" style=\"color:brown;\">http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$imageval."</a>";
                                            if ($image_string != '') {
                                                $image_string = $image_string . ';' . "http://salesmpower.acedns.in/upload/" . strtoupper($_SESSION['nick_name']) . "/" . $image;
                                            } else {
                                                $image_string = "http://salesmpower.acedns.in/upload/" . strtoupper($_SESSION['nick_name']) . "/" . $image;
                                            }
                                        }
                                    }
                                    $value = $image_string;
                                } else {
                                    $value = $value;
                                }
                            }
							if (strtoupper($_SESSION['nick_name']) == 'NIMBUS') {
							$value=$row_survey_output['value'];
							}
                            ${'valueexcel' . $survey_id . $survey_sub_menu} .= $value . "\t";
                            //${datavalue.$menu_id}.=${'valueexcel'.$survey_id};
                            $output_no++;
                        }
                        $menu_no++;
                    }
                    //print_r($survey_id_array);
                    //$menu_id_stat='RA115';
                    //echo ${excelheader.$menu_id_stat};
                    //echo  ${datavalue.$menu_id_stat};
                    $zip = new ZipFile();
                    for ($i = 0; $i < count($survey_sub_menu_array); $i++) {
                        //${dataorderheader.$rds_code_array[$i]} = ${lineorderheader.$rds_code_array[$i]}.${lineorderheader_freight.$rds_code_array[$i]} ;
                        for ($m = 0; $m < count(${'survey_id_array' . $survey_sub_menu_array[$i]}); $m++) {
                            ${'datavalue' . $survey_sub_menu_array[$i]} .= ${'valueexcel' . ${'survey_id_array' . $survey_sub_menu_array[$i]}[$m] . $survey_sub_menu_array[$i]} . "\n";
                        }
                        if (${'datavalue' . $survey_sub_menu_array[$i]} == "") {
                            ${'datavalue' . $survey_sub_menu_array[$i]} = "\r\n(0) Records Found!\n";
                        }

                        $date = gmdate('d', strtotime('+330 minute'));
                        $month = gmdate('m', strtotime('+330 minute'));
                        $year = gmdate('Y', strtotime('+330 minute'));
                        $hour = gmdate('H', strtotime('+330 minute'));
                        $minute = gmdate('i', strtotime('+330 minute'));
                        $second = gmdate('s', strtotime('+330 minute'));

                        ${'file_content' . $survey_sub_menu_array[$i]} = ${'excelheader' . $survey_sub_menu_array[$i]} . "\n" . ${'excelsubheader' . $survey_sub_menu_array[$i]} . "\n" . ${'datavalue' . $survey_sub_menu_array[$i]};
                        ${'file_name' . $survey_sub_menu_array[$i]} = "$survey_sub_menu_array[$i]_$date$month$year$hour$minute$second.xls";

                        //add files to the zip, passing file contents, not actual files
                        //$zip->addFile($file_content1, $file_name1);
                        $zip->addFile(${'file_content' . $survey_sub_menu_array[$i]}, ${'file_name' . $survey_sub_menu_array[$i]});
                    }

                    header("Content-type: application/octet-stream");
                    header("Content-Disposition: inline; filename=excel_files_survey.zip");

                    echo $zip->file();
                    exit();
                }
			}
				 else {
                $sql_get_menu = "SELECT layout_name,menu_id FROM survey_input WHERE type='menu' AND survey_type='" . $survey_type . "' ORDER BY display_order ASC";
                $res_get_menu = mysqli_query($link,$sql_get_menu);
                $count_menu = mysqli_num_rows($res_get_menu);
                $menu_no = 1;
                $menu_id_array = array();
                $menu_name_array = array();

                while ($row_get_menu = mysqli_fetch_assoc($res_get_menu)) {
                    $menu_name = $row_get_menu['layout_name'];
                    $menu_id = $row_get_menu['menu_id'];
                    array_push($menu_id_array, $menu_id);
                    array_push($menu_name_array, $menu_name);
                    ${'excelheader' . $menu_id} = '' . "\t" . '' . "\t" . '' . "\t" . "\t" . "\t";
                    ${'excelsubheader' . $menu_id} = 'Sr. No' . "\t" . 'Unique Store ID' . "\t" . 'Survey Date' . "\t" . 'Lattitude' . "\t" . 'Longitude' . "\t";
                    ${'sl_no' . $menu_id} = 1;
                    $row_id_string = '';
                    $row_id_string_SET = '';
                    $valueexcel = '';
                    ${'survey_id_array' . $menu_id} = array();

                    // ${array_header.$menu_id}=array();
                    $sql_layer_check = "SELECT row_id FROM survey_input WHERE type = 'layer'";
                    $res_layer_check = mysqli_query($link,$sql_layer_check);
                    $total_layer_check = mysqli_num_rows($res_layer_check);


                    $sql_get_layer = "SELECT layout_name,row_id FROM survey_input WHERE type='layer' AND 
										menu_id='" . $menu_id . "' ORDER BY display_order ASC";
                    $res_get_layer = mysqli_query($link,$sql_get_layer);
                    $count_layer = mysqli_num_rows($res_get_layer);
                    $layer_no = 1;
                    while ($row_get_layer = mysqli_fetch_assoc($res_get_layer)) {
                        $layout_name = $row_get_layer['layout_name'];
                        $layer_id = $row_get_layer['row_id'];
                        ${'excelheader' . $menu_id} .= $layout_name;

                        $sql_get_display = "SELECT display_name,row_id,action FROM survey_input WHERE layout_name='" . $layout_name . "' 
											AND type!='layer' AND type!='menu' AND menu_id='" . $menu_id . "' ORDER BY display_order ASC";

                        $res_get_display = mysqli_query($link,$sql_get_display);
                        $count_display = mysqli_num_rows($res_get_display);
                        for ($k = 0; $k < $count_display; $k++) {
                            //echo 'A';
                            ${'excelheader' . $menu_id} .= "\t";
                        }
                        $display_no = 1;
                        while ($row_get_display = mysqli_fetch_assoc($res_get_display)) {
                            $display_name = $row_get_display['display_name'];
                            $action = $row_get_display['action'];
                            if ($action != '') {
                                $display_name_array = explode('#', $action);
                                $display_name = $display_name_array[0];
                            }
                            $display_id = $row_get_display['row_id'];
                            ${'excelsubheader' . $menu_id} .= $display_name . "\t";
                            $row_id_string .= "'" . $display_id . "'" . ',';
                            $row_id_string_SET .= $display_id . ',';
                            $display_no++;
                        }
                        $layer_no++;
                    }

                    $row_id_string = substr($row_id_string, 0, -1);
                    $row_id_string_SET = substr($row_id_string_SET, 0, -1);
                    
                    if (empty($row_id_string)) {
                        $sql_survey_output = "SELECT *, DATE_FORMAT(SUBSTRING(SO.survey_id,-14,14),'%d-%m-%Y %H:%i:%s') AS survey_date 
                        FROM survey_output SO, employee_master EM 
                        WHERE DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') >='2024-03-27' 
                          AND DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') <='2024-03-27' 
                          AND SUBSTRING(SO.survey_id,3,5) = EM.emp_code 
                        ORDER BY SO.survey_id DESC, FIND_IN_SET(SO.row_id, '')
                        ";
                    } else {

                        $sql_survey_output = "SELECT *,DATE_FORMAT(SUBSTRING(SO.survey_id,-14,14),'%d-%m-%Y %H:%i:%s') AS survey_date FROM survey_output SO, employee_master EM WHERE SO.row_id IN(" . $row_id_string . ") " . $date_condition . " AND SUBSTRING(SO.survey_id,3,5)=EM.emp_code" . $emp_condition . " 
									  ORDER BY SO.survey_id DESC,FIND_IN_SET(SO.row_id,'" . $row_id_string_SET . "')";
                    }
                    
                    // $sql_survey_output ="   SELECT *, DATE_FORMAT(SUBSTRING(SO.survey_id,-14,14),'%d-%m-%Y %H:%i:%s') AS survey_date FROM survey_output SO, employee_master EM WHERE DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') >='2024-03-27' AND DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') <='2024-03-27' AND SUBSTRING(SO.survey_id,3,5) = EM.emp_code ORDER BY SO.survey_id DESC, FIND_IN_SET(SO.row_id, '')";

                    $rs_survey_output = mysqli_query($link,$sql_survey_output);
                    $output_no = 1;
                    while ($row_survey_output = mysqli_fetch_assoc($rs_survey_output)) {
                        $survey_id = $row_survey_output['survey_id'];
                        $survey_date = $row_survey_output['survey_date'];
                        $value = $row_survey_output['value'];
                        if (strpos($value, "#") == true) {
                            $valuearray = explode("#", $value);
                            $value = $valuearray[0];
                        }
                        if (!in_array($survey_id, ${'survey_id_array' . $menu_id})) {
                            $sqllatlong = "SELECT latt,longi FROM location WHERE trans_id='" . $survey_id . "'";
                            $rslatlong = mysqli_query($link,$sqllatlong);
                            $rowlatlong = mysqli_fetch_assoc($rslatlong);
                            $lattitude = $rowlatlong['latt'];
                            $longitude = $rowlatlong['longi'];
                            //if($output_no>1)  ${'valueexcel'.$survey_id}.="\n";
                            ${'valueexcel' . $survey_id . $menu_id} .= ${'sl_no' . $menu_id} . "\t";
                            ${'valueexcel' . $survey_id . $menu_id} .= $survey_id . "\t";
                            ${'valueexcel' . $survey_id . $menu_id} .= $survey_date . "\t";
                            ${'valueexcel' . $survey_id . $menu_id} .= $lattitude . "\t";
                            ${'valueexcel' . $survey_id . $menu_id} .= $longitude . "\t";
                            array_push(${'survey_id_array' . $menu_id}, $survey_id);
                            ${'sl_no' . $menu_id}++;
                        }
                        ${'valueexcel' . $survey_id . $menu_id} .= $value . "\t";
                        //${datavalue.$menu_id}.=${'valueexcel'.$survey_id};
                        $output_no++;
                    }
                    $menu_no++;
                }
                //print_r($survey_id_array);
                //$menu_id_stat='RA115';
                //echo ${excelheader.$menu_id_stat};
                //echo  ${datavalue.$menu_id_stat};
                $zip = new ZipFile();
                for ($i = 0; $i < count($menu_id_array); $i++) {
                    //${dataorderheader.$rds_code_array[$i]} = ${lineorderheader.$rds_code_array[$i]}.${lineorderheader_freight.$rds_code_array[$i]} ;
                    for ($m = 0; $m < count(${'survey_id_array' . $menu_id_array[$i]}); $m++) {
                        ${'datavalue' . $menu_id_array[$i]} .= ${'valueexcel' . ${'survey_id_array' . $menu_id_array[$i]}[$m] . $menu_id_array[$i]} . "\n";
                    }
                    if (${'datavalue' . $menu_id_array[$i]} == "") {
                        ${'datavalue' . $menu_id_array[$i]} = "\r\n(0) Records Found!\n";
                    }

                    $date = gmdate('d', strtotime('+330 minute'));
                    $month = gmdate('m', strtotime('+330 minute'));
                    $year = gmdate('Y', strtotime('+330 minute'));
                    $hour = gmdate('H', strtotime('+330 minute'));
                    $minute = gmdate('i', strtotime('+330 minute'));
                    $second = gmdate('s', strtotime('+330 minute'));

                    ${'file_content' . $menu_id_array[$i]} = ${'excelheader' . $menu_id_array[$i]} . "\n" . ${'excelsubheader' . $menu_id_array[$i]} . "\n" . ${'datavalue' . $menu_id_array[$i]};
                    ${'file_name' . $menu_id_array[$i]} = "$menu_name_array[$i]_$date$month$year$hour$minute$second.xls";

                    //add files to the zip, passing file contents, not actual files
                    //$zip->addFile($file_content1, $file_name1);
                    $zip->addFile(${'file_content' . $menu_id_array[$i]}, ${'file_name' . $menu_id_array[$i]});
                }

                header("Content-type: application/octet-stream");
                header("Content-Disposition: inline; filename=excel_files_survey.zip");
                echo $zip->file();
                exit();
            }
            //$sql_get_menu = "SELECT layout_name,menu_id FROM survey_input WHERE type='menu' AND survey_type='".$survey_type."' ORDER BY display_order ASC";die;
        }
            ?>