<?php

namespace App\Traits;
use App\Models\Country;
use App\Models\Division;
use App\Models\District;
use App\Models\Thana;
use App\Models\Union;

trait GeocodeTrait {
    public function countryList() {
        $countries = Country::select('id as value', 'name as label', 'code')->get();
        return $countries;
    }
    public function divisionList() {
        $countries = Division::select('id as value', 'name as label', 'country_id')->get();
        return $countries;
    }
    public function districtList() {
        $countries = District::select('id as value', 'name as label', 'division_id')->get();
        return $countries;
    }
    public function thanaList() {
        $countries = Thana::select('id as value', 'name as label', 'district_id')->get();
        return $countries;
    }
    public function unionList() {
        $countries = Union::select('id as value', 'name as label', 'thana_id')->get();
        return $countries;
    }
}