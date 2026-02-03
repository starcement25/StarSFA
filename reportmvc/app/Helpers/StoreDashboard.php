<?php
namespace App\Helpers;

use DB;

class StoreDashboard {

    public static function getAllStoreList()
    {

        $storelists = DB::table('user_details')
                      ->select('name','address','phone_no','email','nick_name','user_id')
                      ->where('working_mode','live')
                      ->paginate(50);
        return $storelists;

    }

    public static function getAllUserTableData(){
      $schema =DB::select("SELECT column_name as 'ColumnName', data_type as 'DataType',column_default as 'Defaultvalue' FROM information_schema.columns WHERE table_name = 'user_details'");
      return $schema;
    }

    public static function getAllMenuTableData(){
      $schema =DB::select("SELECT column_name as 'ColumnName', data_type as 'DataType',column_default as 'Defaultvalue' FROM information_schema.columns WHERE table_name = 'menu_details'");
      return $schema;
    }

    public static function getAllProductTableData(){
      $schema =DB::select("SELECT column_name as 'ColumnName', data_type as 'DataType',column_default as 'Defaultvalue' FROM information_schema.columns WHERE table_name = 'product_details'");
      return $schema;
    }

    public static function getAllOrderDetailsTableData(){
      $schema =DB::select("SELECT column_name as 'ColumnName', data_type as 'DataType',column_default as 'Defaultvalue' FROM information_schema.columns WHERE table_name = 'order_form_details'");
      return $schema;
    }
    public static function getAllSurvayFormDetailsTableData(){
      $schema =DB::select("SELECT column_name as 'ColumnName', data_type as 'DataType',column_default as 'Defaultvalue' FROM information_schema.columns WHERE table_name = 'survey_form_details'");
      return $schema;
    }
    public static function getAllSaudaFormDetailsTableData(){
      $schema =DB::select("SELECT column_name as 'ColumnName', data_type as 'DataType',column_default as 'Defaultvalue' FROM information_schema.columns WHERE table_name = 'sauda_form_details'");
      return $schema;
    }
    public static function getAllRoutePlanDetailsTableData(){
      $schema =DB::select("SELECT column_name as 'ColumnName', data_type as 'DataType',column_default as 'Defaultvalue' FROM information_schema.columns WHERE table_name = 'route_plan_details'");
      return $schema;
    }
    public static function getAllMarketFeedbackDetailsTableData(){
      $schema =DB::select("SELECT column_name as 'ColumnName', data_type as 'DataType',column_default as 'Defaultvalue' FROM information_schema.columns WHERE table_name = 'market_feedback_details'");
      return $schema;
    }


}
