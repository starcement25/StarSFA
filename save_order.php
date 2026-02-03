<?php
//include "oem_connection.php";
$transaction_details = "transaction_details";
$trans_log = "trans_log";

//$inputJSON = file_get_contents('php://input');
//$input= json_decode($inputJSON,true);
//print_r($input);
$id_array=array();
//echo $input['prod_data'][0]['cat_name'];
$count=0;

/*for($i=0;$i<count($input);$i++)
{
	echo $input['prod_data'][$i]['cat_name'];
	echo $input['prod_data'][$i]['product_name'];
	print_r($input['prod_data'][$i]['sizeThAray']);
	/*foreach($input['prod_data'][$i]['sizeThAray'] as $key=>$sizeThArayval)
	{
		echo $sizeThArayval[0]['ThicknessData'];
	}*/
	//$count++; 
	//print_r($inputvalue);
//}
echo $trans_id = $_REQUEST["trans_id"] ? strtoupper($_REQUEST["trans_id"]) : "";
$dataarray=$_REQUEST["dataarray"];
//print_r($dataarray);
foreach($dataarray as $inputval)
{
echo $inputval['total_order_value'];
//echo $inputval["prod_data"][0]['cat_name'];
}
//echo $data_val=json_encode($_REQUEST);
$sqlinsertlog="INSERT INTO $trans_log SET trans_id='".$trans_id."',trans_val='".$data_val."'";
//$rsinsertlog = mysqli_query($conn,$sqlinsertlog);
$transaction_size_details  = "transaction_size_details ";

$customer_id = $_REQUEST["customer_id"] ? $_REQUEST["customer_id"] : "";
$cpilType = $_REQUEST["cpilType"] ? $_REQUEST["cpilType"] : "";
if($trans_id!='')
{
	
	$prod_data=$_REQUEST["prod_data"] ? $_REQUEST["prod_data"] : array();
	//print_r($prod_data);
	$size_data = $_REQUEST["sizeThAray"] ? $_REQUEST["sizeThAray"] : array();
/*$sqlinsertlog="INSERT INTO $trans_log SET trans_id='".$trans_id."',trans_val='".print_r($_REQUEST)."'";
$rsinsertlog = mysqli_query($conn,$sqlinsertlog);*/

$upload_dir="attachment/";
	$file_name = $_FILES['img_file']['name'];
	$tmp_name=$_FILES['img_file']['tmp_name'];
	$file_size=$_FILES['img_file']['size'];
	$file_type 	= 'general';

	if($file_name != "")// && $file_size < 2097152
	{
		$upload_file = $upload_dir.$file_name;
		move_uploaded_file($tmp_name,$upload_file);
	}
	$sqlinsert="INSERT INTO $transaction_details SET trans_id='".addslashes($trans_id)."',trans_date=CURRENT_TIMESTAMP(),
				customer_id='".addslashes($customer_id)."',
				CPIL_stockist ='".addslashes($cpilType)."',
				attacment='".addslashes($file_name)."'";
	//$rsinsert = mysqli_query($conn,$sqlinsert);
	
		//print_r($prod_data);
		foreach($size_data as $k=>$prod_data_val){
			$cat_name = $prod_data_val["cat_name"] ? trim($prod_data_val["cat_name"]) : "";
			$product_name = $prod_data_val["product_name"] ? trim($prod_data_val["product_name"]) : "";
			$design_name = $prod_data_val["design_name"] ? trim($prod_data_val["design_name"]) : "";
			$type_name = $prod_data_val["type_name"] ? trim($prod_data_val["type_name"]) : "";
			$sizearr=count($prod_data_val['sizeThAray']);
			echo $sqlinsertdetails="INSERT INTO $transaction_size_details SET trans_id='".addslashes($trans_id)."',
				product='".addslashes($product_name)."',
				category='".addslashes($cat_name)."',
				 type='".addslashes($type_name)."',
				design='".addslashes($design_name)."'";
			
		}
		$successval=1;
		$res_data = array("process_status"=>"YES","process_message"=>'Order saved successfully');
	}else{
			$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong");
	}
	echo json_encode($res_data);
mysqli_close($conn);
?>