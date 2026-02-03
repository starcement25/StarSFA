<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;
use Session;
use App\Helpers\Product;
use App\Helpers\Commonfunctions;


class ProductController extends Controller
{
    private $sdbname;

    public function databasename()
    {
        $sdbname =Session::get('DBNAME');
        return $sdbname;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $sdbname =$this->databasename();
        $products=Product::getAllProductList($sdbname);
        return view('product.index',compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $sdbname =$this->databasename();
        $vartical_list=Product::getVarticallist($sdbname);
        if(Session::get('isproductgroup')=="yes"){
          $productgroup_lists=Product::getProductGrouplist($sdbname);
        }else{
          $productgroup_lists='';
        }
        if(Session::get('isproductsubgroup')=="yes"){
          $productsubgroup_lists=Product::getProductSubGrouplist($sdbname);
        }else{
          $productsubgroup_lists='';
        }
        if(Session::get('isproductbrand')=="yes"){
          $productbrand_lists=Product::getProductBrandlist($sdbname);
        }else{
          $productbrand_lists='';
        }
        $productbranch_lists=Product::getBranchlist($sdbname);
        return view('product.create',compact('vartical_list','productgroup_lists','productsubgroup_lists','productbrand_lists','productbranch_lists'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
      $dbname =$this->databasename();
      $CUTDB = Product::dydb($dbname);


      if(Session::get('isbranchwiseproduct')=='yes')
      {
        $this->validate($request, [
              'brnach_code' => 'required',
        ]);
      }
      if(Session::get('iscodeneed')=='yes')
      {
         $this->validate($request, [
               'product_code' => 'required',
         ]);
      }
      $this->validate($request, [
            'product_desc' => 'required',
      ]);
      if($request->input('product_uom2')!='')
      {
        $this->validate($request, [
              'product_conversion' => 'required',
        ]);
      }
      if($request->input('product_uom3')!='')
      {
        $this->validate($request, [
              'product_conversion2' => 'required',
        ]);
      }

      $branchcode=(($request->input('brnach_code'))?$request->input('brnach_code'):'');
      $dnsproductcode=(($request->input('product_code'))?$request->input('product_code'):'');
      $productgroup=(($request->input('productgroup_lists'))?$request->input('productgroup_lists'):'');
      $productsubgroup=(($request->input('productsubgroup_lists'))?$request->input('productsubgroup_lists'):'');
      $productbrand=(($request->input('productbrand_lists'))?$request->input('productbrand_lists'):'');
      $productdesc=(($request->input('product_desc'))?$request->input('product_desc'):'');
      $prostock=(($request->input('product_stock'))?$request->input('product_stock'):'');
      $isblacklist=(($request->input('isblacklist'))?$request->input('isblacklist'):'');
      $product_uom1=(($request->input('product_uom1'))?$request->input('product_uom1'):'');
      $product_uom2=(($request->input('product_uom2'))?$request->input('product_uom2'):'');
      $product_uom3=(($request->input('product_uom3'))?$request->input('product_uom3'):'');
      $product_conversion=(($request->input('product_conversion'))?$request->input('product_conversion'):'');
      $product_conversion2=(($request->input('product_conversion2'))?$request->input('product_conversion2'):'');
      $td=(($request->input('td'))?$request->input('td'):'');
      $is_focus=(($request->input('is_focus'))?$request->input('is_focus'):'');
      $weightage=(($request->input('weightage'))?$request->input('weightage'):'');
      $vat=(($request->input('vat'))?$request->input('vat'):'');
      $addl_vat=(($request->input('addl_vat'))?$request->input('addl_vat'):'');
      $downloadtime=date('Y-m-d H:i:s');
      $productcode=$CUTDB->table('product_master')->max('prod_code');

      if($productcode!='')
      {
        $productcode=$productcode+1;
      }
      else {
        $productcode='12001';
      }

      $emp_vartical='';
      $empmultiplevarticalValues = $request->input('vartical');
      if(count($empmultiplevarticalValues))
      {
        foreach($empmultiplevarticalValues as $value)
        {
            $emp_vartical.=$value.',';
        }
        $emp_vartical=substr($emp_vartical,0,-1);
      }

      /* Insert group subgroup and brand from product start */
      if($productgroup=="othergroup")
      {
         $productgroupcode=$CUTDB->table('product_group_master')->max('product_group_code');
         if($productgroupcode!='')
         {
           $productgroupcode1=substr($productgroupcode,2);
           $newproductgroupcode=$productgroupcode1+1;
           $productgroupcode='BR'.$newproductgroupcode;
         }
         else {
           $productgroupcode='BR1';
         }
         $productgroupname=$request->input('productgroup_name');

         $CUTDB->table('product_group_master')->insert(array(
             'product_group_code' => $productgroupcode,
             'product_group_name' => $productgroupname,
             'download_time' =>$downloadtime,
             'acedns'=>'Y'
         ));

         $productgroup=$productgroupcode;
      }

      if($productsubgroup=="othersubgroup")
      {
          $productsubgroupcode=$CUTDB->table('product_sub_group_master')->max('product_sub_group_code');

          if($productsubgroupcode!='')
          {
            $productsubgroupcode1=substr($productsubgroupcode,2);
            $newproductsubgroupcode=$productsubgroupcode1+1;
            $productsubgroupcode='BF'.$newproductsubgroupcode;
          }
          else {
            $productsubgroupcode='BF1';
          }
          $productsubgroupname=$request->input('productsubgroup_name');
          $productgroup=(($productgroup=="othergroup")?$productgroupcode:$productgroup);

          $CUTDB->table('product_sub_group_master')->insert(array(
              'product_sub_group_code' => $productsubgroupcode,
              'product_group_code' => $productgroup,
              'product_sub_group_name'=>$productsubgroupname,
              'download_time' =>$downloadtime
          ));

          $productsubgroup=$productsubgroupcode;

      }

      if($productbrand=="otherbrand")
      {
          $productbrandcode=$CUTDB->table('product_brand_master')->max('product_brand_code');
          if($productbrandcode!='')
          {
            $productbrandcode1=substr($productbrandcode,2);
            $newproductbrandcode=$productbrandcode1+1;
            $productbrandcode='BS'.$newproductbrandcode;
          }
          else {
            $productbrandcode='BS1';
          }

          $productgroup=(($productgroup=="othergroup")?$productgroupcode:$productgroup);
          $productsubgroup=(($productsubgroup=="othersubgroup")?$productsubgroupcode:$productsubgroup);
          $productbrandname=$request->input('product_brand_name');

          $CUTDB->table('product_brand_master')->insert(array(
              'product_brand_code' => $productbrandcode,
              'product_group_code' => $productgroup,
              'product_sub_group_code'=>$productsubgroup,
              'product_brand_name'=>$productbrandname,
              'download_time' =>$downloadtime
          ));

          $productbrand=$productbrandcode;


      }
      /* Insert group subgroup and brand from product end */


      $CUTDB->table('product_master')->insert(array(
          'branch_code' => $branchcode,
          'prod_code' => $productcode,
          'dns_prod_code' => $dnsproductcode,
          'product_group_code' => $productgroup,
          'product_sub_group_code'=>$productsubgroup,
          'product_brand_code'=>$productbrand,
          'prod_desc'=>$productdesc,
          'cl_stk'=>$prostock,
          'UOM1'=>$product_uom1,
          'UOM2'=>$product_uom2,
          'UOM3'=>$product_uom3,
          'conversion_factor'=>$product_conversion,
          'conversion_factor_two'=>$product_conversion2,
          'TD'=>$td,
          'acedns'=>'Y',
          'black_list'=>$isblacklist,
          'weightage'=>$weightage,
          'vat'=>$vat,
          'addl_vat'=>$addl_vat,
          'focus'=>$is_focus,
          'vertical_value' => $emp_vartical,
          'download_time' =>$downloadtime
      ));
      return redirect('/product')->with('message', 'Success!');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $sdbname =$this->databasename();
        $tablename="product_master";
        $fieldname="prod_code";
        $parameter=$id;
        $productdetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
        return view('product.view',compact('productdetails'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
      $sdbname =$this->databasename();
      $tablename="product_master";
      $fieldname="prod_code";
      $parameter=$id;
      $productdetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
      $vartical_list=Product::getVarticallist($sdbname);
      if(Session::get('isproductgroup')=="yes"){
        $productgroup_lists=Product::getProductGrouplist($sdbname);
      }else{
        $productgroup_lists='';
      }
      if(Session::get('isproductsubgroup')=="yes"){
        $productsubgroup_lists=Product::getProductSubGrouplist($sdbname);
      }else{
        $productsubgroup_lists='';
      }
      if(Session::get('isproductbrand')=="yes"){
        $productbrand_lists=Product::getProductBrandlist($sdbname);
      }else{
        $productbrand_lists='';
      }
      $productbranch_lists=Product::getBranchlist($sdbname);
      return view('product.edit',compact('productdetails','vartical_list','productgroup_lists','productsubgroup_lists','productbrand_lists','productbranch_lists'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
      $dbname =$this->databasename();
      $CUTDB = Product::dydb($dbname);

      if(Session::get('isbranchwiseproduct')=='yes')
      {
        $rules1[] = array(
          'brnach_code' => 'required',
        );
      }
      if(Session::get('iscodeneed')=='yes')
      {

         $rules1[] = array(
            'product_code' => 'required',
         );
      }

      if(Input::get('product_uom2')!='')
      {
        $rules1[]=array(
              'product_conversion' => 'required',
        );
      }
      if(Input::get('product_uom3')!='')
      {

        $rules1[]=array(
              'product_conversion2' => 'required',
        );
      }
      $rules1[]=array(
            'product_desc' => 'required',
      );


      $rules = array();
      foreach($rules1 as $arr) {
           if(is_array($arr)) {
               $rules = array_merge($rules, $arr);
           }
       }

      $validator = Validator::make(Input::all(), $rules);
      if ($validator->fails())
      {
           return Redirect::to('product/'.$id.'/edit')->withErrors($validator);
      }
      else{
            $branchcode=Input::get('brnach_code');
            $dnsproductcode=Input::get('product_code');
            $productgroup=Input::get('productgroup_lists');
            $productsubgroup=Input::get('productsubgroup_lists');
            $productbrand=Input::get('productbrand_lists');
            $productdesc=Input::get('product_desc');
            $prostock=Input::get('product_stock');
            $isblacklist=Input::get('isblacklist');
            $product_uom1=Input::get('product_uom1');
            $product_uom2=Input::get('product_uom2');
            $product_uom3=Input::get('product_uom3');
            $product_conversion=Input::get('product_conversion');
            $product_conversion2=Input::get('product_conversion2');
            $td=Input::get('td');
            $is_focus=Input::get('is_focus');
            $weightage=Input::get('weightage');
            $vat=Input::get('vat');
            $addl_vat=Input::get('addl_vat');


            $emp_vartical='';
            $empmultiplevarticalValues = Input::get('vartical');
            if(count($empmultiplevarticalValues))
            {
              foreach($empmultiplevarticalValues as $value)
              {
                  $emp_vartical.=$value.',';
              }
              $emp_vartical=substr($emp_vartical,0,-1);
            }


              $CUTDB->table('product_master')
              ->where('prod_code', $id)
              ->limit(1)
              ->update(array('branch_code'=>$branchcode,
              'dns_prod_code' => $dnsproductcode,
              'product_group_code' => $productgroup,
              'product_sub_group_code'=>$productsubgroup,
              'product_brand_code'=>$productbrand,
              'prod_desc'=>$productdesc,
              'cl_stk'=>$prostock,
              'UOM1'=>$product_uom1,
              'UOM2'=>$product_uom2,
              'UOM3'=>$product_uom3,
              'conversion_factor'=>$product_conversion,
              'conversion_factor_two'=>$product_conversion2,
              'TD'=>$td,
              'acedns'=>'Y',
              'black_list'=>$isblacklist,
              'weightage'=>$weightage,
              'vat'=>$vat,
              'addl_vat'=>$addl_vat,
              'focus'=>$is_focus,
              'vertical_value' => $emp_vartical
               ));

              return redirect('/product')->with('message', 'Success!');
            }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function productUploadFile(Request $request)
    {

        $dbname =$this->databasename();
        $CUTDB = Branch::dydb($dbname);

        $file = $request->file('product_csv_file');

        //Display File Name
        $nickname=substr(Session::get('DBNAME'),7);
        $name=$nickname.'_'.$file->getClientOriginalName();
        $ext=$file->getClientOriginalExtension();

        if($ext=='csv')
        {

                      $destinationPath = 'uploads/csv';
                      $file->move($destinationPath,$name);
                      $rec_count = 0;
                      $ins_count = 0;
                      $err = "";

                      $lines = file($filename);
                      $duplicate_product=array();
                      $branch_code_array=array();
                      foreach($lines as $line)
                      {
                          $i = 0;
                          $char = substr($line, $i, 1);
                          $value ="";
                          $data="";
                          $double_coute_found = false;
                          if($rec_count>=1)
                          {
                              while($char!="")
                              {

                                  if($double_coute_found && $char=="\"")
                                  {
                                      $double_coute_found = false;
                                      $i++;
                                      $char = substr($line, $i, 1);
                                      continue;
                                  }

                                  if(!$double_coute_found && $char=="\"")
                                  {

                                      $double_coute_found = true;
                                      $i++;
                                      $char = substr($line, $i, 1);
                                      continue;
                                  }

                                  if($char=="," && !$double_coute_found)
                                  {
                                      $data[]=$value;
                                      $value = "";
                                  }
                                  else
                                  {
                                  $value .= $char;
                                  }
                                  $i++;
                                  $char = substr($line, $i, 1);
                              } //end of while
                               $data[]=$value;
                              //print_r($data);

                              $csv_row_count=$rec_count+1;
                              $branch_code_name=trim($data[0]);

                              if(Session::get('isbranchwiseproduct')=='yes')
                              {
                                  if($branch_code_name == '')
                                  {
                                      echo "Please provide valid branch code at row ".($csv_row_count+1);
                                      die;
                                  }
                              }

                              $dns_prod_code=trim($data[1]);
                              $prod_desc=trim($data[2]);
                              $product_group_code_name=trim($data[3]);
                              $product_sub_group_code_name=trim($data[4]);
                              $product_brand_code_name=trim($data[5]);

                              $cl_stk=trim($data[6]);
                              if(strpos($cl_stk,',')!=false){
                                  $stkpos=strpos($cl_stk,',');
                              $cl_stk = substr($cl_stk,0,$stkpos).substr(strstr($cl_stk, ","),1);
                              }
                              $acedns=trim($data[7]);
                              if($acedns =='')
                              {
                                  echo "Please provide proper value for Acedns column at row ".($csv_row_count+1);
                                  die;
                              }

                              $black_list=trim($data[8]);
                              if($black_list=='')
                              {
                                  echo "Please provide proper value for Blacklist column at row ".($csv_row_count+1);
                                  die;
                              }

                              $vertical_value=trim($data[9]);
                              $UOM1=trim($data[10]);
                              $UOM2=trim($data[11]);
                              $conversion=trim($data[12]);
                              $pack_size=trim($data[13]);
                              $UOM3=trim($data[14]);
                              $conversion_factor_two=trim($data[15]);
                              $conversion_factor_two=str_replace(',','',$conversion_factor_two);
                              $TD=trim($data[16]);

                              //echo no_of_filter;
                              if(Session::get('iscodeneed')=='yes'){
                                  $sqlbranchcode=$CUTDB->table('branch_master')
                                                       ->select('branch_code')
                                                       ->where('dns_branch_code', '=' ,$branch_code_name)
                                                       ->first();
                              }
                              else
                              {
                                  $sqlbranchcode=$CUTDB->table('branch_master')
                                                       ->select('branch_code')
                                                       ->where('dns_branch_code', '=' ,$branch_code_name)
                                                       ->first();

                              }

                              $branch_code=$sqlbranchcode->branch_code;

                                  if(Session::get('nooffilter') > 1){
                                  //Product group code checking start
                                  $sqlprodgroupnamechk=$CUTDB->table('product_group_master')
                                                       ->where('product_group_name', '=' ,addslashes($product_group_code_name))
                                                       ->first();
                                  if(count($sqlprodgroupnamechk)<1){
                                    $productgroupcode=$CUTDB->table('product_group_master')->max('product_group_code');

                                    if($productgroupcode!='')
                                    {
                                      $productgroupcode1=substr($productgroupcode,2);
                                      $newproductgroupcode=$productgroupcode1+1;
                                      $max_product_group_code='BR'.$newproductgroupcode;
                                    }
                                    else {
                                      $max_product_group_code='BR1';
                                    }
                                    $downloadtime=date('Y-m-d H:i:s');
                                    $CUTDB->table('product_group_master')->insert(array(
                                        'product_group_code' => $max_product_group_code,
                                        'product_group_name' => addslashes($product_group_code_name),
                                        'vertical_value' => addslashes($vertical_value),
                                        'download_time' =>$downloadtime,
                                        'acedns'=>'Y'
                                    ));

                                      $product_group_code=$max_product_group_code;
                                  }
                                  else
                                  {

                                      $product_group_code=$sqlprodgroupnamechk->product_group_code;
                                      $vertical_value_db=$sqlprodgroupnamechk->vertical_value;
                                      if($vertical_value_db!=$vertical_value)
                                      {
                                          $downloadtime=date('Y-m-d H:i:s');
                                          $CUTDB->table('product_group_master')
                                          ->where('product_group_name', addslashes($product_group_code_name))
                                          ->limit(1)
                                          ->update(array(
                                              'vertical_value' => addslashes($vertical_value),
                                              'download_time' =>$downloadtime
                                           ));
                                      }
                                  }
                                  //Product group code checking end
                                }
                              if(Session::get('nooffilter') > 2){
                                  //Product sub group code checking start
                                  $sqlprodsubgroupnamechk=$CUTDB->table('product_sub_group_master')
                                                                               ->where('product_sub_group_name', '=' ,addslashes($product_sub_group_code_name))
                                                                               ->where('product_group_code', '=' ,$product_group_code)
                                                                               ->first();

                                  if(count($sqlprodsubgroupnamechk)<1){
                                    $productsubgroupcode=$CUTDB->table('product_sub_group_master')->max('product_sub_group_code');

                                    if($productsubgroupcode!='')
                                    {
                                      $productsubgroupcode1=substr($productsubgroupcode,2);
                                      $newproductsubgroupcode=$productsubgroupcode1+1;
                                      $productsubgroupcode='BF'.$newproductsubgroupcode;
                                    }
                                    else {
                                      $productsubgroupcode='BF1';
                                    }
                                      $max_product_sub_group_code=$productsubgroupcode;

                                      $downloadtime=date('Y-m-d H:i:s');
                                      $CUTDB->table('product_sub_group_master')->insert(array(
                                          'product_sub_group_code' => $max_product_sub_group_code,
                                          'product_group_code' => $product_group_code,
                                          'product_sub_group_name'=>$product_sub_group_code_name,
                                          'vertical_value' => addslashes($vertical_value),
                                          'download_time' =>$downloadtime
                                      ));

                                      $product_sub_group_code=$max_product_sub_group_code;
                                  }
                                  else
                                  {

                                      $product_sub_group_code=$sqlprodsubgroupnamechk->product_sub_group_code;
                                      $vertical_value_sub_group=$sqlprodsubgroupnamechk->vertical_value;
                                      if($vertical_value_sub_group!=$vertical_value)
                                      {
                                            $downloadtime=date('Y-m-d H:i:s');
                                            $CUTDB->table('product_sub_group_master')
                                            ->where('product_sub_group_name', addslashes($product_sub_group_code_name))
                                            ->where('product_group_code',$product_group_code)
                                            ->limit(1)
                                            ->update(array('vertical_value' => addslashes($vertical_value),
                                                'download_time' =>$downloadtime
                                             ));
                                      }
                                  }
                                  //Product sub group code checking end
                              }
                              if(Session::get('nooffilter') > 3){
                                  //Product brand code checking start
                                  $sqlprodbrandnamechk=$CUTDB->table('product_brand_master')
                                                                ->where('product_brand_name', '=' ,addslashes($product_brand_code_name))
                                                                ->where('product_sub_group_code', '=' ,$product_sub_group_code)
                                                                ->where('product_group_code', '=' ,$product_group_code)
                                                                ->first();
                                  if(count($sqlprodbrandnamechk)<1){
                                      $productbrandcode=$CUTDB->table('product_brand_master')->max('product_brand_code');

                                      if($productbrandcode!='')
                                      {
                                        $productbrandcode1=substr($productbrandcode,2);
                                        $newproductbrandcode=$productbrandcode1+1;
                                        $productbrandcode='BS'.$newproductbrandcode;
                                      }
                                      else {
                                        $productbrandcode='BS1';
                                      }
                                      $max_product_brand_code=$productbrandcode;
                                      $downloadtime=date('Y-m-d H:i:s');

                                      $CUTDB->table('product_brand_master')->insert(array(
                                          'product_brand_code' => $max_product_brand_code,
                                          'product_sub_group_code' => $product_sub_group_code,
                                          'product_group_code' => $product_group_code,
                                          'product_brand_name'=>$product_brand_code_name,
                                          'vertical_value' => $vertical_value,
                                          'download_time' =>$downloadtime
                                      ));

                                      $product_brand_code=$max_product_brand_code;
                                  }
                                  else
                                  {

                                      $product_brand_code=$sqlprodbrandnamechk->product_brand_code;
                                      $vertical_value_brand=$sqlprodbrandnamechk->vertical_value;
                                      if($vertical_value_brand!=$vertical_value)
                                      {
                                          $downloadtime=date('Y-m-d H:i:s');
                                          $CUTDB->table('product_brand_master')
                                          ->where('product_brand_name', addslashes($product_brand_code_name))
                                          ->where('product_sub_group_code', $product_sub_group_code)
                                          ->where('product_group_code', $product_group_code)
                                          ->limit(1)
                                          ->update(array('vertical_value' =>addslashes($vertical_value),
                                              'download_time' => $downloadtime
                                           ));
                                      }
                                  }
                                  //Product brand code checking end
                              }
                                 if(Session::get('isbranchwiseproduct')=='yes')
                                 {
                                      $sqlskunamechk=$CUTDB->table('product_master')
                                                                    ->where('prod_desc', '=' ,addslashes($product_brand_code_name))
                                                                    ->where('branch_code', '=' ,$product_sub_group_code)
                                                                    ->where('dns_prod_code', '=' ,$product_sub_group_code)
                                                                    ->where('product_group_code', '=' ,$product_group_code)
                                                                    ->first();
                                  }
                                  else
                                  {
                                    $sqlskunamechk=$CUTDB->table('product_master')
                                                                  ->where('prod_desc', '=' ,addslashes($product_brand_code_name))
                                                                  ->where('dns_prod_code', '=' ,$product_sub_group_code)
                                                                  ->where('product_group_code', '=' ,$product_group_code)
                                                                  ->first();
                                  }
                                  $updateflag=0;
                                  $insertflag=0;
                                  if(count($sqlskunamechk)<1)
                                  {

                                      $productcode=$CUTDB->table('product_master')->max('prod_code');
                                      if($productcode!='')
                                      {
                                        $max_prod_code=$productcode+1;
                                      }
                                      else {
                                        $max_prod_code='12001';
                                      }

                                      $downloadtime=date('Y-m-d H:i:s');
                                      $CUTDB->table('product_master')->insert(array(
                                          'branch_code' => $branch_code,
                                          'prod_code' => $max_prod_code,
                                          'dns_prod_code' => $dns_prod_code,
                                          'product_group_code' => $product_group_code,
                                          'product_sub_group_code'=>$product_sub_group_code,
                                          'product_brand_code'=>$product_brand_code,
                                          'prod_desc'=>$productdesc,
                                          'cl_stk'=>$cl_stk,
                                          'UOM1'=>$UOM1,
                                          'UOM2'=>$UOM2,
                                          'UOM3'=>$UOM3,
                                          'pack_size'=>$pack_size,
                                          'conversion_factor'=>$conversion,
                                          'conversion_factor_two'=>$conversion_factor_two,
                                          'TD'=>$TD,
                                          'acedns'=>$acedns,
                                          'black_list'=>$black_list,
                                          'vertical_value' => addslashes($vertical_value),
                                          'download_time' =>$downloadtime,
                                          'download_time_cl_stk'=>$downloadtime
                                      ));

                                      $insertflag=1;
                                      if(Session::get('isbranchwiseproduct')=='yes' || Session::get('isbranchwisemrp')=='yes')
                                      {
                                          $sqlbranch=$CUTDB->table('branch_master')
                                                            ->select('branch_code')
                                                            ->orderBy('branch_code', 'ASC')
                                                            ->get();



                                          foreach ($sqlbranch as $key => $value) {
                                              $branch_code_cl_stk=$value->branch_code;
                                              if(Session::get('isbranchwiseproduct')=='yes')
                                              {
                                                    $CUTDB->table('branch_product_wise_stock')->insert(array(
                                                      'branch_code'=>$branch_code_cl_stk,
                                                      'product_code'=>$max_prod_code,
                                                      'closing_stk'=>0,
                                                      'download_time'=>$downloadtime
                                                    ));


                                              }
                                              if(Session::get('isbranchwisemrp')=='yes')
                                              {
                                                  /*$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) AS max_mrp_code from mrp";
                                                  $rsmaxmrpcode=mysql_query($sqlmaxmrpcode);
                                                  $rowmaxmrpcode=mysql_fetch_array($rsmaxmrpcode);
                                                  $max_mrp_code=$rowmaxmrpcode['max_mrp_code'];

                                                  if($max_mrp_code=='')
                                                  {
                                                      $max_mrp_code='001';
                                                  }
                                                  else
                                                  {
                                                      $max_mrp_code++;
                                                  }
                                                  $max_mrp_code='z'.$max_mrp_code;*/

                                                  $mrpcode=$CUTDB->table('mrp')->max('mrp_code');

                                                  if($mrpcode!='')
                                                  {
                                                    $mrpcode++;
                                                  }
                                                  else {
                                                    $mrpcode='Z001';
                                                  }
                                                  $max_mrp_code=$mrpcode;

                                                  $CUTDB->table('mrp')->insert(array(
                                                      'branch_code' => '$branch_code_cl_stk',
                                                      'product_code' => $max_prod_code,
                                                      'mrp_code'=>$max_mrp_code,
                                                      'mrp' => '0',
                                                      'L1' => '0',
                                                      'vertical_value'=>addslashes($vertical_value),
                                                      'UOM'=>'',
                                                      'download_time' =>$downloadtime
                                                  ));

                                                }
                                          }
                                      }
                                      if((Session::get('ismrp')=='yes' || (Session::get('issalerate')=='yes' && Session::get('issalerateinputdropdown')=='dropdown')) && Session::get('isbranchwisemrp')=='no')
                                      {
                                          /*$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) AS max_mrp_code from mrp";
                                          $rsmaxmrpcode=mysql_query($sqlmaxmrpcode);
                                          $rowmaxmrpcode=mysql_fetch_array($rsmaxmrpcode);
                                          $max_mrp_code=$rowmaxmrpcode['max_mrp_code'];

                                          if($max_mrp_code=='')
                                          {
                                              $max_mrp_code='001';
                                          }
                                          else
                                          {
                                              $max_mrp_code++;
                                          }*/
                                          $mrpcode=$CUTDB->table('mrp')->max('mrp_code');

                                          if($mrpcode!='')
                                          {
                                            $mrpcode++;
                                          }
                                          else {
                                            $mrpcode='Z001';
                                          }
                                          $max_mrp_code=$mrpcode;

                                          $CUTDB->table('mrp')->insert(array(
                                              'branch_code' => '',
                                              'product_code' => $max_prod_code,
                                              'mrp_code'=>$max_mrp_code,
                                              'destination_code'=>$destinationcode,
                                              'order_type'=>$ordertype,
                                              'mrp' => '0',
                                              'L1' => '0',
                                              'vertical_value'=>addslashes($vertical_value),
                                              'UOM'=>'',
                                              'download_time' =>$downloadtime
                                          ));

                                      }
                                  }
                                  else
                                  {
                                      $cl_stk_db=$sqlskunamechk->cl_stk;
                                      $branch_code_db=$sqlskunamechk->branch_code;
                                      $acedns_db=$sqlskunamechk->acedns;
                                      $black_list_db=$sqlskunamechk->black_list;
                                      $prod_code_db=$sqlskunamechk->prod_code;
                                      $product_group_code_db=$sqlskunamechk->product_group_code;
                                      $product_sub_group_code_db=$sqlskunamechk->product_sub_group_code;
                                      $product_brand_code_db=$sqlskunamechk->product_brand_code;
                                      $UOM1_db=$sqlskunamechk->UOM1;
                                      $UOM2_db=$sqlskunamechk->UOM2;
                                      $conversion_db=$sqlskunamechk->conversion_factor;
                                      $vertical_value_db=$sqlskunamechk->vertical_value;

                                      if(($cl_stk_db==$cl_stk) && ($acedns_db!=$acedns || $black_list_db!=$black_list
                                          || $product_group_code_db!=$product_group_code || $product_sub_group_code_db!=$product_sub_group_code
                                          || $product_brand_code_db!=$product_brand_code || $branch_code_db!=$branch_code || $conversion_db!=$conversion
                                          || $vertical_value_db!=$vertical_value || $conversion_factor_two_db!=$conversion_factor_two || $UOM3_db!=$UOM3 || $pack_size_db!=$pack_size || $TD_db!=$TD))
                                      {
                                            $downloadtime=date('Y-m-d H:i:s');
                                            $CUTDB->table('product_master')
                                            ->where('prod_code', $prod_code_db)
                                            ->limit(1)
                                            ->update(array('branch_code'=>$branch_code,
                                            'prod_desc' => addslashes($prod_desc),
                                            'product_group_code' => $product_group_code,
                                            'product_sub_group_code'=>$product_sub_group_code,
                                            'product_brand_code'=>$product_brand_code,
                                            'cl_stk'=>$prostock,
                                            'UOM1'=>$UOM1,
                                            'UOM2'=>$UOM2,
                                            'UOM3'=>$UOM3,
                                            'conversion_factor'=>$conversion,
                                            'conversion_factor_two'=>$conversion_factor_two,
                                            'TD'=>$TD,
                                            'acedns'=>$acedns,
                                            'black_list'=>$black_list,
                                            'download_time'=>$downloadtime,
                                            'vertical_value' => addslashes($vertical_value)
                                             ));
                                          $updateflag=1;
                                      }
                                      else if($cl_stk_db!=$cl_stk)
                                      {
                                          $downloadtime=date('Y-m-d H:i:s');
                                          $CUTDB->table('product_master')
                                          ->where('prod_code', $prod_code_db)
                                          ->limit(1)
                                          ->update(array(
                                             'cl_stk'=>$cl_stk,
                                             'download_time_cl_stk'=>$downloadtime
                                          ));

                                          $updateflag=1;
                                      }
                                  }
                                  if(Session::get('isbranchwisemrp')=='yes' && ($updateflag==1 || $insertflag==1))//Start For emp data download log
                                  {
                                      if(!in_array($branch_code,$branch_code_array))
                                      {
                                          array_push($branch_code_array,$branch_code);
                                          $sqlbranchwiseemp=$CUTDB->SELECT("SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$branch_code."', branch_code)");
                                          foreach ($sqlbranchwiseemp as $key => $value) {
                                            $emp_code_branchwise=$value->emp_code;
                                          }

                                      }
                                  }//End For emp data download log

                              //For TT
                              //array_push($duplicate_product,$dns_prod_code." \t".$prod_desc." \t".$product_group_code." \t".$product_sub_group_code);

                          }
                  $rec_count++;
                  }
                  if(Session::get('isbranchwisemrp')=='no')//Start For emp data download log with no branch tagging
                  {
                      $emp_code='';

                  }//End For emp data download log with no branch tagging

                  //Product group code checking start
                      if(Session::get('nooffilter')==2 || Session::get('nooffilter')==3){
                          $sqlgroupcodeproduct=$CUTDB->SELECT("SELECT product_group_code FROM product_master WHERE product_group_code NOT IN
                                              (SELECT product_group_code FROM product_group_master) GROUP BY product_group_code");

                          if(count($sqlgroupcodeproduct)>0)
                          {
                              $groupcodeproduct='';

                              foreach ($sqlgroupcodeproduct as $key => $value) {

                                  $groupcodeproduct=$groupcodeproduct.$value->product_group_code.',';
                              }
                              $groupcodeproduct=substr($groupcodeproduct,0,-1);
                              $errorgroupcodeproduct=$groupcodeproduct.' exists in Sku master but not exists in Brand Master.';
                              array_push($error_array,$errorgroupcodeproduct);
                          }
                      }
                  //Product group code checking end
                  //Product sub group code checking start
                      if(Session::get('nooffilter')==3){
                      $sqlsubgroupcodeproduct=$CUTDB->SELECT("SELECT product_sub_group_code FROM product_master WHERE product_sub_group_code NOT IN
                      (SELECT product_sub_group_code FROM product_sub_group_master) GROUP BY product_sub_group_code");
                      if(count($sqlsubgroupcodeproduct)>0)
                      {
                          $subgroupcodeproduct='';
                          foreach ($sqlsubgroupcodeproduct as $key => $value) {

                              $subgroupcodeproduct=$subgroupcodeproduct.$value->product_sub_group_code.',';
                          }
                          $subgroupcodeproduct=substr($subgroupcodeproduct,0,-1);
                          $errorsubgroupcodeproduct=$subgroupcodeproduct.' exists in Sku master but not exists in Brand Form Master.';
                          array_push($error_array,$errorsubgroupcodeproduct);
                      }
                  }
                  $successval=1;
        }
    }

}
