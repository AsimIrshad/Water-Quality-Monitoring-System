<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\user;

use App\admin;
use App\super_admin;
use App\Http\Requests\login;
use App\message;
use App\User_has_message;
use App\Http\Requests\Store_Super_Registration;
use App\Http\Requests\editProfile;
use DB;

class UserController extends Controller
{
    //
    public function login(login $request){

    		$email=request('email');
            $password=hash('ripemd128',request('password'));
                    
    		$user=user::
    		where('email', $email)
    			->Where('password',$password)
    			->Where('status',1)->get();

        if(!$user->isEmpty()){

    				$request->session()->put('user_id',$user->first()->id);
    			
                	return redirect('/home_page');
    			}
            else {
                echo "<script type='text/javascript'>alert('Email or Password is invalid!')</script>";
                 return redirect('/');
            }

    		

    }
    public function public_signin(Request $request){
        $email=request('username');
        $password=request('password');
        $user=user::
            where('email', $email)
                ->Where('password',$password)
                ->Where('status',1)->get();
         if(!$user->isEmpty()){
           echo 'login';
         } 
         else{
            echo 'notlogin';
         }      

    }
    public function ShowhomePage(Request $request){

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
            
        if($user->object=="App\admin"){
            $request->session()->put('user_type','admin');
            $request->session()->put('station_id',$user->admin->station->id);
           return view('Admin.functions.home_page' ,compact('user','message','count','Sender_user'));
        }    
        else if($user->object=="App\super_admin"){
            $request->session()->put('user_type','super_admin');
           return view('SAdmin.functions.home_page',compact('message','count','user','Sender_user'));
        }
    }
    else {
        return view('User.sign_in');
    }

    }
 	public function logout(Request $request){

        if($request->session()->get('user_type')=='admin'){
            $request->session()->forget('station_id');
        }
            $request->session()->forget('user_id');
            $request->session()->forget('user_type');

        if( !$request->session()->has('users_id')){
            
            return redirect('/');
        }
 		
    }
    public function show_login_page(Request $request){
        
         if($request->session()->get('user_type')=='admin'){
            $request->session()->forget('station_id');
        }
            $request->session()->forget('user_id');
            $request->session()->forget('user_type');

        if( !$request->session()->has('users_id')){
            
           return view('User.sign_in');
        }

    	
    }
    public function profile(Request $request){
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

             return view('User.profile',compact('message','count','user','Sender_user'));

        }
        else {
        return view('User.sign_in');
    }

    }
    public function edit_profile(Request $request){
         if($request->session()->has('user_id')){
            $user_id= $request->session()->get('user_id');
            $UserID=request('userId');


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

             return view('User.edit_profile',compact('message','count','user','Sender_user'));

        }
        else {
        return view('User.sign_in');
    }

    }
    public function update_profile(editProfile $request){

                 $user_id=request('user_id');
                 $user=user::where('id',$user_id)->first();
                $temp_user=new \App\user;
                

                $temp_user->fname =request('first_name');
                $temp_user->lname=request('last_name');
                $temp_user->mobile_no=request('mobile_no');
                
                $temp_user->user_name=request('user_name');

                
                $cnic =request('cnic');
                $employee_no =request('employee_no');
                $department_name =request('department_name');
              

            DB::table('users')
            ->where('id', $user_id)
            ->update(['fname' => $temp_user->fname ,'lname'=> $temp_user->lname,'mobile_no'=>$temp_user->mobile_no,'user_name'=>$temp_user->user_name] );

                if($user->object=="App\admin"){
                   
                $admin_id=admin::where('user_id',$user_id)->first();

                DB::table('admins')
                ->where('id', $admin_id->id)
                ->update(['cnic' => $cnic ,'department_name'=> $department_name,'employee_no'=>$employee_no] );

          }
          else if($user->object=="App\super_admin"){
           
            $super_admin=super_admin::where('user_id',$user_id)->first();

            DB::table('super_admins')
            ->where('id', $super_admin->id)
             ->update(['cnic' => $cnic ,'department_name'=> $department_name,'employee_no'=>$employee_no ]);
          }
            
                
          //  
          return redirect('/profile');  


    }
    public function check(){

        $email=request('username');
            $password=hash('ripemd128',request('password'));
                    
            $user=user::
            where('email', $email)
                ->Where('password',$password)
                ->Where('status',1)->get();

        if(!$user->isEmpty()){

                    $request->session()->put('user_id',$user->first()->id);
                
                   // return redirect('/home_page');
                }
            else {
                echo "<script type='text/javascript'>alert('Email or Password is invalid!')</script>";
                 //return redirect('/');
            }


            
    


        //return view('welcome');
        //dd($User); 

    
     

}
       

/* hum login page pr do link day dan gy ik admin ky liye or dusra sadmin ky liye