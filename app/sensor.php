<?php

namespace App;



class sensor extends Model
{
     public function reading()
    {
        return $this->hasMany('App\reading');
    }
}
