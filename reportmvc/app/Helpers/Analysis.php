<?php
namespace App\Helpers;


use App\Database\DbOnTheFly;
use App\Helpers\Commonfunctions;
use Session;

class Analysis {

    /**
      *  @param  string  $dbname
      *
      * @return databse object
     */
     public static function dydb($dbname)
     {
        $otf = new DbOnTheFly(['database' => $dbname]);
        $CUTDBOBJ = $otf->getConnection();
        return $CUTDBOBJ;
     }

     /**
      * [getEmplist List all active employee]
      * @param  [type] $dbname [databse name]
      * @return [array]         [select list array]
      */
     public static function getEmplist($dbname)
     {
   		   $CUTDB = self::dydb($dbname);
         $emplists = $CUTDB->table('employee_master')
                 ->where('acedns', 'Y')
                 ->orderBy('emp_name','ASC')
                 ->lists('emp_name','emp_code');
         return $emplists;

     }

     public static function getCollectionReport($dbname,$empids,$type,$satrtdate,$enddate){
       $CUTDB = self::dydb($dbname);
       $total_collection_amount='';
         if($type == 'today'){
         	$today = date('Y-m-d');
         	$payment_status = "Today's Collection Status";
         	$payment_header_cond = " SUBSTRING(PH.receipt_id,-14,8) = '".str_replace("-","",$today)."' ";
         	$value = 1;
         }
         else if($type == 'mtd'){
         	@$current_date = date('Y-m-d');
         	$month = explode("-",$current_date);
         	$year = $month[0];
         	$month = $month[1];
         	$payment_status = "Collection Status MTD";
         	$payment_header_cond = " SUBSTRING(PH.receipt_id,-14,4) =$year AND substring(PH.receipt_id,-10,2) =$month ";
         	$value = 2;
         }
         else if($type == 'custom'){
         	$start_date = str_replace("-","",$satrtdate);
         	$end_date = str_replace("-","",$enddate);
         	$value = 3;
         	$payment_status = "Collection Status From ".date('d-m-Y',strtotime(''.$satrtdate.''))." To ".date('d-m-Y',strtotime(''.$enddate.''));
         	$payment_header_cond = " (SUBSTRING(PH.receipt_id,-14,8) BETWEEN ".date('Ymd',strtotime(''.$satrtdate.''))." AND ".date('Ymd',strtotime(''.$enddate.'')).") ";
         }
         else{
         	$today = date('Y-m-d');
         	$payment_status = "Today's Collection Status";
         	$payment_header_cond = " SUBSTRING(PH.receipt_id,-14,8) = '".str_replace("-","",$today)."' ";
         	$value = 1;
         }
         $htmlview="<tr>
                <td align='center' colspan='4'>$payment_status</td>
             </tr>
             <tr class='TDHEAD_SUB' align='center'>
               <td>SI</td>
               <td width='20%''>Date</td>
               <td>Customer Name</td>
               <td width='20%'>Collected<br />Amount</td></tr>";
               foreach ($empids as $empid) {

               	 $emp_cond = " AND SUBSTRING(PH.receipt_id,2,5)='".$empid."' ";

                 $count = 1;
                 $emp_name_array = array();
                 $receipt_date_array = array();
                 $sql_collection = $CUTDB->select("SELECT PH.receipt_id, PH.customer_code, DATE_FORMAT(SUBSTRING(PH.receipt_id,-14,8),'%d-%m-%Y') as receipt_date FROM payment_header PH, employee_master EM WHERE ".$payment_header_cond." AND PH.receipt_id LIKE 'P%'".$emp_cond." AND EM.emp_code=SUBSTRING(PH.receipt_id,2,5) ORDER BY EM.emp_name ASC, receipt_date DESC");

                 if(count($sql_collection)>0){
                    foreach($sql_collection as $key=>$row_collection){
                      $receipt_id = $row_collection->receipt_id;
                      $customer_code = $row_collection->customer_code;
                      $receipt_date = $row_collection->receipt_date;

                      $sql_empname = $CUTDB->select("SELECT emp_name FROM employee_master WHERE emp_code='".substr($receipt_id,1,5)."'")[0];

                      $emp_name = $sql_empname->emp_name;
                      if(!in_array($emp_name,$emp_name_array)){
                        array_push($emp_name_array,$emp_name);
                        $receipt_date_array = array();
                        $htmlview.= "<tr class=\"TDHEAD\"><td colspan='4' align='center'>$emp_name</td></tr>";
                      }

                      $sql_customer_name = $CUTDB->table('customer_master')
                                  ->select('customer_name')
                                  ->where('customer_code', '=', $customer_code)
                                  ->first();
                      if(count($sql_customer_name)>0) $customer_name = $sql_customer_name->customer_name;

                      $sql_payment_details = $CUTDB->select("SELECT SUM(amount) as collected_amount FROM payment_details WHERE receipt_id='".$receipt_id."'")[0];

                      $collection_amount = $sql_payment_details->collected_amount;

                      $total_collection_amount += $collection_amount;

                      $htmlview.=  "<tr>
                          <td>".$count."</td>";
                          if(!in_array($receipt_date,$receipt_date_array)){
                            array_push($receipt_date_array,$receipt_date);
                            $htmlview.=  "<td bgcolor=\"#F2F2F2\" style=\"font-weight:bold;\">".$receipt_date."</td>";
                          }
                          else{
                          $htmlview.=  "<td></td>";
                          }
                          $htmlview.=  "<td><a href=\"#\" style=\"color:blue; font-weight:bold;\" onclick=\"show_invoice('$receipt_id','$customer_name');\">".$customer_name."</a></td>
                          <td align=\"right\">".number_format($collection_amount,2)."</td>
                          </tr>";
                      $count++;
                    }
                    $htmlview.=  "<tr style=\"font-weight:bold;\">
                        <td colspan='3'>Total</td>
                        <td align=\"right\">".number_format($total_collection_amount,2)."</td>
                        </tr>";
                    $htmlview.=  "</table>";
                   }


              }
       return $htmlview;
     }

