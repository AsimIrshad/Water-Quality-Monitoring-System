<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\user;
use App\station;
use App\admin_has_relation;
use App\Http\Requests\StoreRegistration;
use App\User_has_message;
class adminController extends Controller
{
    //
    public function show_Registration_page(){
    	
        $stations=station::where('admin_id',0)->get();
        if(sizeof($stations)>0){
                $user_type="Admin";
                return view('User.Registration',compact('stations','user_type'));
                }
        else{
            $error_message_body="There are no Station Available for Registration. Contact  Department";
            $button=" ";
            return view('User.Layout.error_message',compact('error_message_body','button'));
        }
    }
    public function Registration(StoreRegistration $request){
            

                $filename=$request->file->getClientOriginalName();
                $path= $request->file->storeAs('public/upload/image',$filename);

                	$user=new \App\user;
                    $user->fname =request('first_name');
                    $user->lname=request('last_name');
                    $user->mobile_no=request('mobile_no');
                    $user->email=request('email');

                    $password=hash('ripemd128',request('password'));
                    $user->password=$password;
                    $user->user_name=request('user_name');
                    $user->object="App\admin";
                    $user->status=0;
                    
                    $user->save();

                    $admin=new \App\admin;
                    $admin->cnic =request('cnic');
                    $admin->employee_no =request('employee_no');
                    $admin->department_name =request('department_name');
                    $admin->image=$path;
                    $admin->super_admin_id =1;
                    $user->admin()->save($admin);


                    $admin_request=new \App\admin_request;
                    $admin_request->station_id=request('station_id');

                    $admin->admin_request()->save($admin_request);

                    echo "<script type='text/javascript'>alert('Registration has been sent successfully!')</script>";
                                   	
                return redirect('/');
                
                
            }


       public function ShowUploadFile(Request $request){

        if($request->session()->has('user_id')){
                
                $user_id= $request->session()->get('user_id');
                
            $user_message=new User_has_message;
            $user=new user;
            $user=$user->get_User($user_id);
            $count=$user_message->get_inbox_count($user_id);
            if($count<=0){
                $message="null";
                $Sender_user="null";        
            }
            else{
                   $message=$user_message->get_message($user_id);
                   $Sender_user=$user->get_User($message->user_id);
            }
            return view('Admin.functions.upload_file',compact('user','message','count','Sender_user'));
                }
        else {
            return redirect("/");
            }
       }
       

       
        
}   