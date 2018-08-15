<?php

namespace App;



class reading extends Model
{
    
    public function map()
    {
    	return $this->belongsTo(map::class);
    }
}
