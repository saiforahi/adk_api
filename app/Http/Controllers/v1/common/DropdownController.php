<?php

namespace App\Http\Controllers\v1\common;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\GeocodeTrait;
use DB;

class DropdownController extends Controller
{
    use GeocodeTrait;
 
    public function geocode ():JsonResponse
    {
        $data = [
            'countryList'=> $this->countryList(),
            'divisionList'=> $this->divisionList(),
            'districtList'=> $this->districtList(),
            'thanaList'=> $this->thanaList(),
            'unionList'=> $this->unionList(),
        ];
        return $this->success($data);
    }
}
