<?php
/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2012 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.7.8, 2012-10-12
 */

/** Error reporting */
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
ini_set('memory_limit', '-1');

ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
ob_end_flush();

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('Europe/London');

/** Include PHPExcel */
require_once 'phpexcel/Classes/PHPExcel.php';
include 'phpexcel/Classes/PHPExcel/Writer/Excel2007.php';

  if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='1';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' emp_code IN('.$emp_hierarchy.')';
	}
 $objPHPExcel = new PHPExcel();
 $i=0; while ($i < 1) {
//$objWorksheet = new PHPExcel_Worksheet($objPHPExcel);
//$objPHPExcel->addSheet($objWorksheet);
	if($i==0)
	{
		$date=date('d/m/y');
		$xlsReader= new PHPExcel_Reader_Excel2007();
		$xlsTemplate = $xlsReader->load("secondary-report-template/secondary-sales-brandwise.xlsx");
		$sheet1 = $xlsTemplate->getSheet(0);
		$objPHPExcel->addExternalSheet( $sheet1, 1 );
		$sheet1->setTitle('BRANDWISE SECONDARY SALES');
		
		$borderArray = array(
			  'borders' => array(
				'allborders' => array(
				  'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			  )
			);
		$boldArray = array('font' => array('bold' => true,));
		
		$sqlvertical_data="SELECT vertical_value,(CASE WHEN SUBSTRING(operation_date,6,2)='08' THEN SUM(qty) ELSE 0 END) AS total_qty_AUG,
						(CASE WHEN SUBSTRING(operation_date,6,2)='09' THEN SUM(qty) ELSE 0 END) AS total_qty_SEPT,
						(CASE WHEN SUBSTRING(operation_date,6,2)='10' THEN SUM(qty) ELSE 0 END) AS total_qty_OCT,
						(CASE WHEN SUBSTRING(operation_date,6,2)='11' THEN SUM(qty) ELSE 0 END) AS total_qty_NOV,
						(CASE WHEN SUBSTRING(operation_date,6,2)='12' THEN SUM(qty) ELSE 0 END) AS total_qty_DEC,
						(CASE WHEN SUBSTRING(operation_date,6,2)='01' THEN SUM(qty) ELSE 0 END) AS total_qty_JAN,
						(CASE WHEN SUBSTRING(operation_date,6,2)='02' THEN SUM(qty) ELSE 0 END) AS total_qty_FEB,
						(CASE WHEN SUBSTRING(operation_date,6,2)='03' THEN SUM(qty) ELSE 0 END) AS total_qty_MAR
						 from vertical_branch_employeewise_details 
						WHERE $emp_hierarchy_condition GROUP BY vertical_value ORDER BY vertical_value ASC";
		$rsvertical_data=mysqli_query($link,$sqlvertical_data);
		$count=4;
		while($rowvertical_data=mysqli_fetch_assoc($rsvertical_data))
		{
		  $qty_monthly_AUG=round($rowvertical_data['total_qty_AUG'],0);
		  $qty_monthly_SEPT=round($rowvertical_data['total_qty_SEPT'],0);
		  $qty_monthly_OCT=round($rowvertical_data['total_qty_OCT'],0);
		  $qty_monthly_NOV=round($rowvertical_data['total_qty_NOV'],0);
		  $qty_monthly_DEC=round($rowvertical_data['total_qty_DEC'],0);
		  $qty_monthly_JAN=round($rowvertical_data['total_qty_JAN'],0);
		  $qty_monthly_FEB=round($rowvertical_data['total_qty_FEB'],0);
		  $qty_monthly_MAR=round($rowvertical_data['total_qty_MAR'],0);
		  $vertical_value=$rowvertical_data['vertical_value'];
		
		  $total_qty_monthly_AUG=$total_qty_monthly_AUG+$qty_monthly_AUG;
		  $total_qty_monthly_SEPT=$total_qty_monthly_SEPT+$qty_monthly_SEPT;
		  $total_qty_monthly_OCT=$total_qty_monthly_OCT+$qty_monthly_OCT;
		  $total_qty_monthly_NOV=$total_qty_monthly_NOV+$qty_monthly_NOV;
		  $total_qty_monthly_DEC=$total_qty_monthly_DEC+$qty_monthly_DEC;
		  $total_qty_monthly_JAN=$total_qty_monthly_JAN+$qty_monthly_JAN;
		  $total_qty_monthly_FEB=$total_qty_monthly_FEB+$qty_monthly_FEB;
		  $total_qty_monthly_MAR=$total_qty_monthly_MAR+$qty_monthly_MAR;
		  
		  ${total_qty.$vertical_value}=${total_qty.$vertical_value}+$qty_monthly_AUG+$qty_monthly_SEPT+$qty_monthly_OCT;
		   $total_qty_yearly=$total_qty_yearly+${total_qty.$vertical_value};
				
			$sheet1->setCellValue('B'.$count,"$vertical_value");
			$sheet1->setCellValue('C'.$count,"$qty_monthly_AUG");
			$sheet1->setCellValue('D'.$count,"$qty_monthly_SEPT");
			$sheet1->setCellValue('E'.$count,"$qty_monthly_OCT");
			$sheet1->setCellValue('F'.$count,"$qty_monthly_NOV");
			$sheet1->setCellValue('G'.$count,"$qty_monthly_DEC");
			$sheet1->setCellValue('H'.$count,"$qty_monthly_JAN");
			$sheet1->setCellValue('I'.$count,"$qty_monthly_FEB");
			$sheet1->setCellValue('J'.$count,"$qty_monthly_MAR");
			$sheet1->setCellValue('K'.$count,"${total_qty.$vertical_value}");
			$sheet1->getStyle('B'.$count)->applyFromArray($boldArray);
			$sheet1->getStyle('B'.$count.':K'.$count)->applyFromArray($borderArray);
			
			//cumulative
			$sheet1->setCellValue('M'.$count,"$vertical_value");
			$sheet1->setCellValue('N'.$count,"$qty_monthly_AUG");
			$sheet1->setCellValue('O'.$count,"$qty_monthly_SEPT");
			$sheet1->setCellValue('P'.$count,"$qty_monthly_OCT");
			$sheet1->setCellValue('Q'.$count,"$qty_monthly_NOV");
			$sheet1->setCellValue('R'.$count,"$qty_monthly_DEC");
			$sheet1->setCellValue('S'.$count,"$qty_monthly_JAN");
			$sheet1->setCellValue('T'.$count,"$qty_monthly_FEB");
			$sheet1->setCellValue('U'.$count,"$qty_monthly_MAR");
			$sheet1->setCellValue('V'.$count,"${total_qty.$vertical_value}");
			$sheet1->getStyle('M'.$count)->applyFromArray($boldArray);
			$sheet1->getStyle('M'.$count.':V'.$count)->applyFromArray($borderArray);


		$count++;
		}
		$sheet1->getStyle('B'.$count.':K'.$count)->applyFromArray($borderArray);
		$sheet1->getStyle('B'.$count.':K'.$count)->applyFromArray($boldArray);
		
		$sheet1->getStyle('M'.$count.':V'.$count)->applyFromArray($borderArray);
		$sheet1->getStyle('M'.$count.':V'.$count)->applyFromArray($boldArray);

		$sheet1->setCellValue('B'.$count,"Grand Total");
		$sheet1->setCellValue('C'.$count,"$total_qty_monthly_AUG");
		$sheet1->setCellValue('D'.$count,"$total_qty_monthly_SEPT");
		$sheet1->setCellValue('E'.$count,"$total_qty_monthly_OCT");
		$sheet1->setCellValue('F'.$count,"$total_qty_monthly_NOV");
		$sheet1->setCellValue('G'.$count,"$total_qty_monthly_DEC");
		$sheet1->setCellValue('H'.$count,"$total_qty_monthly_JAN");
		$sheet1->setCellValue('I'.$count,"$total_qty_monthly_FEB");
		$sheet1->setCellValue('J'.$count,"$total_qty_monthly_MAR");
		$sheet1->setCellValue('K'.$count,"$total_qty_yearly");
		
		$sheet1->setCellValue('M'.$count,"Grand Total");
		$sheet1->setCellValue('N'.$count,"$total_qty_monthly_AUG");
		$sheet1->setCellValue('O'.$count,"$total_qty_monthly_SEPT");
		$sheet1->setCellValue('P'.$count,"$total_qty_monthly_OCT");
		$sheet1->setCellValue('Q'.$count,"$total_qty_monthly_NOV");
		$sheet1->setCellValue('R'.$count,"$total_qty_monthly_DEC");
		$sheet1->setCellValue('S'.$count,"$total_qty_monthly_JAN");
		$sheet1->setCellValue('T'.$count,"$total_qty_monthly_FEB");
		$sheet1->setCellValue('U'.$count,"$total_qty_monthly_MAR");
		$sheet1->setCellValue('V'.$count,"$total_qty_yearly");

	}
$i++; 
}
 //$objPHPExcel->getActiveSheet()->setTitle('List of Photos');
$objPHPExcel->removeSheetByIndex(0);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// It will be called file.xls
header('Content-Disposition: attachment; filename="secondary-sales-report.xlsx"');
// Write file to the browser
$objWriter->save('php://output');

// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;
// Echo done
echo date('H:i:s') , " Done writing files" , EOL;
echo 'Files have been created in ' , getcwd() , EOL;
