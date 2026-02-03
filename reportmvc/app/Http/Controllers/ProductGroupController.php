<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Http\Requests;

use Session;
use App\Helpers\Product;
use App\Helpers\Commonfunctions;


class ProductGroupController extends Controller
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
        $productgroups=Product::getAllProductGruopList($sdbname);
        return view('productgroup.index',compact('productgroups'));
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
        return view('productgroup.create',compact('vartical_list'));
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

      $dnsproductgroupcode=$request->input('productgroup_code');
      $productgroupname=$request->input('productgroup_name');
      $downloadtime=date('Y-m-d H:i:s');
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

      $emp_vartical='';
      $empmultiplevarticalValues = $request->input('vartical');
      foreach($empmultiplevarticalValues as $value)
      {
          $emp_vartical.=$value.',';
      }
      $emp_vartical=substr($emp_vartical,0,-1);


      $CUTDB->table('product_group_master')->insert(array(
          'product_group_code' => $productgroupcode,
          'dns_product_group_code' => $dnsproductgroupcode,
          'product_group_name' => $productgroupname,
          'vertical_value' => $emp_vartical,
          'download_time' =>$downloadtime,
          'acedns'=>'Y'
      ));
      return redirect('/productgroup')->with('message', 'Success!');

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
        $tablename="product_group_master";
        $fieldname="product_group_code";
        $parameter=$id;
        $productgroupdetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
        return view('productgroup.view',compact('productgroupdetails'));
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
        $tablename="product_group_master";
        $fieldname="product_group_code";
        $parameter=$id;
        $productdetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
        $vartical_list=Product::getVarticallist($sdbname);
        return view('productgroup.edit',compact('productdetails','vartical_list'));
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

      $dnsproductgroupcode=Input::get('productgroup_code');
      $productgroupname=Input::get('productgroup_name');


      $emp_vartical='';
      $empmultiplevarticalValues = Input::get('vartical');
      foreach($empmultiplevarticalValues as $value)
      {
          $emp_vartical.=$value.',';
      }
      $emp_vartical=substr($emp_vartical,0,-1);


        $CUTDB->table('product_group_master')
        ->where('product_group_code', $id)
        ->limit(1)
        ->update(array('dns_product_group_code' => $dnsproductgroupcode,
            'product_group_name' => $productgroupname,
            'vertical_value' => $emp_vartical
         ));

        return redirect('/productgroup')->with('message', 'Success!');

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
