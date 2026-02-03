<?php
mysqli_connect("localhost","acedns_dnsprod","dnsprod1234");
mysqli_select_db("acedns_EMAMI");
$current_date = date('Y-m-d',strtotime('yesterday'));
$val = $_REQUEST['val'];
if($val == 'today')
	$current_date = date('Y-m-d');
else
	$current_date = date('Y-m-d',strtotime('yesterday'));

/*-------------------------------------> Product Group Select <-------------------------------------*/
$sql_product_group_today = "SELECT DISTINCT PGM.product_group_code, PGM.product_group_name 
							FROM product_group_master PGM, sauda_transaction_log STL, product_master PM 
							WHERE PM.prod_code = STL.prod_code 
							AND PM.product_group_code = PGM.product_group_code 
							AND SUBSTRING(STL.download_time,1,10) = '".$current_date."' 
							ORDER BY PGM.product_group_name ASC";
$res_product_group_today = mysqli_query($link,$sql_product_group_today);
while($row_product_group_today = mysqli_fetch_assoc($res_product_group_today)){
	$product_group_code = $row_product_group_today['product_group_code'];
	$product_group_name = strtoupper($row_product_group_today['product_group_name']);
	/*-------------------------------> Total Quantity Select According To Prduct Group<----------------------------*/
	$sql_product_booked_quantity = "SELECT SUM(STL.convert_qty_two) as total_booked_ton 
									FROM sauda_transaction_log STL, product_master PM 
									WHERE STL.prod_code=PM.prod_code AND PM.product_group_code = '".$product_group_code."' 
									AND SUBSTRING(STL.download_time,1,10) = '".$current_date."'";
	$res_product_booked_quantity = mysqli_query($link,$sql_product_booked_quantity);
	$row_product_booked_quantity = mysqli_fetch_assoc($res_product_booked_quantity);
	$product_grwise_booked = number_format($row_product_booked_quantity['total_booked_ton'],3);
	
	$product_group_booked_array[$product_group_name] = $product_grwise_booked;
}
/*echo "<pre>";
print_r($product_group_booked_array);
echo "</pre>";*/

foreach($product_group_booked_array as $product_group_name=>$product_group_quantity){
	$data_pie .= "['".$product_group_name."',".$product_group_quantity."],";
}
$main = "['EmployeeName', 'Order'],";
echo $data_pie = rtrim($data_pie,",");

/*if($val == 'today')
echo $string = '{
  "cols": [
        {"id":"","label":"Topping","pattern":"","type":"string"},
        {"id":"","label":"Slices","pattern":"","type":"number"}
      ],
  "rows": [
        {"c":[{"v":"Mushrooms","f":null},{"v":3,"f":null}]},
        {"c":[{"v":"Onions","f":null},{"v":1,"f":null}]},
        {"c":[{"v":"Olives","f":null},{"v":1,"f":null}]},
        {"c":[{"v":"Zucchini","f":null},{"v":1,"f":null}]},
        {"c":[{"v":"Pepperoni","f":null},{"v":2,"f":null}]}
      ]
}';
else
echo $string = '{
  "cols": [
        {"id":"","label":"Topping","pattern":"","type":"string"},
        {"id":"","label":"Slices","pattern":"","type":"number"}
      ],
  "rows": [
        {"c":[{"v":"Banana","f":null},{"v":3,"f":null}]},
        {"c":[{"v":"Apple","f":null},{"v":1,"f":null}]},
        {"c":[{"v":"Orange","f":null},{"v":1,"f":null}]},
        {"c":[{"v":"Grapes","f":null},{"v":1,"f":null}]},
        {"c":[{"v":"Cononut","f":null},{"v":2,"f":null}]}
      ]
}';*/

?>