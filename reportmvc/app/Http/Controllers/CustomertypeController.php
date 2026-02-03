<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;

use Session;
use App\Helpers\Customer;
use App\Helpers\Commonfunctions;


class CustomertypeController extends Controller
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
        $customertypelists=Customer::getAllCustomerTypeList($sdbname);
        return view('customertype.index',compact('customertypelists'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $sdbname =$this->databasename();
        $custtypelists=Customer::getCustomerTypelist($sdbname);
        return view('customertype.create',compact('custtypelists'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
      $dbname =$this->databasename();
      $CUTDB = Customer::dydb($dbname);

      $prefix=$request->input('prifix');
      $name=$request->input('name');
      $depand=$request->input('depanden_to');
      $downloadtime=date('Y-m-d H:i:s');
      $code=$CUTDB->table('customertype_master')->max('id');

      if($code!='')
      {
        $code1=substr($code,2);
        $newcode=$code1+1;
        $newcode1='CT'.$newcode;
      }
      else {
        $newcode1='CT1';
      }
      //$newcode1;exit;
      $rules= array(
        'prifix' => 'required',
        'name' => 'required',
      );
      $validator = Validator::make(Input::all(), $rules);
      if ($validator->fails())
      {
           return Redirect::to('customertype/create')->withErrors($validator);
      }
      else{
          $CUTDB->table('customertype_master')->insert(array(
              'id' => $newcode1,
              'prefix' => $prefix,
              'name'=>$name,
              'depanden_to' => $depand,
              'acedns'=>'Y',
              'download_time' =>$downloadtime
          ));
          return redirect('/customertype')->with('message', 'Success!');
      }
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
        $tablename="customertype_master";
        $fieldname="id";
        $parameter=$id;
        $custtypedetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
        return view('customertype.view',compact('custtypedetails'));
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
      $tablename="customertype_master";
      $fieldname="id";
      $parameter=$id;
      $custtypelists=Customer::getCustomerTypelist($sdbname,$id);
      $custtypedetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
      return view('customertype.edit',compact('custtypelists','custtypedetails'));
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
      $CUTDB = Customer::dydb($dbname);

      $prefix=Input::get('prifix');
      $name=Input::get('name');
      $depand=Input::get('depanden_to');

      $rules= array(
        'prifix' => 'required',
        'name' => 'required',
      );
      $validator = Validator::make(Input::all(), $rules);
      if ($validator->fails())
      {
           return Redirect::to('customertype/'.$id.'/edit')->withErrors($validator);
      }
      else{
        $CUTDB->table('customertype_master')
        ->where('id', $id)
        ->limit(1)
        ->update(array(
            'prefix' => $prefix,
            'name'=>$name,
            'depanden_to' => $depand
         ));

        return redirect('/customertype')->with('message', 'Success!');
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

}
