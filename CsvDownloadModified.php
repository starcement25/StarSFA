<?php
	ob_start();
	set_time_limit(1000);
	error_reporting(E_ALL ^ E_NOTICE);
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	$nick_name='UCLINDIA';

	require("include/config-setup.php");
	define("DB","acedns_UCLINDIA");
	//require("include/dbcon.php");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	require("include/config-email-setup.php");
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

 //For location,order header and order details csv download
	$arr_loction = array('Emp code','Trans id','Date','Latt','Longi','Transferred');
	$headerorder_header = "";
	foreach($arr_loction AS $location_value)
	{
		$location_header .= $location_value. ",";
	}
	$sql_location_download = "SELECT EM.dns_emp_code,LO.trans_id,LO.date,LO.updatetime,LO.latt,LO.longi 
							 FROM employee_master EM,location LO
							 WHERE LO.emp_code=EM.emp_code AND LO.transferred='NO'";
	$rs_location_download = mysqli_query($link,$sql_location_download) or die(mysqli_error()." Error in location download: ".$sql_location_download);
	$count_location_download=mysqli_num_rows($rs_location_download);
	$linelocation = ''; 
	while($rec_location_download = mysqli_fetch_assoc($rs_location_download)) 
	{ 
		$dns_emp_code=$rec_location_download['dns_emp_code'];
		$trans_id=$rec_location_download['trans_id'];
		$date=$rec_location_download['date'];
		$updatetime=$rec_location_download['updatetime'];
		$latt=$rec_location_download['latt'];
		$longi=$rec_location_download['longi'];
		
		/*if(substr($customer_code,0,1)=='N')
		{
			$sqlcustomername="SELECT customer_name FROM prospective_customer_master WHERE customer_code='".$customer_code."'";
			$rscustomername=mysqli_query($link,$sqlcustomername);
			$rowcustomername=mysqli_fetch_assoc($rscustomername);
			$customer_name=$rowcustomername['customer_name'];
		}
		else
		{
			$sqlcustomername="SELECT customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
			$rscustomername=mysqli_query($link,$sqlcustomername);
			$rowcustomername=mysqli_fetch_assoc($rscustomername);
			$customer_name=$rowcustomername['customer_name'];
		}
		
		$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
		$rsempname=mysqli_query($link,$sqlempname);
		$rowempname=mysqli_fetch_assoc($rsempname);
		$emp_name=$rowempname['emp_name'];
		
		$sqlproductdetails="SELECT prod_desc FROM product_master WHERE prod_code='".$sku_code."'";
		$rsproductdetails=mysqli_query($link,$sqlproductdetails);
		$rowproductdetails=mysqli_fetch_assoc($rsproductdetails);
		$prod_desc=$rowproductdetails['prod_desc'];*/
		
		$valuelocation  = $dns_emp_code.",";
		$valuelocation .= $trans_id.",";
		$valuelocation .= $date.",";
		//$valuelocation .= $updatetime.",";
		$valuelocation .= $latt.",";
		$valuelocation .= $longi.",";
		$valuelocation .= 'NO'.",";

		$linelocation  .= $valuelocation."\n"; 
		$sqlupdatelocation="UPDATE location SET transferred='YES' WHERE trans_id='".$trans_id."'";
		$rsupdatelocation=mysqli_query($link,$sqlupdatelocation)or die(mysqli_error()." Error in location update: ".$sqlupdatelocation);
	}
	$datalocation = str_replace("\r","",$linelocation);		
	if ($datalocation == "")
	{ 
		$datalocation = "\n(0) Records Found!\n";                         
    } 
	
	 //For order header csv download
	$arr_header = array('Order no','Customer code','Branch code','Sale Type','Order value','TD','Tag distributor code','Remarks','Transferred');
	$headerorder_header = "";
	foreach($arr_header AS $header_value)
	{
		$headerorder_header .= $header_value. ",";
	}
	$sql_order_header_download = "SELECT OH.order_no,CM.customer_code,CM.dns_customer_code,OH.sale_type,OH.order_value,OH.TD,OH.tag_distributor_code,
								OH.d_instruction FROM order_header OH,customer_master CM
								WHERE OH.customer_code=CM.customer_code AND OH.transferred='NO'";
	$rs_order_header_download = mysqli_query($link,$sql_order_header_download) or die(mysqli_error()." Error in order header download: ".$sql_order_header_download);
	$count_order_header_download=mysqli_num_rows($rs_order_header_download);
	$lineorderheader = ''; 
	while($rec_order_header_download = mysqli_fetch_assoc($rs_order_header_download)) 
	{ 
		$order_no=$rec_order_header_download['order_no'];
		$customer_code=$rec_order_header_download['customer_code'];
		$dns_customer_code=$rec_order_header_download['dns_customer_code'];
		$dns_branch_code='';
		$sale_type=$rec_order_header_download['sale_type'];
		$order_value=$rec_order_header_download['order_value'];
		$TD=$rec_order_header_download['TD'];
		$tag_distributor_code=$rec_order_header_download['tag_distributor_code'];
		
		$d_instruction=preg_replace('/\s+/', ' ',$rec_order_header_download['d_instruction']);
		/*if($sale_type=='CASH')	$sale_type_value='CS';
		if($sale_type=='CREDIT')	$sale_type_value='CR';
		if($sale_type=='COD')	$sale_type_value='COD';	*/

		if(substr($customer_code,0,1)=='N')
		{
			$dns_customer_code=$customer_code;
		}
		
		$valueorderheader  = $order_no.",";
		$valueorderheader .= $dns_customer_code.",";
		$valueorderheader .= $dns_branch_code.",";
		$valueorderheader .= $sale_type.",";
		$valueorderheader .= $order_value.",";
		$valueorderheader .= $TD.",";
		$valueorderheader .= $tag_distributor_code.",";
		$valueorderheader .= $d_instruction.",";
		$valueorderheader .= 'NO'.",";

		$lineorderheader  .= $valueorderheader."\r\n"; 
		$sqlupdateorderheader="UPDATE order_header SET transferred='YES' WHERE order_no='".$order_no."'";
		$rsupdateorderheader=mysqli_query($link,$sqlupdateorderheader)or die(mysqli_error()." Error in order header update: ".$sqlupdateorderheader);
	}
	//$dataorderheader = str_replace("\r\n","",$lineorderheader);
	$dataorderheader = $lineorderheader;	
	if ($dataorderheader == "")
	{ 
		$dataorderheader = "\r\n(0) Records Found!\n";                         
    } 
	
	//For Order details csv download	
	$arr_details = array('Order no','Prod code','qty','mp code','TD','Sale rate','amount','Transferred');
	$headerorder_details = "";
	foreach($arr_details AS $header_value)
	{
		$headerorder_details .= $header_value. ",";
	}
	$sql_order_details_download = "SELECT OD.order_no,PM.dns_prod_code,OD.qty,OD.mrp_code,OD.TD,OD.sale_rate,OD.amount FROM order_details OD,product_master PM
								WHERE OD.sku_code=PM.prod_code AND OD.transferred='NO'";
	$rs_order_details_download = mysqli_query($link,$sql_order_details_download) or die(mysqli_error()." Error in order details download: ".$sql_order_details_download);
	$count_order_details_download=mysqli_num_rows($rs_order_details_download);
	$lineorderdetails = ''; 
	while($rec_order_details_download = mysqli_fetch_assoc($rs_order_details_download)) 
	{ 
		$order_no=$rec_order_details_download['order_no'];
		$dns_prod_code=$rec_order_details_download['dns_prod_code'];
		$qty=$rec_order_details_download['qty'];
		$mrp_code=$rec_order_details_download['mrp_code'];
		$TD=$rec_order_details_download['TD'];
		$sale_rate=$rec_order_details_download['sale_rate'];
		$amount=$rec_order_details_download['amount'];
		
		$sqlselectdnsmrpcode="SELECT dns_mrp_code FROM mrp WHERE mrp_code='".$mrp_code."'";
		$rsselectdnsmrpcode=mysqli_query($link,$sqlselectdnsmrpcode);
		$rowselectdnsmrpcode=mysqli_fetch_assoc($rsselectdnsmrpcode);
		$dns_mrp_code=$rowselectdnsmrpcode['dns_mrp_code'];
		
		$valueorderdetails  = $order_no.",";
		$valueorderdetails .= $dns_prod_code.",";
		$valueorderdetails .= $qty.",";
		$valueorderdetails .= $dns_mrp_code.",";
		$valueorderdetails .= $TD.",";
		$valueorderdetails .= $sale_rate.",";
		$valueorderdetails .= $amount.",";
		$valueorderdetails .= 'NO'.",";

		$lineorderdetails  .= $valueorderdetails."\r\n"; 
		$sqlupdateorderdetails="UPDATE order_details SET transferred='YES' WHERE order_no='".$order_no."'";
		$rsupdateorderdetails=mysqli_query($link,$sqlupdateorderdetails)or die(mysqli_error()." Error in order details update: ".$sqlupdateorderdetails);
	}
	//$dataorderheader = str_replace("\r\n","",$lineorderheader);
	$dataorderdetails = $lineorderdetails;	
	if($dataorderdetails == "")
	{ 
		$dataorderdetails = "\r\n(0) Records Found!\n";                         
    }

	//For New customer csv download	
	$arr_details_customer = array('Customer code','Customer name','Address','Pin','Phone no','Route code','Emp code','Branch code','Acedns','Black list','Cust type','Rds tag','transferred');
	$headernewcustomer = "";
	foreach($arr_details_customer AS $header_value_customer)
	{
		$headernewcustomer .= $header_value_customer. ",";
	}
	$sql_customer_download = "SELECT customer_code,customer_name,address,pin,phone_no,route_code,emp_code,branch_code,acedns,black_list,cust_type,rds_tag FROM 
								customer_master WHERE SUBSTRING(customer_code,1,1)='N' AND transferred='NO'";
	$rs_customer_download = mysqli_query($link,$sql_customer_download) or die(mysqli_error()." Error in new customer download: ".$sql_customer_download);
	$count_customer_download=mysqli_num_rows($rs_customer_download);
	$linenewcustomer = ''; 
	while($rec_customer_download = mysqli_fetch_assoc($rs_customer_download)) 
	{ 
		
		$new_customer_code=$rec_customer_download['customer_code'];
		$customer_name=str_replace(',',' ',$rec_customer_download['customer_name']);
		$address=$rec_customer_download['address'];
		$address=str_replace(',',' ',$rec_customer_download['address']);
		$address=preg_replace('/[\r\n]+/', '',$address);
		$pin=$rec_customer_download['pin'];
		$phone_no=$rec_customer_download['phone_no'];
		$route_code=$rec_customer_download['route_code'];
		$emp_code=$rec_customer_download['emp_code'];
		$branch_code=$rec_customer_download['branch_code'];
		$acedns=$rec_customer_download['acedns'];
		$black_list=$rec_customer_download['black_list'];
		$cust_type=$rec_customer_download['cust_type'];
		$rds_tag=$rec_customer_download['rds_tag'];
		
		$sqlselectdnsempcode="SELECT dns_emp_code FROM employee_master WHERE emp_code='".$emp_code."'";
		$rsselectdnsempcode=mysqli_query($link,$sqlselectdnsempcode);
		$rowselectdnsempcode=mysqli_fetch_assoc($rsselectdnsempcode);
		$dns_emp_code=$rowselectdnsempcode['dns_emp_code'];
		
		$sqlselectdnsroutecode="SELECT dns_route_code FROM route_master WHERE route_code='".$route_code."'";
		$rsselectdnsroutecode=mysqli_query($link,$sqlselectdnsroutecode);
		$rowselectdnsroutecode=mysqli_fetch_assoc($rsselectdnsroutecode);
		$dns_route_code=$rowselectdnsroutecode['dns_route_code'];

		$valuenewcustomer  = $new_customer_code.",";
		$valuenewcustomer .= $customer_name.",";
		$valuenewcustomer .= $address.",";
		$valuenewcustomer .= $pin.",";
		$valuenewcustomer .= $phone_no.",";
		$valuenewcustomer .= $dns_route_code.",";
		$valuenewcustomer .= $dns_emp_code.",";
		$valuenewcustomer .= $branch_code.",";
		$valuenewcustomer .= $acedns.",";
		$valuenewcustomer .= $black_list.",";
		$valuenewcustomer .= $cust_type.",";
		$valuenewcustomer .= $rds_tag.",";
		$valuenewcustomer .= 'NO'.",";

		$linenewcustomer  .= $valuenewcustomer."\r\n"; 
		$sqlupdatenewcustomer="UPDATE customer_master SET transferred='YES' WHERE customer_code='".$new_customer_code."'";
		$rsupdatenewcustomer=mysqli_query($link,$sqlupdatenewcustomer)or die(mysqli_error()." Error in new customer update: ".$sqlupdatenewcustomer);
	}
	//$dataorderheader = str_replace("\r\n","",$lineorderheader);
	$datanewcustomer = $linenewcustomer;	
	if($datanewcustomer == "")
	{ 
		$datanewcustomer = "\r\n(0) Records Found!\n";                         
    }
	
	$file_content1= "$location_header\n$datalocation";
	$file_name1="Location.csv";
	$file_content2= "$headerorder_header\n$dataorderheader";
	$file_name2="Order header.csv";
	$file_content3= "$headerorder_details\n$dataorderdetails";
	$file_name3="Order details.csv";
	$file_content4= "$headernewcustomer\n$datanewcustomer";
	$file_name4="New customer.csv";
	
	$zip = new ZipFile();
	
	//add files to the zip, passing file contents, not actual files
	//$zip->addFile($file_content1, $file_name1);
	if($datalocation!='')
	{
		$zip->addFile($file_content1, $file_name1);
	}
	if($dataorderheader!='')
	{
		$zip->addFile($file_content2, $file_name2);
	}
	if($dataorderdetails!='')
	{
		$zip->addFile($file_content3, $file_name3);
	}
	if($datanewcustomer!='')
	{
		$zip->addFile($file_content4, $file_name4);
	}
	
	$nick_name='UCLINDIA';
	$fullPath = "/home/acedns/public_html/acednsproduct/csvdownload/$nick_name/csv_files.zip";
	
	$folderName = $nick_name;
	if ( !file_exists("csvdownload/$folderName")){
		mkdir("csvdownload/$folderName");
		chmod("csvdownload/$folderName", 0777);
	}
	if($count_location_download >0 || $count_order_header_download >0 || $count_order_details_download >0 || $count_customer_download >0)
		{
			$contents=$zip->file();
			$destination = "csvdownload/$folderName/csv_files.zip";
			@unlink($destination);
			$file = fopen($destination, "w+");
			fputs($file, $contents);
			fclose($file);
		}
	//echo $zip->file();
	//echo $zip->file();
	$successval=1;
	if($successval!=1){
		mysqli_query($link,"ROLLBACK");
		echo '0';
	}
	else
	{
		if($count_location_download >0 || $count_order_header_download >0 || $count_order_details_download >0 || $count_customer_download >0)
		{
		  echo trim('1');
		}
		else
		{
			echo trim('0');
		}
	}
?>	