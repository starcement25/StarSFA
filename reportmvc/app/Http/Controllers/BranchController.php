<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;
use App\Helpers\Branch;
use App\Helpers\Commonfunctions;

use Session;


class BranchController extends Controller
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
      $branchlistings=Branch::getAllBracnchList($sdbname);
      return view('branch.index',compact('branchlistings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
            return view('branch.addedit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {

      $dbname =$this->databasename();
      $CUTDB = Branch::dydb($dbname);

      $dnsbranchcode=(($request->input('brach_master_code'))?$request->input('brach_master_code'):'');
      $branchname=(($request->input('brach_master_name'))?$request->input('brach_master_name'):'');
      $branchlocation=(($request->input('brach_location'))?$request->input('brach_location'):'');
      $branchstate=(($request->input('brach_state'))?$request->input('brach_state'):'');
      $branchemailid=(($request->input('brach_emailid'))?$request->input('brach_emailid'):'');
      $branchaccountemailid=(($request->input('brach_account_emailid'))?$request->input('brach_account_emailid'):'');
      $branchalternetiveemailid=(($request->input('brach_alternative_emailid'))?$request->input('brach_alternative_emailid'):'');
      $plantname=(($request->input('plamt_name'))?$request->input('plamt_name'):'');
      $costcenter=(($request->input('brach_costcenter'))?$request->input('brach_costcenter'):'');
      $acedns='Y';
      $compcode=substr($dbname,7);
      $downloadtime=date('Y-m-d H:i:s');

      $branchcode=$CUTDB->table('branch_master')->max('branch_code');
      $branchcode++;


      if(Session::get('iscodeneed')=='yes')
      {

         $rules1[] = array(
            'brach_master_code' => 'required',
         );
      }

      $rules1[] = array(
        'brach_master_name' => 'required',
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
         return Redirect::to('branch/create')->withErrors($validator);
    }

        else{
          $CUTDB->table('branch_master')->insert(array(
              'comp_code' => $compcode,
              'branch_code' => $branchcode,
              'dns_branch_code' => $dnsbranchcode,
              'branch_name' => $branchname,
              'branch_location' => $branchlocation,
              'branch_state' => $branchstate,
              'branch_email_id' => $branchemailid,
              'branch_accounts_email_id' => $branchaccountemailid,
              'alternative_email_id' => $branchalternetiveemailid,
              'plant_name' => $plantname,
              'costcenter' => $costcenter,
              'acedns' => $acedns,
              'download_time' =>$downloadtime
          ));
          return redirect('/branch')->with('message', 'Success!');
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
      $tablename="branch_master";
      $fieldname="branch_code";
      $parameter=$id;
      $branchdetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
      return view('branch.view',compact('branchdetails'));
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
      $tablename="branch_master";
      $fieldname="branch_code";
      $parameter=$id;
      $branchdetails=Commonfunctions::getAllDetailsById($sdbname,$tablename,$fieldname,$parameter);
      return view('branch.edit',compact('branchdetails'));

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
      $CUTDB = Branch::dydb($dbname);

      if(Session::get('iscodeneed')=='yes')
      {

         $rules1[] = array(
            'brach_master_code' => 'required',
         );
      }

      $rules1[] = array(
        'brach_master_name' => 'required',
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
        return Redirect::to('branch/'.$id.'/edit')->withErrors($validator);
      }
      else{

        $dnsbranchcode=Input::get('brach_master_code');
        $branchname=Input::get('brach_master_name');
        $branchlocation=Input::get('brach_location');
        $branchstate=Input::get('brach_state');
        $branchemailid=Input::get('brach_emailid');
        $branchaccountemailid=Input::get('brach_account_emailid');
        $branchalternetiveemailid=Input::get('brach_alternative_emailid');
        $plantname=Input::get('plamt_name');
        $costcenter=Input::get('brach_costcenter');

        $CUTDB->table('branch_master')
        ->where('branch_code', $id)
        ->limit(1)
        ->update(array('dns_branch_code' => $dnsbranchcode,
        'branch_name' => $branchname,
        'branch_location' => $branchlocation,
        'branch_state' => $branchstate,
        'branch_email_id' => $branchemailid,
        'branch_accounts_email_id' => $branchaccountemailid,
        'alternative_email_id' => $branchalternetiveemailid,
        'plant_name' => $plantname,
        'costcenter' => $costcenter));
        return redirect('/branch')->with('message', 'Success!');
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

    public function branchUploadFile(Request $request)
    {

        $dbname =$this->databasename();
        $CUTDB = Branch::dydb($dbname);

        $file = $request->file('brach_csv_file');

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
    		    $lines = file($destinationPath.'/'.$name);
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
                		$dns_branch_code=trim($data[0]);
                		$branch_name=trim($data[1]);
                		$branch_location=trim($data[2]);
                		$comp_code=trim($data[3]);
                		$branch_email_id=trim($data[5]);
                		$branch_accounts_email_id=trim($data[6]);
                		$alternative_email_id=trim($data[7]);
                    $countbranchnamechk =$CUTDB->table('branch_master')
                                    ->where('branch_name', '=' ,addslashes($branch_name))
                                    ->where('branch_location', '=' ,$branch_location)
                                    ->first();
                    $csv_row_count=$rec_count+1;
                    if(count($countbranchnamechk)<1)
                    {
                          $max_branch_code=$CUTDB->table('branch_master')->max('branch_code');
                          if($max_branch_code=='')
                					{
                						$max_branch_code='B0001';
                					}
                					else
                					{
                						$max_branch_code++;
                					}
                          $downloadtime=date('Y-m-d H:i:s');
                          $CUTDB->table('branch_master')->insert(array(
                              'comp_code' => $comp_code,
                              'branch_code' => $max_branch_code,
                              'dns_branch_code' => $dns_branch_code,
                              'branch_name' => $branch_name,
                              'branch_location' => $branch_location,
                              'branch_email_id' => $branch_email_id,
                              'branch_accounts_email_id' => $branch_accounts_email_id,
                              'alternative_email_id' => $alternative_email_id,
                              'download_time' =>$downloadtime
                          ));
                    }
                    else{
                      $CUTDB->table('branch_master')
                      ->where('branch_code', $countbranchnamechk->branch_code)
                      ->limit(1)
                      ->update(array('dns_branch_code' => $dns_branch_code,
                      'branch_name' => $branch_name,
                      'branch_location' => $branch_location,
                      'branch_email_id' => $branch_email_id,
                      'branch_accounts_email_id' => $branch_accounts_email_id,
                      'alternative_email_id' => $alternative_email_id
                      ));
                    }
                }
                $rec_count++;
            }
             return redirect('/branch');
        }
    }


}
