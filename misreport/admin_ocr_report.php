<?php
ob_start();
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

disphtml("main();");

function main(){
    require("include/dbcon.php");
    ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <?php
    /* ---------- Zone dropdown (from employee_master) ---------- */
    $zone_options = "";
    $sql_zone = "SELECT DISTINCT zone FROM employee_master WHERE zone != '' ORDER BY zone ASC";
    $res_zone = mysqli_query($link, $sql_zone);
    while($r = mysqli_fetch_assoc($res_zone)){
        $zone_options .= "<option value=\"".htmlspecialchars($r['zone'])."\">".htmlspecialchars($r['zone'])."</option>";
    }

    /* ---------- Branch dropdown (from branch_master) ---------- */
    $branch_options = "";
    $sql_branch = "SELECT branch_code, branch_name FROM branch_master WHERE acedns='Y' ORDER BY branch_name ASC";
    $res_branch = mysqli_query($link, $sql_branch);
    while($r = mysqli_fetch_assoc($res_branch)){
        $branch_options .= "<option value=\"".htmlspecialchars($r['branch_code'])."\">".htmlspecialchars($r['branch_name'])."</option>";
    }

    echo "<center>";
    echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
    echo "<span style=\"font-weight:bold; font-size:14px;\">OCR REPORT</span><br><br>";
    ?>
    <table cellpadding="4" cellspacing="0" style="margin:0 auto;">
      <tr>
        <td align="right"><b>Report Type</b></td>
        <td>
          <select id="report_type" name="report_type">
            <option value="ocr">OCR Data</option>
            <option value="participants">Participants Data</option>
          </select>
        </td>
        <td align="right"><b>Zone</b></td>
        <td>
          <select id="zone" name="zone">
            <option value="">All</option>
            <?php echo $zone_options; ?>
          </select>
        </td>
        <td align="right"><b>Branch</b></td>
        <td>
          <select id="branch" name="branch">
            <option value="">All</option>
            <?php echo $branch_options; ?>
          </select>
        </td>
      </tr>
      <tr>
        <td align="right"><b>From Date</b></td>
        <td><input type="date" id="start_date" name="start_date" /></td>
        <td align="right"><b>To Date</b></td>
        <td><input type="date" id="end_date" name="end_date" /></td>
        <td colspan="2" align="center">
          <input type="button" value="Show Report" onClick="display_result();" style="cursor:pointer;" />
        </td>
      </tr>
    </table>
    <?php
    echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
    echo "<br>";
    ?>
    <div id="loader" style="display:none"><br/><center><img src="ajax-loader.gif" /></center></div>
    <div id="display" style="max-height: 500px; max-width:1150px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    <div style="width:100%;" align="right" id="print_export" hidden>
        <input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
        <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();">
    </div>
    <?php
    echo "</center>";
    ?>
    <script>
    function GetXmlHttpObject(){
        var xmlHttp=null;
        try{ xmlHttp=new XMLHttpRequest(); }
        catch (e){
            try{ xmlHttp=new ActiveXObject("Msxml2.XMLHTTP"); }
            catch (e){ xmlHttp=new ActiveXObject("Microsoft.XMLHTTP"); }
        }
        return xmlHttp;
    }

    function display_result(){
        var report_type = document.getElementById("report_type").value;
        var zone        = document.getElementById("zone").value;
        var branch      = document.getElementById("branch").value;
        var start_date  = document.getElementById("start_date").value;
        var end_date    = document.getElementById("end_date").value;

        if(start_date != '' && end_date != '' && start_date > end_date){
            alert("Start date cannot be greater than end date");
            return false;
        }

        document.getElementById('loader').style.display='';
        document.getElementById('display').innerHTML='';
        document.getElementById("print_export").hidden = true;

        var url    = "admin_ocr_report_data.php";
        var params = 'report_type='+encodeURIComponent(report_type)
                   + '&zone='+encodeURIComponent(zone)
                   + '&branch='+encodeURIComponent(branch)
                   + '&start_date='+encodeURIComponent(start_date)
                   + '&end_date='+encodeURIComponent(end_date);

        var xmlHttpdisplay = GetXmlHttpObject();
        if(xmlHttpdisplay==null){ alert("Browser does not support HTTP Request"); return; }
        xmlHttpdisplay.open("POST", url, true);
        xmlHttpdisplay.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlHttpdisplay.onreadystatechange = function(){
            if(xmlHttpdisplay.readyState == 4 && xmlHttpdisplay.status == 200){
                document.getElementById('loader').style.display='none';
                document.getElementById('display').innerHTML = xmlHttpdisplay.responseText;
                document.getElementById("print_export").hidden = false;
            }
        }
        xmlHttpdisplay.send(params);
    }

    function PrintElem(elem){
        var displaydiv = document.getElementById("display").innerHTML;
        Popup(displaydiv);
    }

    function Popup(data){
        var mywindow = window.open('', 'OCR Report', 'height=400,width=600');
        mywindow.document.write('<html><head><title>OCR Report</title>');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('<p align=right><b>Powered By ACEdns</b></p></body></html>');
        mywindow.document.close();
        mywindow.focus();
        mywindow.print();
        mywindow.close();
        return true;
    }

    function exporttocsv(){
        var tab = document.getElementById('display_table');
        if(!tab){ alert('No data to export'); return; }

        var dt = new Date();
        var postfix = dt.getDate()+"."+(dt.getMonth()+1)+"."+dt.getFullYear()+"_"+dt.getHours()+"."+dt.getMinutes();

        var tab_text="<table border='2px'>";
        for(var j=0 ; j<tab.rows.length ; j++){
            tab_text = tab_text + "<tr>" + tab.rows[j].innerHTML + "</tr>";
        }
        tab_text = tab_text + "</table>";
        tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");     // remove links
        tab_text = tab_text.replace(/<img[^>]*>/gi, "");        // remove images
        tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // remove inputs

        var report_type = document.getElementById("report_type").value;
        var fname = (report_type == 'participants' ? 'OCR_Participants_Data' : 'OCR_Data');

        var a = document.createElement('a');
        a.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(tab_text);
        a.download = fname + '_' + postfix + '.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
    </script>
    <?php
}
?>
