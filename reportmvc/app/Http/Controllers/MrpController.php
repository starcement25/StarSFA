<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Http\Requests;

use Session;
use App\Helpers\Product;
use App\Helpers\Commonfunctions;


class MrpController extends Controller
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
        $mrplists=Product::getAllMrpList($sdbname);
        return view('mrp.index',compact('mrplists'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $sdbname =$this->databasename();
        $product_lists=Product::getProductlist($sdbname);
        $branch_lists=Product::getBranchlist($sdbname);
        $destinationlists=Product::getDestinationlist($sdbname);
        $custtypelists=Product::getCusttypelist($sdbname);
        return view('mrp.create',compact('product_lists','branch_lists','destinationlists','custtypelists'));
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

      $branchcode=(($request->input('brnach_code'))?$request->input('brnach_code'):'');
      $productcode=(($request->input('product_code'))?$request->input('product_code'):'');
      $destinationcode=(($request->input('destination_code'))?$request->input('destination_code'):'');
      $ordertype=(($request->input('order_type'))?$request->input('order_type'):'');
      $mrp=(($request->input('mrp'))?$request->input('mrp'):'');
      $salerate=(($request->input('price1'))?$request->input('price1'):'');
      $distributor_rate=(($request->input('price2'))?$request->input('price2'):'');
      $ws_rate=(($request->input('price3'))?$request->input('price3'):'');
      $ss_rate=(($request->input('price4'))?$request->input('price4'):'');
      $depot_rate=(($request->input('price5'))?$request->input('price5'):'');
      $downloadtime=date('Y-m-d H:i:s');
      $mrpcode=$CUTDB->table('mrp')->max('mrp_code');

      if($mrpcode!='')
      {
        $mrpcode1=substr($mrpcode,1);
        $newmrpcode=$mrpcode1+1;
        $mrpcode='Z'.$newmrpcode;
      }
      else {
        $mrpcode='Z1';
      }

      $CUTDB->table('mrp')->insert(array(
          'branch_code' => $branchcode,
          'product_code' => $productcode,
          'mrp_code'=>$mrpcode,
          'destination_code'=>$destinationcode,
          'order_type'=>$ordertype,
          'mrp' => $mrp,
          'L1' => $salerate,
          'L2'=>$distributor_rate,
          'L3'=>$ws_rate,
          'L4'=>$ss_rate,
          'L5'=>$depot_rate,
          'download_time' =>$downloadtime
      ));
      return redirect('/mrp')->with('message', 'Success!');

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
        $tablename="mrp";
        $fieldname="mrp_code";
        $parameter=$id;
        $mrpdetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
        $custtypelists=Product::getCusttypelist($sdbname);
        return view('mrp.view',compact('mrpdetails','custtypelists'));
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
      $tablename="mrp";
      $fieldname="mrp_code";
      $parameter=$id;
      $product_lists=Product::getProductlist($sdbname);
      $branch_lists=Product::getBranchlist($sdbname);
      $destinationlists=Product::getDestinationlist($sdbname);
      $mrpdetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
      $custtypelists=Product::getCusttypelist($sdbname);
      return view('mrp.edit',compact('product_lists','branch_lists','mrpdetails','destinationlists','custtypelists'));
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

      $branchcode=((Input::get('brnach_code'))?Input::get('brnach_code'):'');
      $productcode=((Input::get('product_code'))?Input::get('product_code'):'');
      $dnsmrpcode=((Input::get('dns_mrp_code'))?Input::get('dns_mrp_code'):'');
      $destinationcode=((Input::get('destination_code'))?Input::get('destination_code'):'');
      $ordertype=((Input::get('order_type'))?Input::get('order_type'):'');
      $mrp=((Input::get('mrp'))?Input::get('mrp'):'');
      $salerate=((Input::get('price1'))?Input::get('price1'):'');
      $distributor_rate=((Input::get('price2'))?Input::get('price2'):'');
      $ws_rate=((Input::get('price3'))?Input::get('price3'):'');
      $ss_rate=((Input::get('price4'))?Input::get('price4'):'');
      $depot_rate=((Input::get('price5'))?Input::get('price5'):'');


        $CUTDB->table('mrp')
        ->where('mrp_code', $id)
        ->limit(1)
        ->update(array('branch_code' => $branchcode,
            'product_code' => $productcode,
            'destination_code'=>$destinationcode,
            'order_type'=>$ordertype,
            'mrp' => $mrp,
            'L1' => $salerate,
            'L2'=>$distributor_rate,
            'L3'=>$ws_rate,
            'L4'=>$ss_rate,
            'L5'=>$depot_rate
         ));

        return redirect('/mrp')->with('message', 'Success!');

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
