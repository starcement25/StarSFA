<?php
namespace App\Http\Controllers\Api\v1;
use Illuminate\Http\Request;
use Mail;
use Crypt;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MailController extends Controller {
   public function basic_email(){
      $data = array('nick_name'=>"Virat Gandhi",'versionCode'=>"12",'release_date'=>"2017-10-02",'emp_name'=>"Test",'emp_code'=>"Test",'update_date'=>"201-10-26",'update_time'=>"12:30:51");

      Mail::send(['html'=>'mail'], $data, function($message) {
         $message->to('sumans@coral.in', 'Tutorials Point')->subject
            ('Laravel Basic Testing Mail');
         $message->from('acedns@coral.in','Virat Gandhi');
      });
      echo "Basic Email Sent. Check your inbox.";
   }
   public function enccheck(){
     $xm="";
     $cnt='test';
     $xm=Crypt::encrypt($cnt);
     return $xm;
   }

}

?>
