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
	
	/*$sql_sauda_transaction_log_total_qty = "SELECT SUM(STL.qty) FROM `sauda_transaction_log` STL, product_group_master PGM WHERE STL.product_group_code = PGM.product_group_code AND ".$date_condition." AND PGM.vertical_value = '".$vertical."' ".$emp_hierarchy_condition." GROUP BY STL.product_group_code";
	$res_sauda_transaction_log_total_qty = mysql_query($sql_sauda_transaction_log_total_qty);
	$row_sauda_transaction_log_total_qty = mysql_fetch_array($res_sauda_transaction_log_total_qty);
	$sauda_trans_log_tqty = $row_sauda_transaction_log_total_qty['SUM(STL.qty)'];*/
	
	$sql_sauda_transaction_log = "SELECT SUM(STL.qty), SUM(STL.amount), STL.product_group_code,PGM.vertical_value, PGM.product_group_name FROM `sauda_transaction_log` STL, product_group_master PGM WHERE STL.product_group_code = PGM.product_group_code AND ".$date_condition." AND PGM.vertical_value = '".$vertical."' ".$emp_hierarchy_condition." GROUP BY STL.product_group_code";
	$res_sauda_transaction_log = mysql_query($sql_sauda_transaction_log);
	$total_row_check = mysql_num_rows($res_sauda_transaction_log);
	if($total_row_check == 0 && $date_type == 'prev_day'){
		$date_cond_explode = explode("%",$date_condition);
		$yesterday = date('Y-m-d',strtotime($date_cond_explode[1] . " -1 day"));
		$date_condition = " STL.sauda_date LIKE '%".$yesterday."%' ";
		return get_data_set($vertical,$date_condition,$emp_hierarchy_condition,$date_type);
		
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
			
			if($date_type == 'prev_day'){
				$data_set .= "{
					name: '".$prod_group_name."',
					y: $quantity,
					drilldown: '$product_group_code'
				},";
				
				$data_set_drilldown .= "{
												id: '".$product_group_code."',
												data: [";
												
				$sql_product_drilldown = "SELECT PM.prod_desc, SUM(STL.qty) FROM sauda_transaction_log STL, product_master PM WHERE STL.prod_code = PM.prod_code AND STL.product_group_code = '".$product_group_code."' AND ".$date_condition.$emp_hierarchy_condition." GROUP BY PM.prod_code";
				$res_product_drilldown = mysql_query($sql_product_drilldown);
				while($row_product_drilldown = mysql_fetch_array($res_product_drilldown)){
					$prod_desc = $row_product_drilldown['prod_desc'];
					$prod_qty = $row_product_drilldown['SUM(STL.qty)'];
					
					$data_set_drilldown .= "['$prod_desc', $prod_qty],";
				}
				$data_set_drilldown = rtrim($data_set_drilldown,",");
				$data_set_drilldown .= "]
											},";
			}
			else if($date_type == 'ytd'){
				$data_set .= "{
								name: '".$prod_group_name."',
								y: $quantity,
								drilldown: '$product_group_code'
							},";
				
				$data_set_drilldown .= "{
												id: '".$product_group_code."',
												data: [";
				
				/*$sql_product_drilldown_total_qty = "SELECT SUM(STL.qty) FROM sauda_transaction_log STL, product_master PM WHERE STL.prod_code = PM.prod_code AND STL.product_group_code = '".$product_group_code."' AND ".$date_condition.$emp_hierarchy_condition;
				$res_product_drilldown_total_qty = mysql_query($sql_product_drilldown_total_qty);
				$row_product_drilldown_total_qty = mysql_fetch_array($res_product_drilldown_total_qty);
				$drilldown_total_qty = $row_product_drilldown_total_qty['SUM(STL.qty)'];*/
							
				$sql_product_drilldown = "SELECT PM.prod_desc, SUM(STL.qty) FROM sauda_transaction_log STL, product_master PM WHERE STL.prod_code = PM.prod_code AND STL.product_group_code = '".$product_group_code."' AND ".$date_condition.$emp_hierarchy_condition." GROUP BY PM.prod_code";
				$res_product_drilldown = mysql_query($sql_product_drilldown);
				while($row_product_drilldown = mysql_fetch_array($res_product_drilldown)){
					$prod_desc = $row_product_drilldown['prod_desc'];
					$prod_qty = $row_product_drilldown['SUM(STL.qty)'];
					
					$data_set_drilldown .= "['$prod_desc', $prod_qty],";
				}
				$data_set_drilldown = rtrim($data_set_drilldown,",");
				$data_set_drilldown .= "]
											},";
			}
			
		}
		$data_set = rtrim($data_set,",");
		
		if($date_type == 'prev_day'){
			$data_set_drilldown = rtrim($data_set_drilldown,",");
			$date_cond_explode = explode("%",$date_condition);
			return $data_set."^".$data_set_drilldown."^".$date_cond_explode[1];
		}
		else{
			$data_set_drilldown = rtrim($data_set_drilldown,",");
			return $data_set."^".$data_set_drilldown;
		}
	}
}

