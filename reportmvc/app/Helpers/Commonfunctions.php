<?php
namespace App\Helpers;


use App\Database\DbOnTheFly;

use DB;
use Session;


class Commonfunctions {

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

   public static function getLastRecord($dbname,$tablename='',$fieldname=''){

          $CUTDB = self::dydb($dbname);
          return $dbname;

   }

   /**
    * [getAllDetailsById This function return all the data based on the sigle condition]
    * @param  [type] $dbname    [Dynamic Database name]
    * @param  string $tablename [Table name]
    * @param  string $fieldname [Condition apply based on this Field in where clase ]
    * @param  string $parameter [Dynamic Condition value]
    * @return [array]            [Its retun a array value]
    */
   public static function getAllDetailsById($dbname,$tablename='',$fieldname='',$parameter){

          $CUTDB = self::dydb($dbname);
          $alldetails=$CUTDB->table($tablename)
                      ->where($fieldname, '=', $parameter)
                      ->get();
          return $alldetails;

   }

   public static function getNameTable($dbname,$tablename='',$selectfiled,$fieldname='',$parameter){


          $CUTDB = self::dydb($dbname);
          $fildvalue=$CUTDB->table($tablename)
                      ->select($selectfiled)
                      ->where($fieldname, '=', $parameter)
                      ->first();
          //print_r($fildvalue);exit;
          if(count($fildvalue)>0){
            $val = ($fildvalue->$selectfiled)?$fildvalue->$selectfiled:'';
          }
          else{
            $val ='';
          }
          return $val;

   }

   public static function getNameTableMainDb($tablename='',$selectfiled,$fieldname='',$parameter){

          $fildvalue=DB::table($tablename)
                      ->select($selectfiled)
                      ->where($fieldname, '=',$parameter)
                      ->first();
          if(count($fildvalue)>0){
            $val = $fildvalue->$selectfiled;
          }
          else{
            $val ='';
          }
          return $val;

   }

   public static function getNameINTable($dbname,$tablename='',$selectfiled,$fieldname='',$parameter){


          $CUTDB = self::dydb($dbname);
          $brnachcode=explode(',',$parameter);
          $fildvalue=$CUTDB->table($tablename)
                      ->select($selectfiled)
                      ->whereIn($fieldname,$brnachcode)
                      ->get();
          if(count($fildvalue)>0){
            $name='';
            foreach ($fildvalue as $key => $value) {
              $name.=$value->$selectfiled.',';
            }
            $val = substr($name,0,-1);
          }
          else{
            $val ='';
          }
          return $val;

   }

   public static function return_employee_hierarchy($dbname,$emp_code) {
     $emphierarchy = array();
     $emphierarchystring='';
     $emphierarchy=self::employee_hierarchy_details($dbname,$emp_code,$emphierarchy);
      foreach($emphierarchy as $hierarchyval)
      {
          $emphierarchystring[]=$hierarchyval;
      }
       $emphierarchystring =implode(",",$emphierarchystring);
      //$emphierarchystring=substr($emphierarchystring,0,-1).','."'".$emp_code."'";
       return $emphierarchystring;
   }

   /**
    * [employee_hierarchy_details This function recursivly call to find emp id under one employee]
    * @param  [type] $dbname       [database name]
    * @param  [type] $emp_code     [emp code]
    * @param  [type] $emphierarchy []
    * @return [type]               [emp code array]
    */

