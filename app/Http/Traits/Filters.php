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
        }else if($user->gender === 'male'){
           return $query->where('gender', 'female');
        }else{
            return $query->where('gender', 'male');
        }
    }



}
