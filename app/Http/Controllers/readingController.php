<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\reading;
use App\map;
use App\station;
use App\sensor;
use DB;
use Carbon\Carbon;
use App\user;
use App\User_has_message;
use App\Http\Requests\store_file;

use App\Http\Requests\getmap;

class readingController extends Controller
{

    public function store_readings(store_file $request){
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

        $station_id=$request->session()->get('station_id');
        $sensors = station::find($station_id)->sensor;

        $readings=array();
           foreach($_FILES['file']['tmp_name'] as $key => $tmp_name)
            {

                $file_name = $_FILES['file']['name'][$key];
                $file_size =$_FILES['file']['size'][$key];
                $file_tmp =$_FILES['file']['tmp_name'][$key];
                $file_type=$_FILES['file']['type'][$key];  
               
               $fp = fopen($file_tmp, 'rb');

               
                    while ( ($line = fgets($fp)) !== false) {
                            $sensor_a;
                            $file_data=explode(',', $line);
                            $file_data=array_pad( $file_data ,5,0);
                            
                            foreach($sensors as $sensor){
                                    if($file_data[0]==$sensor->name){
                                            $sensor_a=$sensor;
                                            break;
                                    }    
                                }
                                
                                if($file_data[1]>=$sensor_a->start_range && $file_data[1]<=$sensor_a->end_range){
 
                            $longitude =floatval($file_data[2]);
                            $latitudes =floatval($file_data[3]);
                            

                              
                                        
                            $map = new map;
                            $map=$map->addLocation($longitude,$latitudes);

                            $reading = new reading;
                            $reading ->value =floatval($file_data[1]);
                            $reading->sensor_id =$sensor_a->id; 
                             

                            $map->reading()->save($reading);
                            array_push($readings, $file_data);
                          }
                          else {
                            
                            continue;
                          }
                        }

        }
      
            echo "<script type='text/javascript'>alert('Data Submitted successfully!')</script>";
      return view('Admin.functions.upload_file',compact('readings','user','message','count','Sender_user'));
      }
                else {
                   return redirect("/"); 
                }

       }
    public function view_history_page(Request $request){
          if($request->session()->has('user_id')){

          $user_id= $request->session()->get('user_id');
          
          
            $station_id=$request->session()->get('station_id');
            $sensor_list = station::find($station_id)->sensor;
            if(sizeof($sensor_list)>0){

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


            return view('Admin.functions.view_history',compact('sensor_list','user','message','count','Sender_user'));
          }
          else{
                $error_message_body="There are no Sensor Registered. Kindly Contact Head Office.";
                            $button='';
                            return view('User.Layout.error_message',compact('error_message_body','button'));
            }
          }
                else {
                   return redirect("/"); 
                }
          } 
    public function show(Request $request){
          if($request->session()->has('user_id')){

          $user_id= $request->session()->get('user_id');
          

            $station_id=$request->session()->get('station_id');
            
            $sensor_id=request('sensor_id');
           
            

            $sensors = sensor::where('id',$sensor_id)->with('reading')->whereHas('reading',function($q){
              
              $start_date=request('start_date');
              $end_date=request('end_date');
              $q->whereBetween('created_at', [$start_date, $end_date]);

            })->first();
            
            $sensor_list=sensor::where('station_id',$station_id)->get();

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
         
          //dd($sensor_list);
             return view('Admin.functions.view_history',compact('sensor_list','sensors','user','message','count','Sender_user'));
              }
                else {
                   return redirect("/"); 
                }

             }
    public function showbarchart(Request $request){
          if($request->session()->has('user_id')){

          $user_id= $request->session()->get('user_id');
          $station_id=$request->session()->get('station_id');
          $sensors = station::find($station_id)->sensor;

         
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

            

               return view('Admin.functions.bar_chart',compact('sensors','user','message','count','Sender_user'));
               }
                else {
                   return redirect("/"); 
                }   
            }
    public function get_data(Request $request){

          if($request->session()->has('user_id')){
            $sensor_id=request('sensor');
            $start_date=request('start_date');
            $end_date=request('end_date');
            $category=request('category');
            


            if($category=='day'){
          $readings = DB::table('readings')
                    ->whereBetween('created_at', [$start_date, $end_date])
                   ->select(
                        DB::raw("Day(created_at) as Day"),
                        DB::raw("Month(created_at) as month"),
                        DB::raw("year(created_at) as year"),
                        DB::raw("avg(value) as value"))
                    ->orderBy("created_at")
                    ->groupBy(DB::raw("Day(created_at)"))
                    ->get();

                    $readings['category']="Day";
          return response()->json($readings);
            }
              else if($category=='month'){
                $readings = DB::table('readings')
                          ->whereBetween('created_at', [$start_date, $end_date])
                         ->select(
                              DB::raw("Day(created_at) as Day"),
                              DB::raw("Month(created_at) as month"),
                              DB::raw("year(created_at) as year"),
                              DB::raw("avg(value) as value"))
                          ->orderBy("created_at")
                          ->groupBy(DB::raw("Month(created_at)"))
                          ->get();

                          $readings['category']="Month";
                return response()->json($readings);
              }
             else if($category=='year'){
               $readings = DB::table('readings')
                          ->whereBetween('created_at', [$start_date, $end_date])
                         ->select(
                              DB::raw("Day(created_at) as Day"),
                              DB::raw("Month(created_at) as month"),
                              DB::raw("year(created_at) as year"),
                              DB::raw("avg(value) as value"))
                          ->orderBy("created_at")
                          ->groupBy(DB::raw("Year(created_at)"))
                          ->get();

                          $readings['category']="year";
                return response()->json($readings);
              }
              }
                      else {
                         return redirect("/"); 
                      }
              }
    public function showlinechart(Request $request){
              if($request->session()->has('user_id')){
              $user_id= $request->session()->get('user_id');
             

              $station_id=$request->session()->get('station_id');
              $sensors = station::find($station_id)->sensor;

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

                   return view('Admin.functions.line_chart',compact('sensors','user','message','count','Sender_user'));
                   }
                    else {
                       return redirect("/"); 
                    }   
              }
    public function get_dataa(Request $request){
                if($request->session()->has('user_id')){

                  $sensor_id=request('sensor');
                  $start_date=request('start_date');
                  $end_date=request('end_date');
                  $category=request('category');

                  if($category=='day'){
                     $readings = DB::table('readings')
                          ->whereBetween('created_at', [$start_date, $end_date])
                         ->select(
                              DB::raw("Day(created_at) as Day"),
                              DB::raw("avg(value) as value"))
                          ->orderBy("created_at")
                          ->groupBy(DB::raw("Day(created_at)"))
                          ->get();
                return response()->json($readings);
                }
                else if($category=='month'){
                  $readings = DB::table('readings')
                          ->whereBetween('created_at', [$start_date, $end_date])
                         ->select(
                              DB::raw("Month(created_at) as month"),
                              DB::raw("avg(value) as value"))
                          ->orderBy("created_at")
                          ->groupBy(DB::raw("Month(created_at)"))
                          ->get();
                return response()->json($readings);
                  }
                 else if($category=='year'){
                    $readings = DB::table('readings')
                              ->whereBetween('created_at', [$start_date, $end_date])
                             ->select(
                                  DB::raw("year(created_at) as year"),
                                  DB::raw("avg(value) as value"))
                          ->orderBy("created_at")
                          ->groupBy(DB::raw("Year(created_at)"))
                          ->get();
                    return response()->json($readings);
                  }
                  }
                          else {
                             return redirect("/"); 
                      }
                 }
    public function show_map(Request $request){
          if($request->session()->has('user_id')){
          $user_id= $request->session()->get('user_id');
          
          
           $station_id=$request->session()->get('station_id');
           $station=station::where('id',$station_id)->first();
           $sensors = station::find($station_id)->sensor;
           
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

          return view("Admin.functions.show_map",compact('station','sensors','user','message','count','Sender_user'));
          }
                else {
                   return redirect("/"); 
                }

           }
    public function show_line_map(Request $request){
          if($request->session()->has('user_id')){
          $user_id= $request->session()->get('user_id');
          
          
           $station_id=$request->session()->get('station_id');
           $station=station::where('id',$station_id)->first();
           //dd($station);
          $sensors = station::find($station_id)->sensor;
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

          return view("Admin.functions.show_line_map",compact('station','sensors','user','message','count','Sender_user'));
          }
                else {
                   return redirect("/"); 
                }
              }
    public function get_map_data(getmap $request){
          if($request->session()->has('user_id')){

            $sensor_id=Request('sensor');
            $start_date=Request('start_date');
            $end_date=Request('end_date');
            $category=Request('category');
            


             if($category=='day'){

                  $readings = reading::where('sensor_id',$sensor_id)->with('map')->whereHas('map',function($q){
                        $lat=Request('lat');
                        $lng=Request('lng');
                                  //$q->where('longitude','=',$lng)->where('latitudes','=',$lat)
                         $q->select(DB::raw('*, ( 6367 * acos( cos( radians('.$lat.') ) * cos( radians( latitudes ) ) * cos( radians( longitude ) - radians('.$lng.') ) + sin( radians('.$lat.') ) * sin( radians( latitudes ) ) ) ) AS distance'))
                          ->having('distance', '<', 1)
                          ;
                          })
                   ->whereBetween('readings.created_at', [$start_date, $end_date])
                     
                   ->select(
                        DB::raw("Day(created_at) as Day"),
                        DB::raw("avg(value) as value"),
                        DB::raw("Month(created_at) as month"),
                        DB::raw("year(created_at) as year"))
                         
                    ->orderBy("created_at")
                    ->groupBy(DB::raw("Day(created_at)"))
                    ->get();
                    $readings['category']="Day";
              //dd($readings);
              return response()->json($readings);
            }
            else if($category=='month'){
                $readings = reading::where('sensor_id',$sensor_id)->with('map')->whereHas('map',function($q){
                        $lat=Request('lat');
                        $lng=Request('lng');
                                  //$q->where('longitude','=',$lng)->where('latitudes','=',$lat)
                         $q->select(DB::raw('*, ( 6367 * acos( cos( radians('.$lat.') ) * cos( radians( latitudes ) ) * cos( radians( longitude ) - radians('.$lng.') ) + sin( radians('.$lat.') ) * sin( radians( latitudes ) ) ) ) AS distance'))
                          ->having('distance', '<', 1)
                          ;
                          })
                //select to_char(timestamp, 'yyyy-mm') from your_table
                   ->whereBetween('readings.created_at', [$start_date, $end_date])
                     
                   ->select(
                        
                        DB::raw("avg(value) as value"),
                        DB::raw("Month(created_at) as month"),
                        DB::raw("year(created_at) as year"))
                         
                    ->orderBy("created_at")
                    ->groupBy(DB::raw("Month(created_at)"))
                    ->get();
                    $readings['category']="Month";
             // dd($readings);
              return response()->json($readings);
            }
            else if($category=='year'){

                $readings = reading::where('sensor_id',$sensor_id)->with('map')->whereHas('map',function($q){
                        $lat=Request('lat');
                        $lng=Request('lng');
                                  //$q->where('longitude','=',$lng)->where('latitudes','=',$lat)
                         $q->select(DB::raw('*, ( 6367 * acos( cos( radians('.$lat.') ) * cos( radians( latitudes ) ) * cos( radians( longitude ) - radians('.$lng.') ) + sin( radians('.$lat.') ) * sin( radians( latitudes ) ) ) ) AS distance'))
                          ->having('distance', '<', 1)
                          ;
                          })
                   ->whereBetween('readings.created_at', [$start_date, $end_date])
                     
                   ->select(
                        
                        DB::raw("avg(value) as value"),
                        
                        DB::raw("year(created_at) as year"))
                         
                    ->orderBy("created_at")
                    ->groupBy(DB::raw("Year(created_at)"))
                    ->get();
                    $readings['category']="year";
              //dd($readings);
              return response()->json($readings);
            }


            }
                else {
                   return redirect("/"); 
                }
              }
    public function check(Request $request){
          if($request->session()->has('user_id')){

            $sensor_id=1;
            $start_date="2018-07";
            $end_date="2018-08";
            $category="month";
            $start_date=explode('-', $start_date);
            $end_date=explode('-', $end_date);


             if($category=='day'){

                  $readings = reading::where('sensor_id',$sensor_id)->with('map')->whereHas('map',function($q){
                        $lat=Request('lat');
                        $lng=Request('lng');
                                  //$q->where('longitude','=',$lng)->where('latitudes','=',$lat)
                         $q->select(DB::raw('*, ( 6367 * acos( cos( radians('.$lat.') ) * cos( radians( latitudes ) ) * cos( radians( longitude ) - radians('.$lng.') ) + sin( radians('.$lat.') ) * sin( radians( latitudes ) ) ) ) AS distance'))
                          ->having('distance', '<', 1)
                          ;
                          })
                   ->whereBetween('readings.created_at', [$start_date, $end_date])
                     
                   ->select(
                        DB::raw("Day(created_at) as Day"),
                        DB::raw("avg(value) as value"),
                        DB::raw("Month(created_at) as month"),
                        DB::raw("year(created_at) as year"))
                         
                    ->orderBy("created_at")
                    ->groupBy(DB::raw("Day(created_at)"))
                    ->get();
                    $readings['category']="Day";
              //dd($readings);
              return response()->json($readings);
            }
            else if($category=='month'){
                $readings = reading::where('sensor_id',$sensor_id)->with('map')->whereHas('map',function($q){
                        $lat=31.61702;
                        $lng=74.315403;
                                  //$q->where('longitude','=',$lng)->where('latitudes','=',$lat)
                         $q->select(DB::raw('*, ( 6367 * acos( cos( radians('.$lat.') ) * cos( radians( latitudes ) ) * cos( radians( longitude ) - radians('.$lng.') ) + sin( radians('.$lat.') ) * sin( radians( latitudes ) ) ) ) AS distance'))
                          ->having('distance', '<', 1)
                          ;
                          })
                //select to_char(timestamp, 'yyyy-mm') from your_table
                //$myDateTime = DateTime::createFromFormat('Y-m-d', 'readings.created_at');
                  
                  ->where('Year(created_at)', '>=', $start_date[0])
                  ->whereMonth('created_at', '=', $start_date[1])

                  //->whereYear('created_at', '<', $end_date[0])
                  //->whereMonth('created_at', '=', $end_date[1])   
                   ->select(
                        
                        DB::raw("avg(value) as value"),
                        DB::raw("Month(created_at) as month"),
                        DB::raw("year(created_at) as year"))
                         
                    ->orderBy("created_at")
                    ->groupBy(DB::raw("Month(created_at)"))
                    ->get();
                    $readings['category']="Month";
              dd($readings);
              //return response()->json($readings);
            }
            else if($category=='year'){

                $readings = reading::where('sensor_id',$sensor_id)->with('map')->whereHas('map',function($q){
                        $lat=Request('lat');
                        $lng=Request('lng');
                                  //$q->where('longitude','=',$lng)->where('latitudes','=',$lat)
                         $q->select(DB::raw('*, ( 6367 * acos( cos( radians('.$lat.') ) * cos( radians( latitudes ) ) * cos( radians( longitude ) - radians('.$lng.') ) + sin( radians('.$lat.') ) * sin( radians( latitudes ) ) ) ) AS distance'))
                          ->having('distance', '<', 1)
                          ;
                          })
                   ->whereBetween('readings.created_at', [$start_date, $end_date])
                     
                   ->select(
                        
                        DB::raw("avg(value) as value"),
                        
                        DB::raw("year(created_at) as year"))
                         
                    ->orderBy("created_at")
                    ->groupBy(DB::raw("Year(created_at)"))
                    ->get();
                    $readings['category']="year";
              //dd($readings);
              return response()->json($readings);
            }


            }
                else {
                   return redirect("/"); 
                } 
             
          
          //echo "1";
          /*$sensor_id=1;
          echo $start_date="2018-05-27";
          echo  $end_date="2018-07-28";
          echo  $category="day";*/

        
          /*
                  $readings = DB::table('readings')
                              ->select(
                                  DB::raw("Month(created_at) as month"),
                                  DB::raw("avg(value) as value"))
                              ->orderBy("created_at")
                              ->groupBy(DB::raw("month(created_at)"))
                              ->get();*/
                      //echo($readings);

                      /*  $a;
                    foreach($readings as $key=> $reading){
                      if($key=='06'){
                          $a['June']=$reading;
                      }

                    }  */  
          //dd($readings);

           

                         /* $user = new reading;
                          $user ->value = 4;
                          $user ->sensor_id =1; 
                          $user ->save();

                          $role = new map;
                          $role ->longitude =14.2;
                          $role ->latitudes =15.2;

          // option 1:
          // this will set the user_id on the role, and then save the role to the db
                          $user ->map()->save($role);*/


                   //$sensor = station::find(1)->whereDate('created_at', Carbon::today())->get();      
                 // echo $sensor->created_at;
                  //dd($sensor);
                  //$map = reading::latest()->get();
                 // dd($map);
              }

}