     public static function getCollectionInvdata($dbname,$receipt_id,$customer_name){
       $CUTDB = self::dydb($dbname);
       $total_invoice_amount='';
       $total_amount='00';
       $invoice_date='';
       $invoice_amt='00';
       $count = 1;
       $sql_invoice = $CUTDB->select("SELECT invoice_id, amount FROM payment_details WHERE receipt_id='".$receipt_id."'");
       if(count($sql_invoice)>0){

           $htmlview ="<table width='100%' border='1' style='border-collapse:collapse;' cellpadding='6'>
             <tr><td colspan='5' align='center'>Invoice Details - $customer_name</td></tr>
             <tr align='center' style='font-weight:bold;'>
               <td>SI</td>
               <td width='20%''>Invoice Date</td>
               <td>Invoice No</td>
               <td>Invoice Amount</td>
               <td>Collected Amount</td>
             </tr>";

           	foreach($sql_invoice as $key=>$row_invoice){
                 	$invoice_id = $row_invoice->invoice_id;
                  $amount = $row_invoice->amount;

                 	$sql_outstanding =$CUTDB->table('outstanding')
                              ->select('date','invoice_amount')
                              ->where('invoice_id', '=', $invoice_id)
                              ->first();
                if(count($sql_outstanding)>0)
                {
                    if($sql_outstanding->invoice_amount==''){
                      $invoice_amt =$sql_outstanding->invoice_amount;
                    }
                    if($sql_outstanding->date==''){
                    $invoice_date =date('d-m-Y',strtotime($sql_outstanding->date));
                    }
                }


                  if($invoice_id == ''){
                 		$invoice_id = 'On Account';
                 		$invoice_date='';
                 		$invoice_amt=0;
                 	}

                 	$htmlview .= "<tr><td>".$count."</td>
                 			<td>".$invoice_date."</td>
                 			<td>".$invoice_id."</td>
                 			<td align=\"right\">".number_format($invoice_amt,2)."</td>
                 			<td align=\"right\">".number_format($amount,2)."</td>
                 		  </tr>";
                 	$total_amount += $amount;
                 	$total_invoice_amount += $invoice_amt;
                 	$count++;

                 	$htmlview .= "<tr style=\"font-weight:bold;\">
                 			<td colspan=\"3\">Total</td>
                 			<td align=\"right\">".number_format($total_invoice_amount,2)."</td>
                 			<td align=\"right\">".number_format($total_amount,2)."</td>
                 		  </tr>";
           }
       }
       else{
       	$htmlview = "<font color='red'><strong>No records</strong></font>";
       }


       return $htmlview;
}

public static function getMonthWiseNewEmployeeCnt($dbname){
  $CUTDB = self::dydb($dbname);
  if(date('m') >= 04) {
    $start = date('Y').'04';
    $end=(date('Y') +1).'03';
  } else {
    $start =(date('Y')-1).'04';
    $end=date('Y').'03';
  }
  $newempArray = [];
  //$input = array("#008ee4"=>"a","#6baa01"=>"b","#e44a00"=>"c");
  $color_array = array('#008ee4','#6baa01','#e44a00');
  $x=0;
  $query=$CUTDB->select("SELECT count(*) as CNT,SUBSTRING(customer_code,-14,6) as date1 FROM `customer_master` WHERE customer_code LIKE 'N%' AND SUBSTRING(customer_code,-14,6) BETWEEN $start and $end GROUP BY SUBSTRING(customer_code,-14,6)");
  foreach ($query as $key => $value) {
    $x++;
    $color=$color_array[$x%3];
    $monthNum=substr($value->date1,4);
    $monthname=date("F", mktime(0, 0, 0, $monthNum, 10));
    array_push($newempArray, [
     "label"=>"$monthname",
     "value" => "$value->CNT",
     "color"=>"$color"
   ]);
  }
  $newempJSON = json_encode($newempArray);
  return $newempJSON;
}

public static function getYearMonthWiseNewCustomerDetils($dbname,$monthyear){
  $CUTDB = self::dydb($dbname);
  $query=$CUTDB->table('customer_master')
               ->where($CUTDB->raw("SUBSTRING(customer_code,-14,6)"),'=',$monthyear)
               ->where('customer_code', 'LIKE', 'N%')
               ->paginate(50);


  return $query;
}

public static function getNewCustomerEmployeeWise($dbname,$empid){
  $CUTDB = self::dydb($dbname);
  $query=$CUTDB->table('customer_master')
               ->where($CUTDB->raw("SUBSTRING(customer_code,-19,5)"),'=',$empid)
               ->where('customer_code', 'LIKE', 'N%')
               ->get();
  $htmlview="<table class=\"table table-striped jambo_table bulk_action\" id=\"newcust\">
    <thead>
      <tr class=\"headings\">
        <th class=\"column-title\" style=\"display: table-cell;\">Customer  Code </th>
        <th class=\"column-title\" style=\"display: table-cell;\">Customer Name</th>
        <th class=\"column-title\" style=\"display: table-cell;\">Customer Phone Number</th>
        <th class=\"column-title\" style=\"display: table-cell;\">Assign Employee</th>
        <th class=\"column-title no-link last\" style=\"display: table-cell;\"><span class=\"nobr\">Action</span>
        </th>
      </tr>
    </thead>
    <tbody>";
    foreach($query as $key=>$val){
      $custcode=str_replace('/', '',$val->customer_code);
      $url=url('/customer/'.$custcode);
      $htmlview.="<tr class=\"even pointer\">";
      $htmlview.="<td>".$val->dns_customer_code."</td>";
      $htmlview.="<td>".$val->customer_name."</td>";
      $htmlview.="<td>".$val->phone_no."</td>";
      $htmlview.="<td>".$val->emp_code."</td>";
      $htmlview.="<td><a href=".$url." target=\"_blank\"><i class=\"fa fa-eye\"></i></a></td>";
      $htmlview.="</tr>";
    }
    $htmlview.="</tbody></table>";

  return $htmlview;
}

}
