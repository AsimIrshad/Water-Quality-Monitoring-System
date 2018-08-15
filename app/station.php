<?php

namespace App;



class station extends Model
{
    
    public function sensor()
    {
        return $this->hasMany('App\sensor');
    }
     public function admin()
    {
    	return $this->belongsTo(admin::class);
    }
}
