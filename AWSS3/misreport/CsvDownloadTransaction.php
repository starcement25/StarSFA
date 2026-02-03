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

 
$branch_name=$_REQUEST['branch_name']; 
$rds_name=$_REQUEST['rds_name']; 
if($branch_name!='' || $rds_name!='')
{
	if($branch_name!='') 
	{
		$sqlbranch="SELECT branch_code FROM branch_master WHERE branch_name='".$branch_name."'";
		$rsbranch=mysql_query($sqlbranch);
		$cntbranch=mysql_num_rows($rsbranch);
		$rowbranch=mysql_fetch_array($rsbranch);
		$branch_code=$rowbranch['branch_code'];
		if($cntbranch ==0)
		{
			echo 'No branch or depot exists of this names';
			exit();
		}
	}
	if($rds_name!='')
	{
		$sqlrds="SELECT rds_code,emp_code FROM rds_master WHERE rds_name='".$rds_name."'";
		$rsrds=mysql_query($sqlrds);
		$cntrds=mysql_num_rows($rsrds);
		$rowrds=mysql_fetch_array($rsrds);
		$rds_code=$rowrds['rds_code'];
		$emp_code=$rowrds['emp_code'];
		
		$sqlbranchname="SELECT BM.branch_name FROM branch_master BM,employee_master EM WHERE EM.branch_code=BM.branch_code 
					AND EM.emp_code='".$emp_code."'";
		$rsbranchname=mysql_query($sqlbranchname);
		$rowbranchname=mysql_fetch_array($rsbranchname);
		$branch_name=$rowbranchname['branch_name'];
		if($cntrds ==0)
		{
			echo 'No branch or depot exists of this name';
			exit();
		}
	}
	if($cntbranch >0 || $cntrds >0)
	{
		if($branch_name!='' && $cntbranch >0)
		{
			$condition=" AND BM.branch_code='".$branch_code."'";
		}
		if($rds_name!='' && $cntrds >0)
		{
			$condition.=" AND RM.rds_code='".$rds_code."'";
		}
		 //For Transaction csv download
		$arr_header = array('Branch name','Rds name','Employee Nme','Transaction Date','Transaction Id','Customer name','Sku name','Qty','Sale Rate',
		'Amount','VAT','Transaction type','Delivery instruction');
		$headerorder_header = "";
		foreach($arr_header AS $header_value)
		{ 
			$headerorder_header .= $header_value. ",";
		}
		$sql_order_header_download = "select BM.branch_name,RM.rds_name,EM.emp_name,EM.emp_code,DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') 
										AS transaction_date,LO.trans_id,
									OD.order_no,CM.customer_name,PM.prod_desc, OD.qty, OD.sale_rate,(OD.qty*OD.sale_rate) AS Amount,
									OD.amount AS input_amount,OH.VAT,
									OH.transaction_type,OH.d_instruction,RM.rds_code from order_details OD,customer_master CM,product_master PM, 
									order_header OH,rds_master RM,branch_master BM,employee_master EM,location LO where OH.customer_code = CM.customer_code 
									and OD.sku_code = PM.prod_code and OD.order_no = OH.order_no AND SUBSTRING(OH.order_no,2,1)!='C' AND 
									CM.rds_tag=RM.rds_code AND CM.branch_code=BM.branch_code AND CM.emp_code=EM.emp_code AND LO.trans_id=OH.order_no 
									".$condition." AND OH.downld_transferred='NO'
																	UNION ALL
									select BM.branch_name,RM.rds_name,EM.emp_name,EM.emp_code,DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') 
										AS transaction_date,LO.trans_id,OD.order_no, 
									VM.vendor_name AS customer_name,PM.prod_desc, OD.qty, OD.sale_rate,(OD.qty*OD.sale_rate) AS Amount,
									OD.amount AS input_amount,OH.VAT,
									OH.transaction_type, OH.d_instruction,RM.rds_code from order_details OD,vendor_master VM,product_master PM,order_header OH,
									rds_master RM,branch_master BM,employee_master EM,location LO where OH.customer_code = VM.vendor_code 
									and OD.sku_code = PM.prod_code and OD.order_no = OH.order_no AND SUBSTRING(OH.order_no,2,1)!='C' AND 
									VM.rds_code=RM.rds_code AND VM.branch_code=BM.branch_code AND VM.emp_code=EM.emp_code AND LO.trans_id=OH.order_no 
									".$condition." AND OH.downld_transferred='NO' 
                                    								UNION ALL
                                    select  BM.branch_name,RM.rds_name,EM.emp_name,EM.emp_code,DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') 
										AS transaction_date,LO.trans_id,
									OD.order_no,RM.rds_name AS customer_name,PM.prod_desc, OD.qty, OD.sale_rate,(OD.qty*OD.sale_rate) AS Amount,
									OD.amount AS input_amount,OH.VAT,
									OH.transaction_type, OH.d_instruction,RM.rds_code from order_details OD,product_master PM,order_header OH,
									rds_master RM,branch_master BM,employee_master EM,location LO where SUBSTRING(OH.order_no,2,5) = RM.emp_code 
									AND OH.transaction_type IN('ST','BT') AND OD.sku_code = PM.prod_code and OD.order_no = OH.order_no 
									AND SUBSTRING(OH.order_no,2,1)!='C'  AND EM.branch_code=BM.branch_code AND RM.emp_code=EM.emp_code 
									AND LO.trans_id=OH.order_no ".$condition." AND OH.downld_transferred='NO' 
                                    ORDER BY rds_name,DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d'),trans_id,transaction_type,prod_desc";
		//exit();
		$rs_order_header_download = mysql_query($sql_order_header_download) or die(mysql_error()." Error in order header download: ".$sql_order_header_download);
		$lineorderheader = '';
		$rds_code_array=array();
		$rds_name_array=array();
		$emp_code_array=array();
		while($rec_order_header_download = mysql_fetch_array($rs_order_header_download)) 
		{ 
			$branch_name=$rec_order_header_download['branch_name'];
			$rds_name=$rec_order_header_download['rds_name'];
			$emp_name=$rec_order_header_download['emp_name'];
			$transaction_date=$rec_order_header_download['transaction_date'];
			$order_no=$rec_order_header_download['order_no'];
			$sale_rate=$rec_order_header_download['sale_rate'];
			$customer_name=$rec_order_header_download['customer_name'];
			$customer_name=str_replace(',','',$customer_name);
			$prod_desc=$rec_order_header_download['prod_desc'];
			$qty=$rec_order_header_download['qty'];
			$Amount=$rec_order_header_download['Amount'];
			$input_amount=$rec_order_header_download['input_amount'];
			if($input_amount >0)
			{
				$Amount=$input_amount;
				//$sale_rate=0;
			}
			$VAT=$rec_order_header_download['VAT'];
			$transaction_type=$rec_order_header_download['transaction_type'];
			$d_instruction=$rec_order_header_download['d_instruction'];
			$d_instruction=preg_replace('/\s+/', '',$d_instruction);
			if($d_instruction=='')
			{
				$d_instruction='; ;';
			}
			$rds_code=$rec_order_header_download['rds_code'];
			$emp_code=$rec_order_header_download['emp_code'];
			if($transaction_type=='ST' || $transaction_type=='BT')
			{
				$sqlrdsname="SELECT RM.rds_name FROM rds_master RM,order_header OH WHERE OH.customer_code=RM.rds_code 
							AND OH.order_no='".$order_no."'";
				$rsrdsname=mysql_query($sqlrdsname) or die(mysql_error()." Error in select rds name: ".$sqlrdsname);
				$rowrdsname=mysql_fetch_array($rsrdsname);
				$customer_name=$rowrdsname['rds_name'];		
			}
			
			if(!in_array($rds_code,$rds_code_array))
			{
				array_push($rds_code_array,$rds_code);
				array_push($rds_name_array,$rds_name);
				array_push($emp_code_array,$emp_code);
			}
			
			${branch_name.$rds_code} =$branch_name;
			${rds_name.$rds_code} =$rds_name;
			${emp_name.$rds_code} =$emp_name;
			${transaction_date.$rds_code} =$transaction_date;
			${order_no.$rds_code} =$order_no;
			${sale_rate.$rds_code} =$sale_rate;
			${customer_name.$rds_code} =$customer_name;
			${prod_desc.$rds_code} =$prod_desc;
			${qty.$rds_code} =$qty;
			${Amount.$rds_code} =$Amount;
			${VATINPUT.$rds_code} =$VAT;
			${transaction_type.$rds_code} =$transaction_type;
			${d_instruction.$rds_code} =$d_instruction;
	
			${valueorderheader.$rds_code} =${branch_name.$rds_code}.",";
			${valueorderheader.$rds_code} .=${rds_name.$rds_code}.",";
			${valueorderheader.$rds_code} .=${emp_name.$rds_code}.",";
			${valueorderheader.$rds_code} .=${transaction_date.$rds_code}.",";
			${valueorderheader.$rds_code} .=${order_no.$rds_code}.",";
			${valueorderheader.$rds_code} .=${customer_name.$rds_code}.",";
			${valueorderheader.$rds_code} .=${prod_desc.$rds_code}.",";
			${valueorderheader.$rds_code} .=${qty.$rds_code}.",";
			${valueorderheader.$rds_code} .=${sale_rate.$rds_code}.",";
			${valueorderheader.$rds_code} .=${Amount.$rds_code}.",";
			${valueorderheader.$rds_code} .=${VATINPUT.$rds_code}.",";
			${valueorderheader.$rds_code} .=${transaction_type.$rds_code}.",";
			${valueorderheader.$rds_code} .=${d_instruction.$rds_code}.",";
			
			${lineorderheader.$rds_code}  .= ${valueorderheader.$rds_code}."\r\n"; 
			$sqlupdateorderheader="UPDATE order_header SET downld_transferred='YES' WHERE order_no='".$order_no."'";
			$rsupdateorderheader=mysql_query($sqlupdateorderheader)or die(mysql_error()." Error in order header update: ".$sqlupdateorderheader);
		}
		//print_r($rds_code_array);
		//$rds_code='C/0000291';
		//echo ${lineorderheader.$rds_code};
		//exit();
		
		for($k=0;$k<count($emp_code_array);$k++)
		{
			$sql_query_order_header_SA="SELECT BM.branch_name,RM.rds_name,EM.emp_name,DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') 
										AS transaction_date,LO.trans_id,
									OD.order_no,PM.prod_desc, OD.qty, OD.sale_rate,(OD.qty*OD.sale_rate) AS Amount,
									OD.amount AS input_amount,OH.VAT,
									OH.transaction_type, OH.d_instruction,RM.rds_code from order_details OD,product_master PM,order_header OH,
									rds_master RM,branch_master BM,employee_master EM,location LO where SUBSTRING(OH.order_no,2,5) = '".$emp_code_array[$k]."'
									AND SUBSTRING(OH.order_no,2,5)=EM.emp_code AND OH.transaction_type IN('SA','SH')
									AND OD.sku_code = PM.prod_code and OD.order_no = OH.order_no 
									AND EM.branch_code=BM.branch_code AND RM.emp_code=EM.emp_code 
									AND LO.trans_id=OH.order_no AND OH.downld_transferred='NO' 
									ORDER BY DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d'),trans_id";
			$rs_order_header_SA = mysql_query($sql_query_order_header_SA) or die(mysql_error()." Error in order header SA download: ".$sql_order_header_download);
						
			while($rec_order_header_SA = mysql_fetch_array($rs_order_header_SA)) 
			{ 
				$branch_name_SA=$rec_order_header_SA['branch_name'];
				$rds_name_SA=$rec_order_header_SA['rds_name'];
				$emp_name_SA=$rec_order_header_SA['emp_name'];
				$transaction_date_SA=$rec_order_header_SA['transaction_date'];
				$order_no_SA=$rec_order_header_SA['order_no'];
				$sale_rate_SA=$rec_order_header_SA['sale_rate'];
				$customer_name_SA='';
				$prod_desc_SA=$rec_order_header_SA['prod_desc'];
				$qty_SA=$rec_order_header_SA['qty'];
				$Amount_SA=$rec_order_header_SA['Amount'];
				$input_amount_SA=$rec_order_header_SA['input_amount'];
				if($input_amount_SA >0)
				{
					$Amount_SA=$input_amount_SA;
					$sale_rate_SA=0;
				}
				$VAT_SA=$rec_order_header_SA['VAT'];
				$transaction_type_SA=$rec_order_header_SA['transaction_type'];
				$d_instruction_SA=$rec_order_header_SA['d_instruction'];
				$d_instruction_SA=preg_replace('/\s+/', '',$d_instruction_SA);
				if($d_instruction_SA=='')
				{
					$d_instruction_SA='; ;';
				}
				$rds_code_SA=$rec_order_header_SA['rds_code'];
				
				${valueorderheader_SA.$rds_code_SA} =$branch_name_SA.",";
				${valueorderheader_SA.$rds_code_SA} .=$rds_name_SA.",";
				${valueorderheader_SA.$rds_code_SA} .=$emp_name_SA.",";
				${valueorderheader_SA.$rds_code_SA} .=$transaction_date_SA.",";
				${valueorderheader_SA.$rds_code_SA} .=$order_no_SA.",";
				${valueorderheader_SA.$rds_code_SA} .=$customer_name_SA.",";
				${valueorderheader_SA.$rds_code_SA} .=$prod_desc_SA.",";
				${valueorderheader_SA.$rds_code_SA} .=$qty_SA.",";
				${valueorderheader_SA.$rds_code_SA} .=$sale_rate_SA.",";
				${valueorderheader_SA.$rds_code_SA} .=$Amount_SA.",";
				${valueorderheader_SA.$rds_code_SA} .=$VAT_SA.",";
				${valueorderheader_SA.$rds_code_SA} .=$transaction_type_SA.",";
				${valueorderheader_SA.$rds_code_SA} .=$d_instruction_SA.",";
				
				${lineorderheader_SA.$rds_code_SA}  .= ${valueorderheader_SA.$rds_code_SA}."\r\n"; 
				$sqlupdateorderheader="UPDATE order_header SET downld_transferred='YES' WHERE order_no='".$order_no_SA."'";
				$rsupdateorderheader=mysql_query($sqlupdateorderheader)or die(mysql_error()." Error in order header SA update: ".$sqlupdateorderheader);
			}
			$sql_query_freight="SELECT BM.branch_name,RM.rds_name,EM.emp_name,DATE_FORMAT(FE.date,'%d-%m-%Y') AS transaction_date,FE.date,
								FE.freight_exp_trans_id,FE.amount,FE.trans_type,
								FE.remarks,RM.rds_code from freight_expenses FE,rds_master RM,branch_master BM,employee_master EM 
								where SUBSTRING(FE.freight_exp_trans_id,3,5) = '".$emp_code_array[$k]."' AND 
								SUBSTRING(FE.freight_exp_trans_id,3,5)=EM.emp_code 
								AND EM.branch_code=BM.branch_code AND RM.emp_code=EM.emp_code AND FE.downld_transferred='NO' ORDER BY DATE_FORMAT(SUBSTRING(FE.date,8,8),'%Y-%m-%d'),freight_exp_trans_id";
			$rs_query_freight = mysql_query($sql_query_freight) or die(mysql_error()." Error in freight download: ".$sql_query_freight);
						
			while($rec_query_freight = mysql_fetch_array($rs_query_freight)) 
			{ 
				$branch_name_freight=$rec_query_freight['branch_name'];
				$rds_name_freight=$rec_query_freight['rds_name'];
				$emp_name_freight=$rec_query_freight['emp_name'];
				$transaction_date_freight=$rec_query_freight['transaction_date'];
				$transaction_date_freight=str_replace('/','_',$transaction_date_freight);
				$order_no_freight=$rec_query_freight['freight_exp_trans_id'];
				$sale_rate_freight='';
				$customer_name_freight='';
				$prod_desc_freight='';
				$qty_freight='';
				$Amount_freight=$rec_query_freight['amount'];
				$VAT_freight='';
				$transaction_type_freight=$rec_query_freight['trans_type'];
				$d_instruction_freight=$rec_query_freight['remarks'];
				$d_instruction_freight=preg_replace('/\s+/', '',$d_instruction_freight);
				if($d_instruction_freight=='')
				{
					$d_instruction_freight='; ;';
				}
				$rds_code_freight=$rec_query_freight['rds_code'];
				
				${valueorderheader_freight.$rds_code_freight} =$branch_name_freight.",";
				${valueorderheader_freight.$rds_code_freight} .=$rds_name_freight.",";
				${valueorderheader_freight.$rds_code_freight} .=$emp_name_freight.",";
				${valueorderheader_freight.$rds_code_freight} .=$transaction_date_freight.",";
				${valueorderheader_freight.$rds_code_freight} .=$order_no_freight.",";
				${valueorderheader_freight.$rds_code_freight} .=$customer_name_freight.",";
				${valueorderheader_freight.$rds_code_freight} .=$prod_desc_freight.",";
				${valueorderheader_freight.$rds_code_freight} .=$qty_freight.",";
				${valueorderheader_freight.$rds_code_freight} .=$sale_rate_freight.",";
				${valueorderheader_freight.$rds_code_freight} .=$Amount_freight.",";
				${valueorderheader_freight.$rds_code_freight} .=$VAT_freight.",";
				${valueorderheader_freight.$rds_code_freight} .=$transaction_type_freight.",";
				${valueorderheader_freight.$rds_code_freight} .=$d_instruction_freight.",";
				
				${lineorderheader_freight.$rds_code_freight}  .= ${valueorderheader_freight.$rds_code_freight}."\r\n"; 
				$sqlupdatefreight="UPDATE freight_expenses SET downld_transferred='YES' WHERE freight_exp_trans_id='".$order_no_freight."'";
				$rsupdatefreight=mysql_query($sqlupdatefreight)or die(mysql_error()." Error in  freight update: ".$sqlupdatefreight);
			}
		}
		$zip = new ZipFile();
		for($i=0;$i<count($rds_code_array);$i++)
		{
			//$dataorderheader = str_replace("\r\n","",$lineorderheader);
			${dataorderheader.$rds_code_array[$i]} = ${lineorderheader.$rds_code_array[$i]}.${lineorderheader_SA.$rds_code_array[$i]}.${lineorderheader_freight.$rds_code_array[$i]} ;
			//${dataorderheader.$rds_code_array[$i]} = ${lineorderheader.$rds_code_array[$i]};
			//${dataorderheader.$rds_code_array[$i]} = ${lineorderheader.$rds_code_array[$i]}.${lineorderheader_freight.$rds_code_array[$i]} ;
				
			if (${dataorderheader.$rds_code_array[$i]} == "")
			{ 
				${dataorderheader.$rds_code_array[$i]} = "\r\n(0) Records Found!\n";                         
			} 
			
				$date=gmdate('d',strtotime('+330 minute'));
				$month=gmdate('m',strtotime('+330 minute'));
				$year=gmdate('Y',strtotime('+330 minute'));
				$hour=gmdate('H',strtotime('+330 minute'));
				$minute=gmdate('i',strtotime('+330 minute'));
				$second=gmdate('s',strtotime('+330 minute'));
	
			${file_content.$rds_code_array[$i]}= "$headerorder_header\r\n".${dataorderheader.$rds_code_array[$i]};
			${file_name.$rds_code_array[$i]}="$rds_name_array[$i]_$date$month$year$hour$minute$second.csv";
			
			//add files to the zip, passing file contents, not actual files
			//$zip->addFile($file_content1, $file_name1);
			$zip->addFile(${file_content.$rds_code_array[$i]}, ${file_name.$rds_code_array[$i]});
		}
		
		//$nick_name=$_SESSION['nick_name'];
		$nick_name=$_REQUEST['nick_name'];
		$fullPath = "/home/acedns/public_html/acednsproduct/csvdownload/$nick_name/csv_files_$branch_name.zip";
		
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
		$destination = "../csvdownload/$folderName/csv_files_$branch_name.zip";
		@unlink($destination);
		$file = fopen($destination, "w+");
		fputs($file, $contents);
		fclose($file);
		//echo $zip->file();
		//echo $zip->file();
		$successval=1;
		if($successval!=1){
			mysql_query("ROLLBACK");
		}
		else
		{
			echo 'SUCCESS';
		}
	}
	else
	{
		echo 'No branch or depot exists of this name';
	}
}
else
{
	echo 'No branch or depot exists of this name';
}
mysql_close($link);
?>	