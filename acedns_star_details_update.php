<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
header('Content-type: text/html; charset=utf-8');
include "star_connection.php";
mysqli_set_charset("UTF8");
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$ledger = "ledger";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$product_master = "product_master";
$res_data = array();
/*$sql1 = "update `$t_apperpdo` inner join `$customer_master` on `$t_apperpdo`.`customer_code`=`$customer_master`.`customer_code` set `$t_apperpdo`.`dns_customer_code`=`$customer_master`.`dns_customer_code` where `$t_apperpdo`.`customer_code`!='' and `$t_apperpdo`.`customer_code` is not null and `$customer_master`.`customer_code`!='' and `$customer_master`.`customer_code` is not null";
$res1 = mysqli_query($link,$sql1);*/

$sql2 = "update `$t_apperpdo` inner join `$customer_master` on  `$t_apperpdo`.`dns_customer_code`=`$customer_master`.`dns_customer_code` set `$t_apperpdo`.`customer_code`=`$customer_master`.`customer_code` where `$t_apperpdo`.`dns_customer_code`!='' and `$t_apperpdo`.`dns_customer_code` is not null and `$customer_master`.`dns_customer_code`!='' and `$customer_master`.`dns_customer_code` is not null";
$res2 = mysqli_query($link,$sql2);

/*$sql3 = "update `$t_dochallan` inner join `$customer_master` on `$t_dochallan`.`customer_code`=`$customer_master`.`customer_code` set `$t_dochallan`.`dns_customer_code`=`$customer_master`.`dns_customer_code` where `$t_dochallan`.`customer_code`!='' and `$t_dochallan`.`customer_code` is not null and `$customer_master`.`customer_code`!='' and `$customer_master`.`customer_code` is not null";
$res3 = mysqli_query($link,$sql3);*/

$sql4 = "update `$t_dochallan` inner join `$customer_master` on `$t_dochallan`.`dns_customer_code`=`$customer_master`.`dns_customer_code` set `$t_dochallan`.`customer_code`=`$customer_master`.`customer_code` where `$t_dochallan`.`dns_customer_code`!='' and `$t_dochallan`.`dns_customer_code` is not null and `$customer_master`.`dns_customer_code`!='' and `$customer_master`.`dns_customer_code` is not null";
$res4 = mysqli_query($link,$sql4);

/*$sql5 = "update `$ledger` inner join `$customer_master` on `$ledger`.`customer_code`=`$customer_master`.`customer_code` set `$ledger`.`dns_customer_code`=`$customer_master`.`dns_customer_code` where `$ledger`.`customer_code`!='' and `$ledger`.`customer_code` is not null and `$customer_master`.`customer_code`!='' and `$customer_master`.`customer_code` is not null";
$res5 = mysqli_query($link,$sql5);*/

$sql6 = "update `$ledger` inner join `$customer_master` on `$ledger`.`dns_customer_code`=`$customer_master`.`dns_customer_code` set `$ledger`.`customer_code`=`$customer_master`.`customer_code` where `$ledger`.`dns_customer_code`!='' and `$ledger`.`dns_customer_code` is not null and `$customer_master`.`dns_customer_code`!='' and `$customer_master`.`dns_customer_code` is not null";
$res6 = mysqli_query($link,$sql6);

/*$sql7 = "update `$ledger_balance` inner join `$customer_master` on `$ledger_balance`.`customer_code`=`$customer_master`.`customer_code` set `$ledger_balance`.`dns_customer_code`=`$customer_master`.`dns_customer_code` where `$ledger_balance`.`customer_code`!='' and `$ledger_balance`.`customer_code` is not null and `$customer_master`.`customer_code`!='' and `$customer_master`.`customer_code` is not null";
$res7 = mysqli_query($link,$sql7);*/