$date_condition = " STL.sauda_date LIKE '%".$yesterday."%' ";
$data_set_vertical_one = get_data_set('HRB',$date_condition,$emp_hierarchy_condition,'prev_day');
$data_set_vertical_one = explode("^",$data_set_vertical_one);
$data_set_vertical_two = get_data_set('Specialty Fats',$date_condition,$emp_hierarchy_condition,'prev_day');
$data_set_vertical_two = explode("^",$data_set_vertical_two);

$date_condition = " SUBSTRING(STL.sauda_date,1,10) BETWEEN '".$previous_year_date."' AND '".$yesterday."' ";
$data_set_vertical_three = get_data_set('HRB',$date_condition,$emp_hierarchy_condition,'ytd');
$data_set_vertical_three = explode("^",$data_set_vertical_three);
$data_set_vertical_four = get_data_set('Specialty Fats',$date_condition,$emp_hierarchy_condition,'ytd');
$data_set_vertical_four = explode("^",$data_set_vertical_four);
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
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<!--script src="https://code.highcharts.com/modules/exporting.js"></script-->
</head>
<body>
<div id="contentwrapper">
<div class="container-fluid">
<div class="row">
    <div class="col-md-6 col-sm-6 text-left" align="center" style="text-align:center;">
    	<div id="container1"></div>
    </div>
    <div class="col-md-6 col-sm-6 text-left" align="center" style="text-align:center;">
    	<div id="container3"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 col-sm-6 text-left" align="center" style="text-align:center;">
    	<div id="container4"></div>
    </div>
    <div class="col-md-6 col-sm-6 text-left" align="center" style="text-align:center;">
    	<div id="container2"></div>
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
        text: 'HBC,Rasoi,BIB - Sauda Booked - <?php echo date('d-m-Y',strtotime($data_set_vertical_one[2])); ?>'
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
        data: [<?php echo $data_set_vertical_one[0]; ?>]
    }],
	drilldown: {
        series: [<?php echo $data_set_vertical_one[1]; ?>]
    }
});

Highcharts.chart('container2', {
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
        text: 'Specialty Fats - Sauda Booked - <?php echo date('d-m-Y',strtotime($data_set_vertical_two[2])); ?>'
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
    }],
	drilldown: {
        series: [<?php echo $data_set_vertical_two[1]; ?>]
    }
});


Highcharts.chart('container3', {
    chart: {
        type: 'column',
		margin: 75,
        options3d: {
            enabled: true,
            alpha: 15,
            beta: 0,
            depth: 50
        }
    },
    title: {
        text: 'HBC,Rasoi,BIB - Sauda Booked: <?php echo $previous_year." - ".$current_year; ?>'
    },
    subtitle: {
        text: ''
    },
    xAxis: {
        type: 'category',
		labels: {
            rotation: -45,
            style: {
                fontSize: '10px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Quantity Booked'
        }
    },
    legend: {
        enabled: false
    },
    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0,
			depth: 25
        }
    },
    series: [{
        name: 'Brands',
        colorByPoint: true,
        data: [<?php echo $data_set_vertical_three[0]; ?>]
    }],
	drilldown: {
		series: [<?php echo $data_set_vertical_three[1]; ?>]
    }
});

Highcharts.chart('container4', {
    chart: {
        type: 'column',
		margin: 75,
        options3d: {
            enabled: true,
            alpha: 15,
            beta: 0,
            depth: 50
        }
    },
    title: {
        text: 'Specialty Fats - Sauda Booked: <?php echo $previous_year." - ".$current_year; ?>'
    },
    subtitle: {
        text: ''
    },
    xAxis: {
        type: 'category',
		labels: {
            rotation: -20,
            style: {
                fontSize: '10px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Quantity Booked'
        }
    },
    legend: {
        enabled: false
    },
    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0,
			depth: 25
        }
    },
    series: [{
        name: 'Brands',
        colorByPoint: true,
        data: [<?php echo $data_set_vertical_four[0]; ?>]
    }],
	drilldown: {
		series: [<?php echo $data_set_vertical_four[1]; ?>]
    }
});
</script>