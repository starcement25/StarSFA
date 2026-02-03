<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\User;
use App\Http\Requests;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Database\DbOnTheFly;
use App\Helpers\Apicommonfunction;
use Session;
use DB;

class OutstandingMasterController extends Controller{
  /**
   * [dydb this function use to connect database on the flay]
   * @param  [varcar] $dbname [database name]
   * @return [object]         [databse connection object]
   */
  public function dydb($dbname){
    $otf = new DbOnTheFly(['database' => $dbname]);
    return $otf;
  }

  public function outstandingmasterincremental(Request $request){
      $nick_name=$request->input('nickname');
      $emp_code=$request->input('emp_code');
      $device_id=$request->device_id;
      $verificationcode=$request->verificationcode;
      $db_name='acedns_'.strtoupper($request->input('nickname'));
      $dydb =$this->dydb($db_name);
      $CUTDB = $dydb->getConnection();
      $isverify=Apicommonfunction::verifyApikey($db_name,$verificationcode);
      if($isverify==1){
           $employeewise_hierarchy=Apicommonfunction::getNameTableMainDb('user_details','employeewise_hierarchy','nick_name',$nick_name);
           $modified_customer_emp_route=Apicommonfunction::getNameTableMainDb('user_details','modified_customer_emp_route','nick_name',$nick_name);
           if($employeewise_hierarchy=='yes'){
           	$employee_hierarchy=Apicommonfunction::return_employee_hierarchy($db_name,$emp_code);
           	$emp_hierarchy_condition=' AND cc.emp_code IN('.$employee_hierarchy.')';
           	$emp_hierarchy_condition_modified=' AND CRER.emp_code IN('.$employee_hierarchy.')';
           }
           else{
           	$emp_hierarchy_condition=" AND cc.emp_code='".$emp_code."'";
           	$emp_hierarchy_condition_modified=" AND CRER.emp_code='".$emp_code."'";
           }
           if($emp_code!='C0007'){
             	if($modified_customer_emp_route=='yes'){
             		$sqlquery=$CUTDB->select("SELECT DISTINCT c.customer_code,c.recid, cc.customer_name, c.invoice_id, c.date, c.invoice_amount, c.due_amount
             				FROM outstanding c, customer_master cc,customer_route_emp_relation CRER WHERE
             				c.customer_code = cc.customer_code AND CRER.customer_code=cc.customer_code ".$emp_hierarchy_condition_modified."
             				ORDER BY c.date ASC");
             	}
             	else{
             		$sqlquery=$CUTDB->select("SELECT DISTINCT c.customer_code,c.recid, cc.customer_name, c.invoice_id, c.date, c.invoice_amount, c.due_amount
             				FROM outstanding c, customer_master cc WHERE
             				c.customer_code = cc.customer_code ".$emp_hierarchy_condition."
             				ORDER BY c.date ASC");
             	}
          }
          else{
          	$sqlquery=$CUTDB->select("SELECT DISTINCT c.customer_code,c.recid, cc.customer_name, c.invoice_id, c.date, c.invoice_amount, c.due_amount
          				FROM outstanding c, customer_master cc WHERE
          				c.customer_code = cc.customer_code
          				ORDER BY c.date ASC");
          }

            $count=count($sqlquery);
          	$contentsrowcolumn=$count.'¥'.'7';
          	if($count>0){
          		$date=date('Y-m-d');
          		$time=date('H:i:s');
          		$contentsdatetime = $date.'€'.$time."\n";
          		foreach($sqlquery as $rowoutstanding){
                $contents  = (($rowoutstanding->customer_code!='')?$rowoutstanding->customer_code: ' ')."^";
          			$contents  .= (($rowoutstanding->customer_name!='')?$rowoutstanding->customer_name: ' ')."^";
          			$contents  .= (($rowoutstanding->invoice_id!='')?$rowoutstanding->invoice_id: ' ')."^";
          			$contents  .= (($rowoutstanding->recid!='')?$rowoutstanding->recid: ' ')."^";
          			$contents  .= (($rowoutstanding->date!='')?$rowoutstanding->date: ' ')."^";
          			$contents  .= (($rowoutstanding->invoice_amount!='')?$rowoutstanding->invoice_amount: ' ')."^";
          			$contents  .= (($rowoutstanding->due_amount!='')?$rowoutstanding->due_amount: ' ');
          			$linecontents  .= $contents."\n";
          		}
          		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
            }
            else{
          		$datacontents = '0'.'¥'.'0';
          	}
            $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
            $url = url('/api/v1/outstandingmaster?nick_name='.$nick_name.'&emp_code='.$emp_code);
            Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
            header("Content-type: application/text");
          	header("Content-Disposition: attachment; filename=outstanding.txt");
          	print "$datacontents";
      }
      else{
        $datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
        $url = url('/api/v1/outstandingmaster');
        Apicommonfunction::insertapilog($db_name,$datetime,$emp_code,$url);
        return 404;
      }
  }


}
