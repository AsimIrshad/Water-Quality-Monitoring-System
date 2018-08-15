<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\user;
use App\Station;
use App\admin;
use App\super_admin;
use App\User_has_message;
use App\Http\Requests\Store_Super_Registration;
use App\Http\Requests\admin_request_list;
class SadminController extends Controller
{
    //
    public function show_Registration_page(){
    	return view('User.Registration',[
    		'user_type' => 'Super Admin']);
    }
    
    public function Registration(Store_Super_Registration $request){
            
            
                $filename=$request->file->getClientOriginalName();
                 $path=$request->file->storeAs('public/upload/image',$filename);


            	$user=new \App\user;
                $user->fname =request('first_name');
                $user->lname=request('last_name');
                $user->mobile_no=request('mobile_no');
                $user->email=request('email');
                $password=hash('ripemd128',request('password'));
                    $user->password=$password;
                $user->user_name=request('user_name');
                $user->status=0;
                $user->object="App\super_admin";
                $user->save();

                $admin=new \App\super_admin;
                $admin->cnic =request('cnic');
                $admin->employee_no =request('employee_no');
                $admin->department_name =request('department_name');
                $admin->image=$path;

                $user->super_admin()->save($admin);

    	       
               echo "<script type='text/javascript'>alert('Registration successfully!')</script>"; 	
           
             return redirect('/');
             
    }
    public function ShowAdminList(Request $request) {
        if($request->session()->has('user_id')){

            $user_id= $request->session()->get('user_id');
            
            $users = user::where('users.status',1)->where('users.object','App\admin')->orderBy('fname', 'dec')->get();

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

           return view('SAdmin.functions.show_admin_list',compact('users','user','message','count','Sender_user'));
        }
                else {
                   return redirect("/"); 
                }
    }
    
    
    public function Show_registration_list(Request $request){
        if($request->session()->has('user_id')){
        $user_id= $request->session()->get('user_id');
         

        $users = user::where('users.status','=','0')->where('users.object','App\admin')->orderBy('fname', 'ace')->get();
       
     
       $stations=station::where('stations.admin_id','=','0')->get();
        
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
      
        return view('SAdmin.functions.show_reg_list',compact('users','stations','user','message','count','Sender_user'));
        }
                else {
                   return redirect("/"); 
                }
    }
    public function approve_admin(admin_request_list $request){
        if($request->session()->has('user_id')){
        
        $result=request('submit');
        $user_id=request('user_id');


        if($result=="approve"){

            $station_id=request('station_id');
            $users = user::where('id','=',$user_id)->first();
            $users->status=1;
            $users->save();
            
            
            DB::table('stations')
            ->where('id', $station_id)
            ->update(['admin_id' => $users->admin->id]);
                           
               echo "<script type='text/javascript'>alert('Admin Request has been successfully Approve !')</script>";
            }
            

       return back()->withInput();
       }
                else {
                   return redirect("/"); 
                }
       // return redirect(back);
    }
    public function cancel_admin(Request $request){
        if($request->session()->has('user_id')){
                $user_id=request('user_id');

             
                DB::table('users')->where('id', '=', $user_id)->delete();
                DB::table('admins')->where('user_id', '=', $user_id)->delete();
                echo "<script type='text/javascript'>alert('Registration Canceled!')</script>";   
            return back()->withInput();
        }
        else {
                   return redirect("/"); 
                }

    }
       
}

