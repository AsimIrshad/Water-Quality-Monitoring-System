<?php

namespace App;


class message extends Model
{
    public function user(){
    	return $this->belongsTo(user::class);
    }
    public function User_has_message(){

    	return $this->hasMany(User_has_message::class);
    } 
    
}