   public static function employee_hierarchy_details($dbname,$emp_code,&$emphierarchy){

      $CUTDB = self::dydb($dbname);
      $sqlemphierarchy=$CUTDB->select("SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$emp_code."', reporting_to)");
        if(count($sqlemphierarchy)>0)
        {
           foreach ($sqlemphierarchy as $value) {
            $emphierarchy[] = "'".$value->emp_code."'";
            self::employee_hierarchy_details($dbname,$value->emp_code,$emphierarchy);
          }
        }
       else
       {
          if(!in_array("'".$emp_code."'",$emphierarchy))
          {
            $emphierarchy[] ="'".$emp_code."'";
          }
        }
       return $emphierarchy;
   }

   public static function getAvilablefilter($dbname){
     $CUTDB = self::dydb($dbname);
     $nickname = substr(Session::get('DBNAME'),7);
     $avilablefilter=DB::table('configuration_filter')
                     ->select('filter_value')
                     ->where('nick_name', '=',$nickname)
                     ->first();
      if(count($avilablefilter)>0){
       $val = $avilablefilter->filter_value;
      }
      else{
        $val ='';
      }
      return $val;
   }

   public static function getListTable($dbname,$tablename, $fildname){

        $CUTDB = self::dydb($dbname);
        if(Session::get('USERNAME')=="admin"){
      		$emp_hierarchy_value = '';
      		$zone_condition = " WHERE zone != '' ";
      		$state_condition = " WHERE state != '' ";
      		$branch_condition = " WHERE branch_code != '' ";
      		$sale_access_condition = " WHERE sale_access != '' ";
      		$hq_condition = " WHERE hq != '' ";
      		$designation_condition = " WHERE designation != '' ";
      	}
      	else{
      		$emp_hierarchy_value=self::return_employee_hierarchy(Session::get('USERNAME'));
      		$zone_condition = " AND zone != '' ";
      		$state_condition = " AND state != '' ";
      		$branch_condition = " AND branch_code != '' ";
      		$sale_access_condition = " AND sale_access != '' ";
      		$hq_condition = " AND hq != '' ";
      		$designation_condition = " AND designation != '' ";
      	}
        if(Session::get('USERNAME')=="admin"){
            $fildvalue=$CUTDB->table($tablename)
                        ->select($fildname)
                        ->where($fildname, '!=','')
                        ->distinct()
                        ->lists($fildname,$fildname);
        }
        else{
          $newempid=str_replace("'",'',$emp_hierarchy_value);
          $empidarr=explode(',',$newempid);
          $fildvalue=$CUTDB->table($tablename)
                      ->select($fildname)
                      ->where($fildname, '!=','')
                      ->whereIn('emp_code',$empidarr)
                      ->distinct()
                      ->lists($fildname,$fildname);
        }

        return $fildvalue;



   }

   public static function getListAjaxTable($dbname,$tablename,$fildname,$conditionvalue,$conditionfiledname){

        $CUTDB = self::dydb($dbname);
        if(Session::get('USERNAME')=="admin"){
      		$emp_hierarchy_value = '';

      	}
      	else{
      		$emp_hierarchy_value=self::return_employee_hierarchy(Session::get('USERNAME'));

      	}

        if(Session::get('USERNAME')=="admin"){
            $fildvalue=$CUTDB->table($tablename)
                        ->select($fildname)
                        ->where($fildname, '!=','')
                        ->where($conditionfiledname, '=',$conditionvalue)
                        ->distinct()
                        ->get();
        }
        else{
          $newempid=str_replace("'",'',$emp_hierarchy_value);
          $empidarr=explode(',',$newempid);
          $fildvalue=$CUTDB->table($tablename)
                      ->select($fildname)
                      ->where($fildname, '!=','')
                      ->where($conditionfiledname, '=',$conditionvalue)
                      ->whereIn('emp_code',$empidarr)
                      ->distinct()
                      ->get();
        }

        return $fildvalue;



   }
   public static function getListEmpAjaxTable($dbname,$tablename,$zone,$state,$district,$hq,$designation){

             $CUTDB = self::dydb($dbname);

             if(Session::get('USERNAME')=="admin"){
           		$emp_hierarchy_value = '';
           	 }
           	 else{
           		 $emp_hierarchy_value=self::return_employee_hierarchy(Session::get('USERNAME'));
           	 }

             $fildvalue=$CUTDB->table($tablename)->select('emp_code','emp_name');
             if($zone) $fildvalue->where('zone', '=',$zone);
             if($state) $fildvalue->where('state', '=',$state);
             if($district) $fildvalue->where('District', '=',$district);
             if($hq) $fildvalue->where('HQ', '=',$hq);
             if($designation) $fildvalue->where('designation', '=',$designation);
             if($emp_hierarchy_value) {
               $newempid=str_replace("'",'',$emp_hierarchy_value);
               $empidarr=explode(',',$newempid);
               $fildvalue->whereIn('emp_code',$empidarr);
             }

             $result=$fildvalue->get();
             return $result;
             //print_r($result);exit;
    }

   public static function show_data($dbname,$conditionvalue,$type,$conditionfiledname){
     $avialblefilter=self::getAvilablefilter($dbname);
     $filteravilable=explode(',',$avialblefilter);

     if($type == 'state'){
         if(in_array('District',$filteravilable))
           $onclick = "state_district(this.value);";
         else if(in_array('HQ',$filteravilable))
           $onclick = "state_hq(this.value);";
         else if(in_array('Designation',$filteravilable))
           $onclick = "state_designation(this.value);";
         else
           $onclick = "state_emp(this.value);";

     	   $select_control1 =self::getListAjaxTable($dbname, 'employee_master', 'state',$conditionvalue,$conditionfiledname);

         $select_control = "<select name=\"state\" id=\"state\" class=\"form-control\" onchange=\"".$onclick."\">";
      	 $select_control .= "<option value=\"\">Select</option>";
         foreach($select_control1 as $key=>$row_state){
        		$state = $row_state->state;
        		$select_control .= "<option value=\"".$state."\">".$state."</option>";
        	}
        	$select_control .= "</select>";
     }
     if($type == 'district'){
         if(in_array('HQ',$filteravilable))
           $onclick = "district_hq(this.value);";
         else if(in_array('Designation',$filteravilable))
           $onclick = "district_designation(this.value);";
         else
           $onclick = "district_emp(this.value);";

     	   $select_control1 =self::getListAjaxTable($dbname, 'employee_master', 'District',$conditionvalue,$conditionfiledname);

         $select_control = "<select name=\"state\" id=\"district\" class=\"form-control\" onchange=\"".$onclick."\">";
      	 $select_control .= "<option value=\"\">Select</option>";
         foreach($select_control1 as $key=>$row_state){
        		$state = $row_state->state;
        		$select_control .= "<option value=\"".$state."\">".$state."</option>";
        	}
        	$select_control .= "</select>";
     }

     else if($type == 'hq'){
       if(in_array('Designation',$filteravilable))
         $onclick = "hq_designation(this.value);";
       else
         $onclick = "hq_emp(this.value);";
     $select_control1 =self::getListAjaxTable($dbname, 'employee_master', 'hq',$conditionvalue,$conditionfiledname);
     $select_control="<select name=\"hq\" id=\"hq\" onchange=\"".$onclick."\">";
   	 $select_control.="<option value=\"\">Select</option>";

     foreach($select_control1 as $key=>$row_hq){
        $hq = $row_hq->hq;
        $select_control .= "<option value=\"'".$hq."'\">".$hq."</option>";
      }
      $select_control .= "</select>";
    }
    else if($type == 'designation'){
    	$onclick = "designation_emp(this.value);";
    	$select_control= "<select name=\"designation\" id=\"designation\" class=\"form-control\" onchange=\"".$onclick."\">";
    	$select_control.= "<option value=\"\">Select</option>";
    	$select_control1 =self::getListAjaxTable($dbname, 'employee_master', 'designation',$conditionvalue,$conditionfiledname);
    	foreach($select_control1 as $key=>$row_designation){
    		$designation = $row_designation->designation;
    		$select_control.= "<option value=\"".$designation."\">".$designation."</option>";
    	}
    	$select_control.= "</select>";

    }


    return $select_control;
   }

   public static function show_emp_data($dbname,$zone,$state,$district,$hq,$designation){

     $select_control="<select name=\"employee[]\" id=\"employee\" class=\"form-control multiselect-ui\" multiple=\"multiple\">";
     $select_control1 =self::getListEmpAjaxTable($dbname,'employee_master',$zone,$state,$district,$hq,$designation);
     foreach($select_control1 as $key=>$row_emp){
       $emp_code = $row_emp->emp_code;
       $emp_name = $row_emp->emp_name;
       $select_control .= "<option value=\"".$emp_code."\">".$emp_name."</option>";
     }
     $select_control .= "</select>";

     return $select_control;

   }

   public static function getNameMenuDetails(){
     $nickname=substr(Session::get('DBNAME'),7);
     $menuaccessdetails= DB::table('menu_details')
                ->join('user_details', 'menu_details.nick_name', '=', 'user_details.nick_name')
                ->join('product_details', 'menu_details.nick_name', '=', 'product_details.nick_name')
                ->join('order_form_details', 'menu_details.nick_name', '=', 'order_form_details.nick_name')
                ->where('menu_details.nick_name','=',$nickname)
                ->select('menu_details.*', 'user_details.vertical_fields','user_details.no_of_branches', 'product_details.uom_wise_mrp','product_details.branch_wise_mrp','product_details.state_wise_mrp','order_form_details.add_customer')
                ->first();
     return $menuaccessdetails;
  }






}
