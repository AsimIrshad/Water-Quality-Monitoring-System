<?php

namespace App;


class super_admin extends Model
{
    //
    
    public function user()
    {
    	return $this->belongsTo(user::class);
    }
}
