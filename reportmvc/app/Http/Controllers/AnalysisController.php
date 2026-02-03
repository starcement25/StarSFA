<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Http\Requests;

use Session;
use App\Helpers\Analysis;
use App\Helpers\Commonfunctions;

class AnalysisController extends Controller
{
    /*
        Common function to connect dynamic databse.
     */
    private $sdbname;

    public function databasename()
    {
        $sdbname =Session::get('DBNAME');
        return $sdbname;
    }

    /**
     * [collectionanalysis To display collection report]
     * @return boolean [description]
     */
    public function collectionanalysis(){
      $dbname =$this->databasename();
      $avialblefilter=Commonfunctions::getAvilablefilter($dbname);
      $type='';
      $collectionanalysis='';
      $emplist =Analysis::getEmplist($dbname);

      return view('analysis.collectionanalysis',compact('emplist','collectionanalysis','type','avialblefilter'));
    }

    /**
     * [show_collectionanalysis To dispaly collection report result]
     * @return boolean [description]
     */
    public function show_collectionanalysis(Request $request){

      $dbname =$this->databasename();
      $empids=$request->employee;
      $type=$request->duration;

      $satrtdate=$request->start_date;
      $enddate=$request->end_date;
      $collectionanalysis=Analysis::getCollectionReport($dbname,$empids,$type,$satrtdate,$enddate);
      $emplist =Analysis::getEmplist($dbname);

      //return view('analysis.collectionanalysis',compact('emplist','collectionanalysis','type'));
      return $collectionanalysis;
    }

    public function show_invcollectionanalysis(){
      $dbname =$this->databasename();
      $receipt_id=Input::get('receipt_id');
      $customer_name=Input::get('customer_name');

      $data = Analysis::getCollectionInvdata($dbname,$receipt_id,$customer_name);

      return $data;
    }
    public function show_statelisting(){
      $dbname =$this->databasename();
      $conditionvalue=Input::get('conditionfiledvalue');
      $type=Input::get('type');
      $conditionfiledname=Input::get('conditionfiledname');
      $data = Commonfunctions::show_data($dbname,$conditionvalue,$type,$conditionfiledname);

      return $data;
    }

    public function show_districtlisting(){
      $dbname =$this->databasename();
      $conditionvalue=Input::get('conditionfiledvalue');
      $type=Input::get('type');
      $conditionfiledname=Input::get('conditionfiledname');

      $data = Commonfunctions::show_data($dbname,$conditionvalue,$type,$conditionfiledname);

      return $data;
    }

    public function show_hqlisting(){
      $dbname =$this->databasename();
      $conditionvalue=Input::get('conditionfiledvalue');
      $type=Input::get('type');
      $conditionfiledname=Input::get('conditionfiledname');

      $data = Commonfunctions::show_data($dbname,$conditionvalue,$type,$conditionfiledname);

      return $data;
    }


    public function show_designationlisting(){

      $dbname =$this->databasename();
      $conditionvalue=Input::get('conditionfiledvalue');
      $type=Input::get('type');
      $conditionfiledname=Input::get('conditionfiledname');

      $data = Commonfunctions::show_data($dbname,$conditionvalue,$type,$conditionfiledname);

      return $data;

    }

    public function show_emplisting(){

      $dbname =$this->databasename();
      $zone=(Input::get('zone'))?Input::get('zone'):'';
      $state=(Input::get('state'))?Input::get('state'):'';
      $hq=(Input::get('hq'))?Input::get('hq'):'';
      $district=(Input::get('district'))?Input::get('district'):'';
      $designation=(Input::get('designation'))?Input::get('designation'):'';


      $data = Commonfunctions::show_emp_data($dbname,$zone,$state,$district,$hq,$designation);

      return $data;

    }

    public function newcustomeranalysis(){
      $dbname =$this->databasename();
      $newcust=Analysis::getMonthWiseNewEmployeeCnt($dbname);

      return view('analysis.newcustomer',compact('newcust'));
    }

    public function newcustomerdetails($id){
      $dbname =$this->databasename();
      $monthyear=$id;

      $type='';
      $avialblefilter=Commonfunctions::getAvilablefilter($dbname);
      $yearmonthwisecustdetails=Analysis::getYearMonthWiseNewCustomerDetils($dbname,$monthyear);

      return view('analysis.newcustomerdetails',compact('type','avialblefilter','yearmonthwisecustdetails'));
    }

    public function newcustomeremployeewise(Request $request){

      $dbname =$this->databasename();
      $empid=$request->employee;
      $empwisenewcust=Analysis::getNewCustomerEmployeeWise($dbname,$empid);

       return $empwisenewcust;

    }


}
