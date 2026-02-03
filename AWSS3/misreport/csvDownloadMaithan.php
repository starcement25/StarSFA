<?php
	ob_start();
	session_start();
	require("adminUtils.php");
	//if($_SESSION['admin_login']=="")  		header("location:index.php");
	ob_end_flush();
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

 //For order related csv download
	$arr_header = array('Order no','Date','Partyname','Source','destination','Business type','Taxtype','Transporter','item name','qty','rate','delivery due date','amount');
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
	$sql_order_header_download = "SELECT OH.order_no,OH.customer_code,OH.d_instruction,OH.destination_code,OH.order_type,LO.emp_code,
								DATE_FORMAT(LO.date,'%d/%m/%Y') AS order_date,OD.sku_code,OD.qty,PM.prod_desc,OD.sale_rate,OD.amount".$TD_condition." 
								FROM order_header OH,location LO,order_details OD,product_master PM
								WHERE LO.trans_id=OH.order_no AND OH.order_no=OD.order_no AND OH.transferred='NO' 
								AND (LO.trans_id LIKE 'O%') AND LO.emp_code!='C0007' AND OD.sku_code=PM.prod_code 
								ORDER BY DATE_FORMAT(LO.date,'%Y-%m-%d'),LO.trans_id DESC,PM.prod_desc ASC";
	$rs_order_header_download = mysql_query($sql_order_header_download) or die(mysql_error()." Error in order header download: ".$sql_order_header_download);
	$lineorderheader = ''; 
	while($rec_order_header_download = mysql_fetch_array($rs_order_header_download)) 
	{ 
		$customer_code=$rec_order_header_download['customer_code'];
		$emp_code=$rec_order_header_download['emp_code'];
		$order_date=$rec_order_header_download['order_date'];
		$sku_code=$rec_order_header_download['sku_code'];
		$qty=$rec_order_header_download['qty'];
		$sale_rate=$rec_order_header_download['sale_rate'];
		$order_no=$rec_order_header_download['order_no'];
		$d_instruction=preg_replace('/\s+/', ' ',$rec_order_header_download['d_instruction']);
		$order_type=$rec_order_header_download['order_type'];
		$destination_code=$rec_order_header_download['destination_code'];
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
			$sqlcustomername="SELECT customer_name,TD  FROM customer_master WHERE customer_code='".$customer_code."'";
			$rscustomername=mysql_query($sqlcustomername);
			$rowcustomername=mysql_fetch_array($rscustomername);
			$customer_name=$rowcustomername['customer_name'];
		//}
		
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
		$sqldestination="SELECT destination_name FROM destination_master WHERE destination_code='".$destination_code."'";
		$rsdestination=mysql_query($sqldestination);
		$rowdestination=mysql_fetch_array($rsdestination);
		$destination_name=$rowdestination['destination_name'];
		
		$prod_desc=$rec_order_header_download['prod_desc'];
		$valueorderheader  = $order_no.",";
		$valueorderheader .= $order_date.",";
		$valueorderheader .= $customer_name.",";
		$valueorderheader .= $emp_name.",";
		$valueorderheader .= $destination_name.",";
		$valueorderheader .= $order_type.",";
		$valueorderheader .= ''.",";
		$valueorderheader .= ''.",";
		if(substr($order_no,0,1)=='O')
		{
			$valueorderheader .= $prod_desc.",";
			$valueorderheader .= $qty.",";
			$valueorderheader .= $sale_rate.",";
		}
		else
		{
			$valueorderheader .= 'NO SKU'.",";
			$valueorderheader .= '0'.",";
			$valueorderheader .= '0'.",";
		}
		$valueorderheader .= str_replace(',','',$d_instruction).",";
		$valueorderheader .= $amount.",";
		//$valueorderheader .= stripslashes($rec_order_header_download['one_time_password']).",";
		//$valueorderheader .= stripslashes($rec_order_header_download['is_confirmed']).",";

		$lineorderheader  .= $valueorderheader."\r\n"; 
		$sqlupdateorderheader="UPDATE order_header SET transferred='YES' WHERE order_no='".$order_no."'";
		$rsupdateorderheader=mysql_query($sqlupdateorderheader)or die(mysql_error()." Error in order header update: ".$sqlupdateorderheader);
	}
	//$dataorderheader = str_replace("\r\n","",$lineorderheader);
	$dataorderheader = $lineorderheader;	
	if ($dataorderheader == "")
	{ 
		$dataorderheader = "\r\n(0) Records Found!\n";                         
    } 
	
	$file_content1= "$headerorder_header\r\n$dataorderheader";
	$file_name1="Sales Order Details.csv";
	
	$zip = new ZipFile();
	
	//add files to the zip, passing file contents, not actual files
	//$zip->addFile($file_content1, $file_name1);
	$zip->addFile($file_content1, $file_name1);
	
	//$nick_name=$_SESSION['nick_name'];
	$nick_name=$_REQUEST['nick_name'];
	$fullPath = "/home/acedns/public_html/acednsproduct/csvdownload/$nick_name/csv_files.zip";
	
	//prepare the proper content type
	//header("Content-type: application/octet-stream");
	//header("Content-Disposition: inline; filename=csv_files.zip");
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
?>	