$sql8 = "update `$ledger_balance` inner join `$customer_master` on `$ledger_balance`.`dns_customer_code`=`$customer_master`.`dns_customer_code` set `$ledger_balance`.`customer_code`=`$customer_master`.`customer_code` where `$ledger_balance`.`dns_customer_code`!='' and `$ledger_balance`.`dns_customer_code` is not null and `$customer_master`.`dns_customer_code`!='' and `$customer_master`.`dns_customer_code` is not null";
$res8 = mysqli_query($link,$sql8);

/*----UPDATE PRODUCT DETAILS IN DOCHALLAN TABLE-----*/
$sql9 = "update `$t_dochallan` inner join `$product_master` on `$t_dochallan`.`prod_code`=`$product_master`.`prod_code` set `$t_dochallan`.`dns_prod_code`=`$product_master`.`dns_prod_code`,`$t_dochallan`.`prod_display_name`=`$product_master`.`prod_desc` where `$t_dochallan`.`prod_code`!='' and `$t_dochallan`.`prod_code` is not null and `$product_master`.`prod_code`!='' and `$product_master`.`prod_code` is not null";
$res9 = mysqli_query($link,$sql9);

$sql10 = "update `$t_dochallan` inner join `$product_master` on `$t_dochallan`.`dns_prod_code`=`$product_master`.`dns_prod_code` set `$t_dochallan`.`prod_code`=`$product_master`.`prod_code`,`$t_dochallan`.`prod_display_name`=`$product_master`.`prod_desc` where `$t_dochallan`.`dns_prod_code`!='' and `$t_dochallan`.`dns_prod_code` is not null and `$product_master`.`dns_prod_code`!='' and `$product_master`.`dns_prod_code` is not null";
$res10 = mysqli_query($link,$sql10);


/*-----FOR Dispatched-----*/

$sql11 = "update `$t_apperpdo` inner join `$t_dochallan` on `$t_apperpdo`.`ERPORDERNO`=`$t_dochallan`.`ERPORDERNO` set `$t_apperpdo`.`STATUS`='Dispatched' where `$t_apperpdo`.`ERPORDERNO`!='' and `$t_apperpdo`.`ERPORDERNO` is not null and `$t_dochallan`.`ERPORDERNO`!='' and `$t_dochallan`.`ERPORDERNO` is not null and (`$t_apperpdo`.`STATUS`='Order received' or `$t_apperpdo`.`STATUS`='DO approved') and (`$t_dochallan`.`CHALLANNO`!='' or `$t_dochallan`.`CHALLANNO` is not null)";
$res11 = mysqli_query($link,$sql11);

$sql11 = "update `$t_apperpdo` inner join `$t_dochallan` on `$t_apperpdo`.`APPORDERNO`=`$t_dochallan`.`APPORDERNO` set `$t_apperpdo`.`STATUS`='Dispatched' where `$t_apperpdo`.`ERPORDERNO`!='' and `$t_apperpdo`.`ERPORDERNO` is not null and `$t_dochallan`.`ERPORDERNO`!='' and `$t_dochallan`.`ERPORDERNO` is not null and (`$t_apperpdo`.`STATUS`='Order received' or `$t_apperpdo`.`STATUS`='DO approved') and (`$t_dochallan`.`CHALLANNO`!='' or `$t_dochallan`.`CHALLANNO` is not null)";
$res11 = mysqli_query($link,$sql11);

/*-----FOR DO APPROVED-----*/
$sql12 = "update `T_APPERPDO` left join `T_DOCHALLAN` on `T_APPERPDO`.`ERPORDERNO`=`T_DOCHALLAN`.`ERPORDERNO` set `T_APPERPDO`.`STATUS`='DO approved' where `T_APPERPDO`.`ERPORDERNO`!='' and `T_APPERPDO`.`ERPORDERNO` is not null and (`T_DOCHALLAN`.`ERPORDERNO`='' or `T_DOCHALLAN`.`ERPORDERNO` is null)";
$res12 = mysqli_query($link,$sql12);

$res_data = array("process_status"=>"YES","process_message"=>"DONE");
echo json_encode($res_data);
mysqli_close();
?>