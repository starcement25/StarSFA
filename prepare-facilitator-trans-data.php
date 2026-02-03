<?php
ini_set('MAX_EXECUTION_TIME', -1);
set_time_limit (0);
ini_set('memory_limit', '-1');
define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	//define("DB","acedns_VCONNECT");
	define("DB","acedns_DURO");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	excelDownload();
	function excelDownload()
	{
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
		$nick_name='DURO';
		$from_date='2020-04-01';
		$to_date='2021-03-31';
		if($from_date!='' && $to_date!='')
		{
		  $date_condition=" AND DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') >='".$from_date."' AND 
							DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') <='".$to_date."'";
		}
			  $sql_get_menu = "SELECT layout_name,menu_id,survey_sub_menu FROM survey_input WHERE type='menu' AND menu_id IN('RA034','RA035') ORDER BY display_order ASC";
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
				 if(strtoupper($nick_name)=='DURO' && $menu_id=='RA002')
				 {
				 ${excelsubheader_one.$menu_id}=''."\t".''."\t".''."\t".''."\t".''."\t".''."\t";
				 }
				 ${sl_no.$menu_id}=1;
				 $row_id_string='';
				 $row_id_string_SET='';
				 $valueexcel='';
				 ${survey_id_array.$menu_id}=array();
				 					
				   $sql_get_display="SELECT display_name,row_id,action,display_order FROM survey_input WHERE  type!='menu' AND menu_id='".$menu_id."'  AND acedns='Y'
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
						if(strtoupper($nick_name)=='DURO' && $menu_id=='RA002')
						 {
						    $display_order=$row_get_display['display_order'];
							if($display_id =='RA059'){
							  ${display_order.$display_id}=$row_get_display['display_order'];	
							  ${excelsubheader.$menu_id}.=$display_name."\t".''."\t".''."\t".''."\t".''."\t";
							  ${excelsubheader_one.$menu_id}.="\t".'Category'."\t".'Qty'."\t".'Value'."\t".'Desc'."\t".'Spices'."\t";
							}
							 else if($display_id =='RA085')
							 {
								 ${excelsubheader.$menu_id}.=$display_name."\t".'Name'."\t";
								 ${excelsubheader_one.$menu_id}.=''."\t";
							  	 ${excelblankcontent.$menu_id}.=''."\t";
							 }
							else
							{
							  ${excelsubheader.$menu_id}.=$display_name."\t";
						  	  ${excelsubheader_one.$menu_id}.=''."\t";
							  ${excelblankcontent.$menu_id}.=''."\t";
							}
							if(($display_order >  ${display_order.$display_id}) && ${display_order.$display_id}!='')
							{
								${excelblankcontent_next.$menu_id}.=''."\t";
							}
						 }
						 else if(strtoupper($nick_name)=='DURO' && $menu_id=='RA035')
						 {
							 if($display_id =='RA047'){
							 ${excelsubheader.$menu_id}.='Facilitator name'."\t".'Type'."\t";
							 }
							 else
							 {
								 ${excelsubheader.$menu_id}.=$display_name."\t";
							 }
						 }
						 else
						 {
							 ${excelsubheader.$menu_id}.=$display_name."\t";
						 }
						$row_id_string.="'".$display_id."'".',';
						$row_id_string_SET.=$display_id.',';
						$display_no++;
					}
				  $row_id_string=substr($row_id_string,0,-1);
				  $row_id_string_SET=substr($row_id_string_SET,0,-1);
				  
				   if(strtoupper($nick_name)=='DURO')
					{
				   $sql_survey_existing_row_id="SELECT *,DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%d-%m-%Y') AS survey_date FROM survey_output SO, employee_master EM WHERE SO.row_id IN(".$row_id_string.") ".$date_condition." AND SUBSTRING(SO.survey_id,3,5)=EM.emp_code 
									  ORDER BY SO.survey_id DESC,FIND_IN_SET(SO.row_id,'".$row_id_string_SET."') ";
				  $rs_survey_existing_row_id=mysqli_query($link,$sql_survey_existing_row_id);
				   while($row_survey_existing_row_id=mysqli_fetch_assoc($rs_survey_existing_row_id))
				   {
					 $survey_id_existing= $row_survey_existing_row_id['survey_id'];
					  ${row_id_string.$survey_id_existing}.="'".$row_survey_existing_row_id['row_id']."'".',';
				   }
					}
				  //echo ${row_id_string.'SUE016320201014100700'};
				  $sql_survey_output="SELECT *,DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%d-%m-%Y') AS survey_date FROM survey_output SO, employee_master EM WHERE SO.row_id IN(".$row_id_string.") ".$date_condition." AND SUBSTRING(SO.survey_id,3,5)=EM.emp_code 
									  ORDER BY SO.survey_id DESC,FIND_IN_SET(SO.row_id,'".$row_id_string_SET."') ";
				  $rs_survey_output=mysqli_query($link,$sql_survey_output);
				  $output_no=1;
				  while($row_survey_output=mysqli_fetch_assoc($rs_survey_output))
				  {
					 $survey_id= $row_survey_output['survey_id'];
					
					 $survey_date= date("d-M-Y",strtotime($row_survey_output['survey_date']));
					 $value=$row_survey_output['value'];
					 if(strpos($value,"#") == true && strtoupper($nick_name)!='DURO'){
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
					$sqlmasterview="SELECT type,display_table_name,insert_table_detail FROM survey_input WHERE  row_id='".$row_survey_output['row_id']."'";
					$resmasterview = mysqli_query($link,$sqlmasterview); 
					$rowmasterview=mysqli_fetch_assoc($resmasterview);
					$type=$rowmasterview['type'];
					$insert_table_detail=$rowmasterview['insert_table_detail'];
					$insert_table_detail_parts=explode("#",$insert_table_detail);
					if($type=='masterview')
					{
						$display_table_name=$rowmasterview['display_table_name'];
						$dispaly_table_name_partsone=explode('#',$display_table_name);
						$dispaly_table_name_partstwo=explode('%',$dispaly_table_name_partsone[1]);
						if(strpos($dispaly_table_name_partstwo[1],'&')!=false){
							$dispaly_table_name_partstwo_sub=explode('&',$dispaly_table_name_partstwo[1]);
							$dispaly_table_name_partstwo[1]=$dispaly_table_name_partstwo_sub[0];
						}
						if($dispaly_table_name_partsone[0]=='emp_master') $dispaly_table_name_partsone[0]='employee_master';
						$value=str_replace(";",",",$value);
						$sqlfetchval="SELECT GROUP_CONCAT($dispaly_table_name_partstwo[1] SEPARATOR ';') AS fetch_value 
								FROM $dispaly_table_name_partsone[0] WHERE FIND_IN_SET($dispaly_table_name_partstwo[0],'".$value."')";
						$rsfetchval=mysqli_query($link,$sqlfetchval);
						$rowfetchval=mysqli_fetch_assoc($rsfetchval);
						$value=$rowfetchval['fetch_value'];
						/*if($survey_id=='SUE009120201127214438' &&  $row_survey_output['row_id']=='RA024')
						{
							echo $sqlfetchval;
							exit();
						}*/
					}
					if($insert_table_detail_parts[0]=='insert')
					{
						$valueparts=explode(";",$value);
						$value=$valueparts[0];
					}
					/*if($row_survey_output['row_id']=='RA003')
					{
						$valueparts=explode(";",$value);
						$value=$valueparts[0];
					}*/

					if($value =='') $value=$row_survey_output['value'];
					if(strtoupper($nick_name)=='DURO')
					{
						 ${row_id_string.$survey_id}=str_replace("'","",substr(${row_id_string.$survey_id},0,-1));
					 	 ${row_id_array.$survey_id}=explode(",",${row_id_string.$survey_id});
						if($row_survey_output['row_id']=='RA016' || $row_survey_output['row_id']=='RA017' || $row_survey_output['row_id']=='RA096' || $row_survey_output['row_id']=='RA097'){
							$value=str_replace('YES:','',$value);
							$value=str_replace('NO','',$value);
						}
						else if($row_survey_output['row_id']=='RA006')
						{
							if(!in_array('RA108',${row_id_array.$survey_id}))
							{
								//echo 'a';
								$value=$value."\t"."";
							}
							else
							{
								$value=$value;
							}
							//echo 'b';
						}
						else if($row_survey_output['row_id']=='RA027')
						{
							if(!in_array('RA109',${row_id_array.$survey_id}))
							{
								//echo 'a';
								$value=$value."\t"."";
							}
							else
							{
								$value=$value;
							}
							//echo 'b';
						}
						else if($row_survey_output['row_id']=='RA030')
						{
							if(!in_array('RA107',${row_id_array.$survey_id}))
							{
								//echo 'a';
								$value=$value."\t"."";
							}
							else
							{
								$value=$value;
							}
							//echo 'b';
						}
						else if($row_survey_output['row_id']=='RA037')
						{
							if(!in_array('RA120',${row_id_array.$survey_id}))
							{
								//echo 'a';
								$value=$value."\t"."";
							}
							else
							{
								$value=$value;
							}
							//echo 'b';
						}
						
						else if($row_survey_output['row_id']=='RA047')
						{
							$valueparts=explode(";",$value);
							$sqlfacilitatortype="SELECT f_type FROM facilitator_master WHERE f_code='".$valueparts[1]."'";
							$rsfacilitatortype=mysqli_query($link,$sqlfacilitatortype);
							$rowfacilitatortype=mysqli_fetch_assoc($rsfacilitatortype);
							$f_type=$rowfacilitatortype['f_type'];
							//$value=$valueparts[0].' - '.$f_type;
							$value=$valueparts[0]."\t".$f_type;
						}
						else if($row_survey_output['row_id']=='RA053')
						{
							if(!in_array('RA110',${row_id_array.$survey_id}))
							{
								//echo 'a';
								$value=$value."\t"."";
							}
							else
							{
								$value=$value;
							}
							if(!in_array('RA111',${row_id_array.$survey_id}))
							{
								//echo 'a';
								$value=$value."\t"."";
							}
							else
							{
								$value=$value;
							}
							//echo 'b';
						}
						else if($row_survey_output['row_id']=='RA077')
						{
							$valueparts=explode(";",$value);
							$value=$valueparts[0];
						}
						else if($row_survey_output['row_id']=='RA085')
						{
							$valueparts=explode(":",$value);
							if(strtolower($valueparts[0])!='office' && count($valueparts) > 0)
							{
								$sqlfacilitatorname="SELECT facilitator_name FROM facilitator_master WHERE f_code='".$valueparts[1]."'";
								$rsfacilitatorname=mysqli_query($link,$sqlfacilitatorname);
								$rowfacilitatorname=mysqli_fetch_assoc($rsfacilitatorname);
								$facilitator_name=$rowfacilitatorname['facilitator_name'];
								$value=$valueparts[0]."\t".$facilitator_name;
							}
							else if(strtolower($valueparts[0])=='office' && count($valueparts) > 0)
							{
								$value=$valueparts[0]."\t".$valueparts[1];
							}
							else
							{
								$value=$value."\t"."";
							}
						}
						else if($row_survey_output['row_id']=='RA098' || $row_survey_output['row_id']=='RA073' || $row_survey_output['row_id']=='RA075')
						{
							if($value!='')
							{
							$value=date("d-M-Y",strtotime(substr(str_replace("/","-",$value),0,10)));
							}
							else
							{
								$value=$value;
							}
						}
						else if($row_survey_output['row_id']=='RA090')
						{
							$sqltableview="SELECT value FROM table_view WHERE row_id='".$row_survey_output['row_id']."'";
							$rstableview=mysqli_query($link,$sqltableview);
							$rowtableview=mysqli_fetch_assoc($rstableview);
							$table_view_val=explode("/",$rowtableview['value']);
							foreach($table_view_val as $table_view_value)
							{
								if (strpos($value,strtoupper($table_view_value)) !== false) {
									$value=strtoupper($table_view_value);	
								}
							}
						}
						else if($row_survey_output['row_id']=='RA059' && $value!='')
						{
							$product_cat_first_part=explode("$",$value);
							//echo $survey_id;
							//echo '<br />';
							$countfirstpart=1;
							foreach($product_cat_first_part as $product_cat_first_part_val)
							{
								$TD_count=1;
								if($countfirstpart > 1)
								{
									//echo 'aaaaaaaaaaaaaaaaa';
									${valueexcel.$survey_id.$menu_id.$row_survey_output['row_id']}.="\n".''."\t".''."\t".''."\t".''."\t".''."\t".''.${excelblankcontent.$menu_id};
								}
								$product_cat_second_part=explode("#",$product_cat_first_part_val);
								//print_r($product_cat_second_part);
								$product_cat_second_part_sub_val=explode(":",$product_cat_second_part[0]);
								${valueexcel.$survey_id.$menu_id.$row_survey_output['row_id']}.=$product_cat_second_part_sub_val[0]."\t";
								for($TD_count=0;$TD_count < 4;$TD_count++) //FOr qty , value , Desc and species
								{
								   if($TD_count==0){
									 $product_cat_second_part_sub_val=explode(":",$product_cat_second_part[$TD_count]);
									 ${valueexcel.$survey_id.$menu_id.$row_survey_output['row_id']}.=$product_cat_second_part_sub_val[1];
									}
									else{
									 ${valueexcel.$survey_id.$menu_id.$row_survey_output['row_id']}.=$product_cat_second_part[$TD_count];
									}
									if($TD_count <3)
									{
										${valueexcel.$survey_id.$menu_id.$row_survey_output['row_id']}.="\t";
									}
								}
								//echo $survey_id;
								//echo '<br />';
								if($countfirstpart >= 1 && $countfirstpart!=count($product_cat_first_part))
								{
									${valueexcel.$survey_id.$menu_id.$row_survey_output['row_id']}.="\t".${excelblankcontent_next.$menu_id};
								}
								$countfirstpart++;
							   //${valueexcel.$survey_id.$menu_id.$row_survey_output['row_id']}.=''."\t"."\n";
							}
						  	$value=${valueexcel.$survey_id.$menu_id.$row_survey_output['row_id']};
							//echo '<br />';
					  }
					  else if($row_survey_output['row_id']=='RA059' && $value=='')
						{
							$value=''."\t".''."\t".''."\t".''."\t".'';
						}
						//exit();
					}  //End of Product Category
					 ${valueexcel.$survey_id.$menu_id}.=$value."\t";
					 //${datavalue.$menu_id}.=${valueexcel.$survey_id};
					 $output_no++;
				  }
				$menu_no++;
			 }
			 //exit();
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
		
				if(strtoupper($nick_name)=='DURO' && $menu_id_array[$i]=='RA002')
				 {
					${file_content.$menu_id_array[$i]}= ${excelheader.$menu_id_array[$i]}."\n".${excelsubheader.$menu_id_array[$i]}."\n".${excelsubheader_one.$menu_id_array[$i]}."\n".${datavalue.$menu_id_array[$i]};
				 }
				 else
				 {
					${file_content.$menu_id_array[$i]}= ${excelheader.$menu_id_array[$i]}."\n".${excelsubheader.$menu_id_array[$i]}."\n".${datavalue.$menu_id_array[$i]};
				 }
				${file_name.$menu_id_array[$i]}="$menu_name_array[$i]_$survey_sub_menu_array[$i]_Apr2020_Mar2021_$date$month$year$hour$minute$second.xls";
				
				//add files to the zip, passing file contents, not actual files
				//$zip->addFile($file_content1, $file_name1);
				$zip->addFile(${file_content.$menu_id_array[$i]}, ${file_name.$menu_id_array[$i]});
			}
			$finalzipfile='facilitator-data.zip';
			$destination = "smpower-bkup/$finalzipfile";
			$filedestination = fopen($destination, "w");
			fputs($filedestination,$zip->file());
			fclose($filedestination);
	}
?>	