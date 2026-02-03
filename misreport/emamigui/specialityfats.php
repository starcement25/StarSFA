<?php
session_start();
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db("acedns_EMAMI");
require("../adminUtils.php");

$current_date = date('Y-m-d');
$current_year = date('Y');
$yesterday = date('Y-m-d', strtotime("-1 day"));
if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login']=="supervisor" || $_SESSION['admin_login']=="system"){
	$emp_hierarchy='';
	$emp_hierarchy_condition='';
	$emp_hierarchy_condition_one='';
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition=' AND STL.emp_code IN('.$emp_hierarchy.')';
	$emp_hierarchy_condition_one=' AND substring(SD.sauda_no,3,5) IN ('.$emp_hierarchy.')';
}

$current_month = date('m');
if($current_month == '01' || $current_month == '02' || $current_month == '03'){
	$previous_year = date('Y', strtotime('-1 year'));
	$previous_year_date = $previous_year."-04-01";
}
else{
	$previous_year_date = date('Y-04-01');
}

function get_data_set($vertical,$date_condition,$emp_hierarchy_condition,$date_type){
	if($vertical == 'HRB'){
		$vertical = 'HBC,Rasoi,BIB';
	}
	
	$sql_sauda_transaction_log = "SELECT SUM(STL.qty), SUM(STL.amount), STL.product_group_code,PGM.vertical_value, PGM.product_group_name FROM `sauda_transaction_log` STL, product_group_master PGM WHERE STL.product_group_code = PGM.product_group_code AND ".$date_condition." AND PGM.vertical_value = '".$vertical."' ".$emp_hierarchy_condition." GROUP BY STL.product_group_code";
	$res_sauda_transaction_log = mysql_query($sql_sauda_transaction_log);
	$total_row_check = mysql_num_rows($res_sauda_transaction_log);
	if($total_row_check == 0 && $date_type == 'prev_day'){
		$date_cond_explode = explode("%",$date_condition);
		$yesterday = date('Y-m-d',strtotime($date_cond_explode[1] . " -1 day"));
		$date_condition = " STL.sauda_date LIKE '%".$yesterday."%' ";
		return get_data_set($vertical,$date_condition,$emp_hierarchy_condition,'prev_day');
		
	}
	else{
		$res_sauda_transaction_log = mysql_query($sql_sauda_transaction_log);
		while($row_sauda_transaction_log = mysql_fetch_array($res_sauda_transaction_log)){
			$product_group_code = $row_sauda_transaction_log['product_group_code'];
			$quantity = $row_sauda_transaction_log['SUM(STL.qty)'];
			
			$sql_prod_group_name = "SELECT product_group_name FROM product_group_master WHERE product_group_code = '".$product_group_code."'";
			$res_prod_group_name = mysql_query($sql_prod_group_name);
			$row_prod_group_name = mysql_fetch_array($res_prod_group_name);
			$prod_group_name = $row_prod_group_name['product_group_name'];
			
			/*if($vertical == "Specialty Fats"){
				if($prod_group_name == "All Purpose")
					$prod_group_name = "All P";
				else if($prod_group_name == "Puffs & Kharis")
					$prod_group_name = "Puffs";
				else if($prod_group_name == "Biscuits & Cookies")
					$prod_group_name = "Bisc";
				else if($prod_group_name == "Three In One")
					$prod_group_name = "Thre";
				else if($prod_group_name == "Cakes & Creams (Aer)")
					$prod_group_name = "Cakes";
			}*/
			
			//$quantity = rand(10000,15000);
			
			//$total_qty += $quantity;
			
			$data_set .= "{
					name: '".$prod_group_name."',
					y: $quantity
				},";
			
		}
		$data_set = rtrim($data_set,",");
		if($date_type == 'prev_day'){
			$date_cond_explode = explode("%",$date_condition);
			return $data_set."^".$date_cond_explode[1];
		}
		else{
			return $data_set;
		}
	}
}

$date_condition = " STL.sauda_date LIKE '%".$yesterday."%' ";
$data_set_vertical_one = get_data_set('HRB',$date_condition,$emp_hierarchy_condition,'prev_day');
$data_set_vertical_one = explode("^",$data_set_vertical_one);
echo $data_set_vertical_two = get_data_set('Specialty Fats',$date_condition,$emp_hierarchy_condition,'prev_day');
$data_set_vertical_two = explode("^",$data_set_vertical_two);

$date_condition = " SUBSTRING(STL.sauda_date,1,10) BETWEEN '".$previous_year_date."' AND '".$yesterday."' ";
$data_set_vertical_three = get_data_set('HRB',$date_condition,$emp_hierarchy_condition,'ytd');
$data_set_vertical_four = get_data_set('Specialty Fats',$date_condition,$emp_hierarchy_condition,'ytd');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>EMAMI GUI</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
<link href='http://fonts.googleapis.com/css?family=Rosario:400,700' rel='stylesheet' type='text/css'>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="code/highcharts.js"></script>
<script src="code/highcharts-3d.js"></script>
<!--script src="https://code.highcharts.com/modules/exporting.js"></script-->
</head>
<body>
<div id="contentwrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-sm-6 text-left" align="center" style="text-align:center;">
                <div id="container1"></div>
            </div>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">



Highcharts.chart('container1', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie',
		options3d: {
            enabled: true,
            alpha: 45,
            beta: 0
        }
    },
    title: {
        text: 'Specialty Fats - Sauda Booked - <?php echo date('d-m-Y',strtotime($data_set_vertical_two[1])); ?>'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
			depth: 35,
			size: "50%",
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
        }
    },
    series: [{
        name: 'Brands',
        colorByPoint: true,
        data: [<?php echo $data_set_vertical_two[0]; ?>]
    }]
});

Highcharts.chart('container3', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie',
		options3d: {
            enabled: true,
            alpha: 45,
            beta: 0
        }
    },
    title: {
        text: 'HBC,Rasoi,BIB - Sauda Booked - <?php echo $previous_year." - ".$current_year; ?>'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
			depth: 35,
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
        }
    },
    series: [{
        name: 'Brands',
        colorByPoint: true,
        data: [<?php echo $data_set_vertical_three; ?>]
    }]
});

Highcharts.chart('container4', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie',
		options3d: {
            enabled: true,
            alpha: 45,
            beta: 0
        }
    },
    title: {
        text: 'Specialty Fats - Sauda Booked - <?php echo $previous_year." - ".$current_year; ?>'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
			depth: 35,
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
        }
    },
    series: [{
        name: 'Brands',
        colorByPoint: true,
        data: [<?php echo $data_set_vertical_four; ?>]
    }]
});
		</script>