<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Http\Requests;

use Session;
use App\Helpers\Vartical;
use App\Helpers\Commonfunctions;


class VarticalController extends Controller
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
        $varticals=Vartical::getAllVarticalList($sdbname);
        return view('vartical.index',compact('varticals'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $sdbname =$this->databasename();
        $branch_list=Vartical::getBrnchlist($sdbname);
        return view('vartical.create',compact('branch_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
      $dbname =$this->databasename();
      $CUTDB = Vartical::dydb($dbname);

      $varticalname=$request->input('vartical_name');
      $downloadtime=date('Y-m-d H:i:s');
      $varticalcode=$CUTDB->table('vartical_master')->max('vartical_code');
      if($varticalcode!='')
      {
        $varticalcode++;
      }
      else {
        $varticalcode='V0001';
      }

      $branch='';
      $empmultipleValues = $request->input('branch');
      if(count($empmultipleValues))
      {
        foreach($empmultipleValues as $value)
        {
            $branch.=$value.',';
        }
        $branch=substr($branch,0,-1);
      }

      $CUTDB->table('vartical_master')->insert(array(
          'vartical_code' => $varticalcode,
          'vartical_name' => $varticalname,
          'branch_code'=>$branch,
          'acedns' => 'Y',
          'download_time' =>$downloadtime
      ));
      return redirect('/vartical')->with('message', 'Success!');

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
        $tablename="vartical_master";
        $fieldname="vartical_code";
        $parameter=$id;
        $varticaldetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
        return view('vartical.view',compact('varticaldetails'));
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
        $tablename="vartical_master";
        $fieldname="vartical_code";
        $parameter=$id;
        $varticaldetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
        $branch_list=Vartical::getBrnchlist($sdbname);
        return view('vartical.edit',compact('varticaldetails','branch_list'));
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
        $CUTDB = Vartical::dydb($dbname);

        $varticalname=Input::get('vartical_name');

        $branch='';
        $empmultipleValues = Input::get('branch');
        if(count($empmultipleValues))
        {
          foreach($empmultipleValues as $value)
          {
              $branch.=$value.',';
          }
          $branch=substr($branch,0,-1);
        }

        $CUTDB->table('vartical_master')
        ->where('vartical_code', $id)
        ->limit(1)
        ->update(array('vartical_name' => $varticalname,
              'branch_code'=>$branch
         ));

        return redirect('/vartical')->with('message', 'Success!');

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
