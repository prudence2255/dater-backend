<?php
namespace App\http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;



trait Filters {

    /**
     * filter users by gender
     *
     * @param [type] $query
     * @return void
     */
    public function filterByGender($query){
        $user = request()->user();
        if(request()->has('gender')){
           return $query->where('gender', request()->gender);
        }else if($user->gender === 'Male'){
           return $query->where('gender', 'Female');
        }else{
            return $query->where('gender', 'Male');
        }
    }



}
