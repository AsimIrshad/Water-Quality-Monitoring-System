<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\reading;
use App\map;
use App\station;
use App\sensor;
use App\user;
use App\User_has_message;
use DB;

class mapController extends Controller
{
    
    public function show_heat(request $request){
    		 if($request->session()->has('user_id')){


			$station_id=$request->session()->get('station_id');
          	$sensors = station::find($station_id)->sensor;
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

    		return view('Admin.functions.heat_map',compact('sensors','user','message','count','Sender_user'));
    		 }
        else {
            return redirect("/");
            } 
    }
    public function get_heat_map_data(request $request){
    		if($request->session()->has('user_id')){
    			
    			
                $map=map::with('reading')->whereHas('reading',function($q){
                    $start_date=request('start_date');
                    $end_date=request('end_date');
                    $sensor_id=request('sensor');
                    $q->where('sensor_id',$sensor_id)->whereBetween('readings.created_at',[$start_date, $end_date])
                ;
                })
                //->select(DB::raw("avg(map.readings.value) as value"))
                ->get();

               foreach($map as $tempmap){
                    $sum=0;
                    $count=0;
                    foreach($tempmap->reading as $tempread){
                        $sum=$sum+$tempread->value;
                        $count++;
                    }
                    $tempmap['average']=floatval($sum/$count);
               }
                 	return response()->json($map);

    		}
    		else {
            	return redirect("/");
            } 

    }
    public function check(){

    	$sensor_id=1;
    	$start_date="2018-07-01";
    	$end_date="2018-07-16";


    	$tempreading=reading::where('sensor_id',$sensor_id)
    	->whereBetween('readings.created_at', [$start_date, $end_date])->get();
    	$data;

    	foreach($tempreading as $read){
    		$sum=0;
    		$count=0;
    			foreach($tempreading as $read1){
    				if(($read1->map->longitude==$read->map->longitude)&&($read1->map->latitudes==$read->map->latitudes)){
    					$sum=$sum+$read1->value;
    				}

    			}
    	}
    	dd($data);
    	//dd($reading->find('1')->map->longitude);*/

    } 
}
