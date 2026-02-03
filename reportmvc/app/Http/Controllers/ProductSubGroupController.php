<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Http\Requests;

use Session;
use App\Helpers\Product;
use App\Helpers\Commonfunctions;


class ProductSubGroupController extends Controller
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
        $productsubgroups=Product::getAllProductSubGruopList($sdbname);
        return view('productsubgroup.index',compact('productsubgroups'));
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
        $productgroup_lists=Product::getProductGrouplist($sdbname);
        return view('productsubgroup.create',compact('vartical_list','productgroup_lists'));
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

      $dnsproductsubgroupcode=$request->input('productsubgroup_code');
      $productgroup=$request->input('productgroup_lists');
      $productsubgroupname=$request->input('productsubgroup_name');
      $downloadtime=date('Y-m-d H:i:s');
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

      $emp_vartical='';
      $empmultiplevarticalValues = $request->input('vartical');
      foreach($empmultiplevarticalValues as $value)
      {
          $emp_vartical.=$value.',';
      }
      $emp_vartical=substr($emp_vartical,0,-1);


      $CUTDB->table('product_sub_group_master')->insert(array(
          'product_sub_group_code' => $productsubgroupcode,
          'dns_product_sub_group_code' => $dnsproductsubgroupcode,
          'product_group_code' => $productgroup,
          'product_sub_group_name'=>$productsubgroupname,
          'vertical_value' => $emp_vartical,
          'download_time' =>$downloadtime
      ));
      return redirect('/productsubgroup')->with('message', 'Success!');

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
        $tablename="product_sub_group_master";
        $fieldname="product_sub_group_code";
        $parameter=$id;
        $productsubgroupdetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
        return view('productsubgroup.view',compact('productsubgroupdetails'));
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
      $tablename="product_sub_group_master";
      $fieldname="product_sub_group_code";
      $parameter=$id;
      $productsubgroupdetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
      $vartical_list=Product::getVarticallist($sdbname);
      $productgroup_lists=Product::getProductGrouplist($sdbname);
      return view('productsubgroup.edit',compact('productsubgroupdetails','vartical_list','productgroup_lists'));
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

      $dnsproductsubgroupcode=Input::get('productsubgroup_code');
      $productgroup=Input::get('productgroup_lists');
      $productsubgroupname=Input::get('productsubgroup_name');


      $emp_vartical='';
      $empmultiplevarticalValues = Input::get('vartical');
      foreach($empmultiplevarticalValues as $value)
      {
          $emp_vartical.=$value.',';
      }
      $emp_vartical=substr($emp_vartical,0,-1);


        $CUTDB->table('product_sub_group_master')
        ->where('product_sub_group_code', $id)
        ->limit(1)
        ->update(array('dns_product_sub_group_code' => $dnsproductsubgroupcode,
            'product_group_code' => $productgroup,
            'product_sub_group_name'=>$productsubgroupname,
            'vertical_value' => $emp_vartical
         ));

        return redirect('/productsubgroup')->with('message', 'Success!');

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

}
