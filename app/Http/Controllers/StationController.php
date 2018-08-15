<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\reading;
use App\map;
use App\station;
use App\sensor;
use App\user;
use Carbon\Carbon;
use DB;
use App\User_has_message;   
use App\Http\Requests\storeStation;
class StationController extends Controller
{
     public function Show_reg_page(Request $request)
    {
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

              $stations=station::all();
        return view('SAdmin.functions.register_station',compact('user','message','count','Sender_user','stations'));
        }
                else {
                   return redirect("/"); 
                }
    }
    public function store(storeStation $request){
        if($request->session()->has('user_id')){
            $station=new station;
            
            $station->station_name =request('station_name');
            $station->address=request('address');
            $station->admin_id=0;
            $station->save();
            
            echo "<script type='text/javascript'>alert('sensor has been inserted successfully!')</script>";
            
                 return redirect('/show_station');
                 }
                else {
                   return redirect("/"); 
                }
            
            }
            public function show_station(Request $request)
            { 
                if($request->session()->has('user_id')){
                     $stations=station::
                            orderby('station_name','asc')->get();
                     if(sizeof($stations)>0){        
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

                 $stations=station::
                            orderby('station_name','asc')->get();
                  
              return view('SAdmin.functions.show_station',compact('stations','user','message','count','Sender_user'));
              }
               else{

                            $error_message_body="There are no Station Registered. Kindly Register Station First.";
                            $button='<a href="/register_station"><button type="button" class="btn btn-block btn-info">Add Station</button></a>';
                            return view('User.Layout.error_message',compact('error_message_body','button'));
                        } 
          }

                else {
                   return redirect("/"); 
                }
            }
            public function edit_page(Request $request)
                {
                    if($request->session()->has('user_id')){
                    $id=request('id');
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
                    $stations=station::
                            get();

                    if(request('submit')=='edit'){
                        $station=station::
                                where('id', $id)->first();
                        return view('SAdmin.functions.edit_station',compact('stations','station','message','count','Sender_user','user'));
                    }
                    else if(request('submit')=='remove'){

                        DB::table('stations')->where('id', '=', $id)->delete();
                            //echo "<script type='text/javascript'>alert('Registration Canceled!')</script>";
                            return redirect('/show_station') ;
                    }
                    }
                else {
                   return redirect("/"); 
                }
                    
                }
                public function update_station(storeStation $request)
                    {
                        if($request->session()->has('user_id')){
                         $id=request('id');

                             $station_name =request('station_name');
                            $address=request('address');
                            

                           DB::table('stations')
                            ->where('id', $id)
                            ->update(['station_name' => $station_name ,'address'=> $address]);


                        return redirect('/show_station');
                        }
                else {
                   return redirect("/"); 
                }
                    }
}
