<?php

namespace App;



class User_has_message extends Model
{
    //
    public function message()
    {
    	return $this->belongsTo(message::class);
    }
    public function user()
    {
    	return $this->belongsTo(user::class);
    }
    public function get_inbox_count($user_id){
		
		return User_has_message::where('User_has_messages.receiver_id',$user_id)->where('status','0')->count();
            
	}
	public function get_message($user_id){
		return User_has_message::where('User_has_messages.receiver_id',$user_id)->where('status','0')->latest()->first();

	}
}
