<?php

namespace App\Http\Controllers;

use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\store_video;
class videoController extends Controller
{
    //
	public function Show_upload_page(Request $request){
	 if($request->session()->has('user_id')){
		return view('Admin.functions.upload_video');
     }
                else {
                   return redirect("/"); 
                }

	}
	public function store(store_video $request){
			
			$station_id=$request->session()->get('station_id');
			$filename=$request->file[0]->getClientOriginalName();
                $path= $request->file[0]->storeAs('public/upload/video',$filename);
//dd($filename+$path);
           /*foreach($_FILES['file']['tmp_name'] as $key => $tmp_name)
            {
               echo  $file_name = $_FILES['file']['name'][$key];
                $file_size =$_FILES['file']['size'][$key];
                $file_tmp =$_FILES['file']['tmp_name'][$key];
                $file_type=$_FILES['file']['type'][$key];  
               
              
			            //echo "<script type='text/javascript'>alert('Data Submitted successfully!')</script>";
			           	
					//return view('Admin.functions.upload_video');

				}*/
			/*	if($request->hasFile('file[]')){
                $filename=$request->file->getClientOriginalName();
                 //$path=$request->file->store('public/upload/image',$filename);
            	$path = Storage::putFile('public/upload/image', $request->file[0]);
             }
             else{
             	foreach($request->file('file') as $file) {
   echo"1";
   				$filename=$request->file[0]->getClientOriginalName();
                // $path=$request->file[0]->storeAs('public/upload/',$filename);
			$path = Storage::putFile('file[0]', new file('public/upload/image'));
}
             	echo"j";
             }*/
			}

	public function show(Request $request){
	$user_id= $request->session()->get('user_id');
          $user=user::where('id',$user_id)->first();
          
		return view('Admin.functions.upload_video',compact('user'));

	}

}
