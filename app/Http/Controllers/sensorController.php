<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\sensor;
use App\station;
use DB;
use App\user;
use App\User_has_message;
use App\Http\Requests\addsensor;
use App\Http\Requests\update_sensor;
class sensorController extends Controller
{
    public function store(addsensor $request){

            if($request->session()->has('user_id')){
    		
    		$sensor=new sensor;
    		$sensor->station_id=request('station_id');
			$sensor->name =request('name');
			$sensor->stability_criteria=request('stability_criteria');
			$sensor->start_range=request('start_range');
			$sensor->end_range=request('end_range');
			$sensor->status=request('status');
			$sensor->save();
			
			echo "<script type='text/javascript'>alert('sensor has been inserted successfully!')</script>";
            
                 return redirect('/show_sensor');
            
                  }
                else {
                   return redirect("/"); 
                }
    		
}
    public function Show_reg_page(Request $request)
	{

		if($request->session()->has('user_id')){

			$stations=station::where('admin_id','!=','0')->get();
		
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
					return view('SAdmin.functions.Register_sensor',compact('stations','user','message','count','Sender_user'));
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
	public function show_sensor(Request $request)
	{	if($request->session()->has('user_id')){

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

		if($request->session()->get('user_type')=='admin'){
				$station_id=$request->session()->get('station_id');
				 $sensors=sensor::
				    		where('station_id', $station_id)
				    		->orderby('name','asc')->get();

             			
				     return view('Admin.functions.show_sensor',compact('sensors','user','message','count','Sender_user'));
			}
		else if($request->session()->get('user_type')=='super_admin'){
					$stations=station::all();
					if(sizeof($stations)>0){ 

					//dd($stations);

         
				    return view('SAdmin.functions.Show_sensor',compact('stations','user','message','count','Sender_user'));
				}
				else{

                            $error_message_body="There are no Station Registered. Kindly Register Station First.";
                            $button='<a href="/register_station"><button type="button" class="btn btn-block btn-info">Add Station</button></a>';
                            return view('User.Layout.error_message',compact('error_message_body','button'));
                        }   
			}
			 }
                else {
                   return redirect("/"); 
                }
	}
	public function edit_page(Request $request)
	{	if($request->session()->has('user_id')){
		$user_id= $request->session()->get('user_id');
		$sensor_id=request('sensor_id');
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

		if(request('submit')=='edit'){
			$sensors=sensor::
		    		where('id', $sensor_id)->first();
		    		$stations=station::where('admin_id','!=','0')->get();
		    		//dd($sensors);
		    		
		    return view('SAdmin.functions.edit_sensor',compact('sensors','stations','user','message','count','Sender_user'));
		}
		else if(request('submit')=='remove'){
			
			DB::table('sensors')->where('id', '=', $sensor_id)->delete();
                echo "<script type='text/javascript'>alert('Registration Canceled!')</script>";
                return redirect('/show_sensor') ;
		}
		
		 }
                else {
                   return redirect("/"); 
                }
	}
	public function update_sensor(update_sensor $request)
	{	if($request->session()->has('user_id')){
			$sensor_id=request('sensor_id');

			$name =request('name');
			$stability_criteria=request('stability_criteria');
			$start_range=request('start_range');
			$end_range=request('end_range');
			$status=request('status');
			$station_id=request('station_id');
			 

		   DB::table('sensors')
            ->where('id', $sensor_id)
            ->update(['name' => $name ,'stability_criteria'=> $stability_criteria,'start_range'=>$start_range,'end_range'=>$end_range,'status'=>$status ,'station_id'=>$station_id ]);


		return redirect('/show_sensor');
		 }
                else {
                   return redirect("/"); 
                }
	}
}
