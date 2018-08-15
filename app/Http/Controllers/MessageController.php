<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\user;
use App\station;
use App\admin;
use App\message;
use App\super_admin;
use DB;
use App\User_has_message;
use App\Http\Requests\storemessage;
class MessageController extends Controller
{
   
    public function ShowMessage_page(request $request){
             if($request->session()->has('user_id')){

                $stations=station::where('admin_id','!=','0')->get()
                        ->sortBy('station_name');
                  if(sizeof($stations)>0){      


            		       $user_id = $request->session()->get('user_id');
                    		$user_type = $request->session()->get('user_type');

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

            		    	 return view('User.message_page',compact('user','stations','count','message','Sender_user'));
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

    public function store(storemessage $request){
        if($request->session()->has('user_id')){
    		$result=request('submit');
    		if($result=="send"){
                
                if($request->hasFile('file')){
                        $filename=$request->file->getClientOriginalName();
                        $path= $request->file->storeAs('public/upload/image',$filename);

                        $message=new \App\message;
                        
                        $message->body=request('body');
                        $message->category=request('category');
                        $message->subject=request('subject');
                        $message->image=$path;
                        $message->save();


                        $user_has_message=new \App\User_has_message;
                        $user_has_message->receiver_id =request('receiver');
                        $user_has_message->user_id=request('sender');
                        $user_has_message->status=0;

                        $message->User_has_message()->save($user_has_message);

                        echo "<script type='text/javascript'>alert('Message sent successfully!')</script>";
                        return redirect('/compose');
                }
                else
                {
                
                        $message=new \App\message;
                        
                        $message->body=request('body');
                        $message->category=request('category');
                        $message->subject=request('subject');
                        $message->image="null";
                        $message->save();


                        $user_has_message=new \App\User_has_message;
                        $user_has_message->receiver_id =request('receiver');
                        $user_has_message->user_id=request('sender');
                        $user_has_message->status=0;

                        $message->User_has_message()->save($user_has_message);  

                        echo "<script type='text/javascript'>alert('Message sent successfully!')</script>";
                return redirect('/compose');   
                }
    			
            }
            else if($result=="cancel"){
            	return redirect('/home_page');

            }
        }
        else {
            return redirect('/');
        }
    }
    public function showSentMessage(Request $request){
        if($request->session()->has('user_id')){
    		$user_id = $request->session()->get('user_id');
            $user_type = $request->session()->get('user_type');
            
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


    	return view('User.sent_message',compact('user','count','message','Sender_user')); 
        }
        else {
            return redirect('/');
        }
    }
    
    public function showInboxMessage(Request $request){

        if($request->session()->has('user_id')){
            $user_id = $request->session()->get('user_id');
            $user_type = $request->session()->get('user_type');
            $user_message=new User_has_message;
            $user=new user;
            $user=$user->get_User($user_id);
            $count=$user_message->get_inbox_count($user_id);
                    $user_messages=\App\User_has_message::where('User_has_messages.receiver_id',$user_id)->get();
              
                  if($count<=0){
                $message="null";
                $Sender_user="null";        
            }
            else{
                   $message=$user_message->get_message($user_id);
                   $Sender_user=$user->get_User($message->user_id);
            }



        return view('User.inbox_message',compact('user_messages','count','user','message','Sender_user')); 
        }
        else {
            return redirect('/');
        }
    }
    public function showDetail(Request $request){
        if($request->session()->has('user_id')){

        $user_id = $request->session()->get('user_id');
        
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
            


        $id=request('id');
        $sender=request('sender');
        $receiver=request('receiver');
        $messages=message::where('id',$id)->first();


        $sender = user::where('id', $sender)->first();
        $receiver =user::where('id', $receiver)->first();


                    DB::table('user_has_messages')
                            ->where('message_id', $id)->where('receiver_id',$user_id)
                            ->update(['status' => 1 ]);

           

        return view('User.show_message_detail',compact('messages','sender','receiver','count','user','message','Sender_user'));
        }
        else {
            return redirect('/');
        }
      
    }

}
