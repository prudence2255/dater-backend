<?php

namespace App\http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait Filters
{

    /**
     * filter users by gender
     *
     * @param [type] $query
     * @return object
     */
    public function filters($query)
    {
        $user = request()->user();

        $queryParams = request()->query();

        if (!empty($queryParams['gender'])) {
            $query->where('gender', $queryParams['gender']);
        } else if ($user->gender === 'Male') {
            $query->where('gender', 'Female');
        } else {
            $query->where('gender', 'Male');
        }

        if (!empty($queryParams['country'])) {
            $query->where('country', $queryParams['country']);
        }

        if (!empty($queryParams['city'])) {
            $query->where('city', $queryParams['city']);
        }

        if (!empty($queryParams['min_age'])) {
            $query->whereYear('birth_date', '<=', $this->birth_date($queryParams['min_age']));
        }

        if (!empty($queryParams['max_age'])) {
            $query->whereYear('birth_date', '>=', $this->birth_date($queryParams['max_age']));
        }

        return $query;
    }


    private function birth_date($age)
    {
        return date('Y', strtotime($age . ' years ago'));
    }
}
