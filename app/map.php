<?php

namespace App;


class map extends Model
{
    //
     
    public function reading()
    {
        return $this->hasMany('App\reading');
    }
    public function get_Map_id($Long,$lat){
    		return map::where('longitude',$Long)->where('latitudes',$lat)->first();
    }
    public function addLocation($Long,$lat){
    		$tempmap=new map;
    		$map_id=$this->get_Map_id($Long,$lat);
    		if(empty($map_id)){

    		$tempmap->longitude=$Long;
    		$tempmap->latitudes=$lat;
    		$tempmap->save();
    		return $tempmap;

   			}else if (!empty($map_id)){
   				echo "string";
   				return $map_id;
   			}
    }
}
