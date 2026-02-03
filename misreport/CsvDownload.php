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

 //For order header csv download
	$arr_header = array('Transaction Id','Employee name','Customer name','Order date','Sku name','Qty','Sale Rate/Mrp','Remarks');
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
	$sql_order_header_download = "SELECT OH.order_no,OH.customer_code,OH.d_instruction,OH.sale_type,LO.emp_code,
								DATE_FORMAT(LO.date,'%d/%m/%Y') AS order_date,OD.sku_code,OD.qty,PM.prod_desc,OD.sale_rate".$TD_condition." 
								FROM order_header OH,location LO,order_details OD,product_master PM
								WHERE LO.trans_id=OH.order_no AND OH.order_no=OD.order_no AND OH.dwnld_transferred='NO' 
								AND (LO.trans_id LIKE 'O%' OR LO.trans_id LIKE 'NO%') AND LO.emp_code!='C0007' AND OD.sku_code=PM.prod_code 
								ORDER BY DATE_FORMAT(LO.date,'%Y-%m-%d'),LO.trans_id DESC,PM.prod_desc ASC";
	$rs_order_header_download = mysqli_query($link,$sql_order_header_download) or die(mysqli_error()." Error in order header download: ".$sql_order_header_download);
	$lineorderheader = ''; 
	while($rec_order_header_download = mysqli_fetch_assoc($rs_order_header_download)) 
	{ 
		$customer_code=$rec_order_header_download['customer_code'];
		$emp_code=$rec_order_header_download['emp_code'];
		$order_date=$rec_order_header_download['order_date'];
		$sku_code=$rec_order_header_download['sku_code'];
		$qty=$rec_order_header_download['qty'];
		$sale_rate=$rec_order_header_download['sale_rate'];
		$order_no=$rec_order_header_download['order_no'];
		$d_instruction=preg_replace('/\s+/', ' ',$rec_order_header_download['d_instruction']);
		$sale_type=$rec_order_header_download['sale_type'];
		if($sale_type=='CASH')	$sale_type_value='CS';
		if($sale_type=='CREDIT')	$sale_type_value='CR';
		if($sale_type=='COD')	$sale_type_value='COD';	

		
		/*if(substr($customer_code,0,1)=='N')
		{
			$sqlcustomername="SELECT customer_name FROM prospective_customer_master WHERE customer_code='".$customer_code."'";
			$rscustomername=mysqli_query($link,$sqlcustomername);
			$rowcustomername=mysqli_fetch_assoc($rscustomername);
			$customer_name=$rowcustomername['customer_name'];
		}
		else
		{*/
			$sqlcustomername="SELECT customer_name,TD  FROM customer_master WHERE customer_code='".$customer_code."'";
			$rscustomername=mysqli_query($link,$sqlcustomername);
			$rowcustomername=mysqli_fetch_assoc($rscustomername);
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
		$rsempname=mysqli_query($link,$sqlempname);
		$rowempname=mysqli_fetch_assoc($rsempname);
		$emp_name=$rowempname['emp_name'];
		
		/*$sqlproductdetails="SELECT prod_desc FROM product_master WHERE prod_code='".$sku_code."'";
		$rsproductdetails=mysqli_query($link,$sqlproductdetails);
		$rowproductdetails=mysqli_fetch_assoc($rsproductdetails);*/
		$prod_desc=$rec_order_header_download['prod_desc'];
		
		$valueorderheader  = $order_no.",";
		$valueorderheader .= $emp_name.",";
		$valueorderheader .= $customer_name.",";
		$valueorderheader .= $order_date.",";
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
		$valueorderheader .= $TD_value.';'.$sale_type_value.';'.$d_instruction.",";
		//$valueorderheader .= stripslashes($rec_order_header_download['one_time_password']).",";
		//$valueorderheader .= stripslashes($rec_order_header_download['is_confirmed']).",";

		$lineorderheader  .= $valueorderheader."\r\n"; 
		$sqlupdateorderheader="UPDATE order_header SET dwnld_transferred='YES' WHERE order_no='".$order_no."'";
		$rsupdateorderheader=mysqli_query($link,$sqlupdateorderheader)or die(mysqli_error()." Error in order header update: ".$sqlupdateorderheader);
	}
	//$dataorderheader = str_replace("\r\n","",$lineorderheader);
	$dataorderheader = $lineorderheader;	
	if ($dataorderheader == "")
	{ 
		$dataorderheader = "\r\n(0) Records Found!\n";                         
    } 
	
	//For payment header csv download
	$arr_header = array('Transaction Id','Employee name','Customer name','Payment date','Cash_cheque','Cheque no','Bank','Invoice id','Amount','Remarks');
	$headerpayment_header = "";
	foreach($arr_header AS $header_value)
	{
		$headerpayment_header .= $header_value. ",";
	}
	$sql_payment_header_download = "SELECT LO.emp_code,PH.receipt_id,PH.customer_code,DATE_FORMAT(LO.date,'%d/%m/%Y') AS date,
									PH.cash_cheque,PH.cheque_no,PH.bank,PD.invoice_id,PD.amount,PH.p_remark
									FROM payment_header PH,location LO,payment_details PD WHERE PH.dwnld_transferred='NO' AND LO.trans_id=PH.receipt_id 
									AND (LO.trans_id LIKE 'P%' OR LO.trans_id LIKE 'NC%')AND PH.receipt_id=PD.receipt_id AND LO.emp_code!='C0007' 
									ORDER BY DATE_FORMAT(LO.date,'%Y-%m-%d') DESC";
	$rs_payment_header_download = mysqli_query($link,$sql_payment_header_download) or die(mysqli_error()." Error in payment header download: ".$sql_payment_header_download);
	$linepaymentheader = ''; 
	while($rec_payment_header_download = mysqli_fetch_assoc($rs_payment_header_download)) 
	{ 
		$customer_code=$rec_payment_header_download['customer_code'];
		$emp_code=$rec_payment_header_download['emp_code'];
		$receipt_id=$rec_payment_header_download['receipt_id'];

		/*if(substr($customer_code,0,1)=='N')
		{
			$sqlcustomername="SELECT customer_name FROM prospective_customer_master WHERE customer_code='".$customer_code."'";
			$rscustomername=mysqli_query($link,$sqlcustomername);
			$rowcustomername=mysqli_fetch_assoc($rscustomername);
			$customer_name=$rowcustomername['customer_name'];
		}
		else
		{*/
			$sqlcustomername="SELECT customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
			$rscustomername=mysqli_query($link,$sqlcustomername);
			$rowcustomername=mysqli_fetch_assoc($rscustomername);
			$customer_name=$rowcustomername['customer_name'];
		//}
		
		$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
		$rsempname=mysqli_query($link,$sqlempname);
		$rowempname=mysqli_fetch_assoc($rsempname);
		$emp_name=$rowempname['emp_name'];
		
		$valuepaymentheader = $receipt_id.",";
		$valuepaymentheader .= $emp_name.",";
		$valuepaymentheader .= $customer_name.",";
		if(substr($receipt_id,0,1)=='P')
		{
			$valuepaymentheader .= stripslashes($rec_payment_header_download['date']).",";
			$valuepaymentheader .= stripslashes($rec_payment_header_download['cash_cheque']).",";
			$valuepaymentheader .= stripslashes($rec_payment_header_download['cheque_no']).",";
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
			$valuepaymentheader .= 'No Amount'.",";
		}
		$p_remark=str_replace(',','',$rec_payment_header_download['p_remark']);
		$p_remark=preg_replace('/\s+/', ' ',$p_remark);
		$valuepaymentheader .= stripslashes($p_remark).",";

		$linepaymentheader  .= $valuepaymentheader."\r\n"; 
		$sqlupdatepaymentheader="UPDATE payment_header SET dwnld_transferred='YES' WHERE receipt_id='".$receipt_id."'";
		$rsupdatepaymentheader=mysqli_query($link,$sqlupdatepaymentheader)or die(mysqli_error()." Error in payment header update: ".$sqlupdatepaymentheader);
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
							WHERE PCH.dwnld_transferred='NO' AND LO.trans_id=PCH.trans_id AND PCH.trans_id=PCD.trans_id AND LO.trans_id LIKE 'D%'
							AND LO.emp_code!='C0007' ORDER BY DATE_FORMAT(LO.date,'%Y-%m-%d') DESC";
	$rs_prospect_download = mysqli_query($link,$sql_prospect_download) or die(mysqli_error()." Error in prospect download: ".$sql_prospect_download);
	$lineprospect = ''; 
	while($rec_prospect_download = mysqli_fetch_assoc($rs_prospect_download)) 
	{ 
		$emp_code=$rec_prospect_download['emp_code'];
		$sku_code=$rec_prospect_download['product_code'];
		$trans_id=$rec_prospect_download['trans_id'];
		$remarks=$rec_prospect_download['remarks'];
		$remarks=preg_replace('/\s+/', ' ',$remarks);

		$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
		$rsempname=mysqli_query($link,$sqlempname);
		$rowempname=mysqli_fetch_assoc($rsempname);
		$emp_name=$rowempname['emp_name'];
		
		$sqlproductdetails="SELECT prod_desc FROM product_master WHERE prod_code='".$sku_code."'";
		$rsproductdetails=mysqli_query($link,$sqlproductdetails);
		$rowproductdetails=mysqli_fetch_assoc($rsproductdetails);
		$prod_desc=$rowproductdetails['prod_desc'];


		$address=$rec_retail_download['address'];
		$address=str_replace(',','|',$address);
		$valueprospect  = stripslashes($trans_id).",";
		$valueprospect .= stripslashes($emp_name).",";
		$valueprospect .= stripslashes($rec_prospect_download['customer_name']).",";
		$valueprospect .= stripslashes($address).",";
		$valueprospect .= stripslashes($rec_prospect_download['pin']).",";
		$valueprospect .= stripslashes($rec_prospect_download['phone_no']).",";
		$valueprospect .= stripslashes($rec_prospect_download['date']).",";
		$valueprospect .= stripslashes($prod_desc).",";
		$valueprospect .= stripslashes($remarks).",";

		$lineprospect  .= $valueprospect."\r\n"; 
		$sqlupdateprospectheader="UPDATE prospective_customer_header SET dwnld_transferred='YES' WHERE  trans_id ='".$trans_id."'";
		$rsupdateprospectheader=mysqli_query($link,$sqlupdateprospectheader)or die(mysqli_error()." Error in prospect header update: ".$sqlupdateprospectheader);

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
	$nick_name=$_REQUEST['nick_name'];
	$fullPath = "/home/acedns/public_html/csvdownload/$nick_name/csv_files.zip";
	
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
		mysqli_query($link,"ROLLBACK");
	}
	
	mysqli_close($link);
?>	