<?php

namespace App;


class user extends Model
{
    //
    public function User_has_message(){

    	return $this->hasMany(User_has_message::class);
    }
    public function admin(){

    	return $this->hasOne(admin::class);
    }
    public function super_admin(){

        return $this->hasOne(super_admin::class);
    }
     public function get_User($user_id){
         return user::where('id',$user_id)->first();
     }
    
}
