<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;
use App\Helpers\Attendence;
use App\Helpers\Commonfunctions;

use Session;


class AttendanceController extends Controller
{


  private $sdbname;

  public function databasename()
  {
      $sdbname =Session::get('DBNAME');
      return $sdbname;
  }

  /**
   * Display a attendance.
   *
   * @return Response
   */
  public function showtodayattendance()
  {
    $sdbname =$this->databasename();
    $attendancelists=Attendence::gettodayattendence($sdbname);
    return view('attendance.index',compact('attendancelists'));
  }

  public function showattendancecalview(){
    $sdbname =$this->databasename();
    $emplists=Attendence::getEmpList($sdbname);
    $presentlist=Attendence::getAttendenceList($sdbname);
    return view('attendance.calview',compact('emplists','presentlist'));

  }

  public function showsearchresult(){
    $sdbname =$this->databasename();
    $emplists=Attendence::getEmpList($sdbname);
    $month=Input::get('selec_mn');
    $year=Input::get('selec_year');
    
    $presentlist=Attendence::getAttendenceList($sdbname,$month,$year);
    return view('attendance.calview',compact('emplists','presentlist'));
  }















}
