<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Http\Requests;

use Session;
use App\Helpers\Product;
use App\Helpers\Commonfunctions;


class ProductBrandController extends Controller
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
        $productbands=Product::getAllProductBrandList($sdbname);
        return view('productbrand.index',compact('productbands'));
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
        $productsubgroup_lists=Product::getProductSubGrouplist($sdbname);
        return view('productbrand.create',compact('vartical_list','productgroup_lists','productsubgroup_lists'));
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

      $dnsproductbrandcode=$request->input('productbrand_code');
      $productgroup=$request->input('productgroup_lists');
      $productsubgroup=$request->input('productsubgroup_lists');
      $productbrand=$request->input('productbrand_name');
      $downloadtime=date('Y-m-d H:i:s');
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

      $emp_vartical='';
      $empmultiplevarticalValues = $request->input('vartical');
      foreach($empmultiplevarticalValues as $value)
      {
          $emp_vartical.=$value.',';
      }
      $emp_vartical=substr($emp_vartical,0,-1);


      $CUTDB->table('product_brand_master')->insert(array(
          'product_brand_code' => $productbrandcode,
          'dns_product_brand_code' => $dnsproductbrandcode,
          'product_group_code' => $productgroup,
          'product_sub_group_code'=>$productsubgroup,
          'product_brand_name'=>$productbrand,
          'vertical_value' => $emp_vartical,
          'download_time' =>$downloadtime
      ));
      return redirect('/productbrand')->with('message', 'Success!');

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
        $tablename="product_brand_master";
        $fieldname="product_brand_code";
        $parameter=$id;
        $productbranddetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
        return view('productbrand.view',compact('productbranddetails'));
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
      $tablename="product_brand_master";
      $fieldname="product_brand_code";
      $parameter=$id;
      $productbranddetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
      $vartical_list=Product::getVarticallist($sdbname);
      $productgroup_lists=Product::getProductGrouplist($sdbname);
      $productsubgroup_lists=Product::getProductSubGrouplist($sdbname);
      return view('productbrand.edit',compact('productbranddetails','vartical_list','productgroup_lists','productsubgroup_lists'));
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



      $dnsproductbrandcode=Input::get('productbrand_code');
      $productgroup=Input::get('productgroup_lists');
      $productsubgroup=Input::get('productsubgroup_lists');
      $productbrand=Input::get('productbrand_name');


      $emp_vartical='';
      $empmultiplevarticalValues = Input::get('vartical');
      foreach($empmultiplevarticalValues as $value)
      {
          $emp_vartical.=$value.',';
      }
      $emp_vartical=substr($emp_vartical,0,-1);


        $CUTDB->table('product_brand_master')
        ->where('product_brand_code', $id)
        ->limit(1)
        ->update(array('dns_product_brand_code' => $dnsproductbrandcode,
            'product_group_code' => $productgroup,
            'product_sub_group_code'=>$productsubgroup,
            'product_brand_name'=>$productbrand,
            'vertical_value' => $emp_vartical
         ));

        return redirect('/productbrand')->with('message', 'Success!');

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
