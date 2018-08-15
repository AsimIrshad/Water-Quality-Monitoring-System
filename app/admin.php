<?php

namespace App;


class admin extends Model
{
    //
     public function user()
    {
    	return $this->belongsTo(user::class);
    }
    public function station(){

    	return $this->hasOne(station::class);
    }
    public function admin_request(){

    	return $this->hasOne(admin_request::class);
    }
   
}
