<?php

use App\Models\Admin;
use App\Models\Dealer;
use App\Models\PreDealer;
use App\Models\SubDealer;
use App\Models\User;

if (!function_exists('get_first_user_by_email')) {
    function get_first_user_by_email($email)
    {
        $user = null;
        if(Admin::where('email',$email)->exists()){
            $user = Admin::with('roles')->where('email',$email)->first();
        }
        if(Dealer::where('email',$email)->exists()){
            $user = Dealer::with('roles')->where('email',$email)->first();
        }
        // else if(SubDealer::where('email',$email)->exists()){
        //     $user = SubDealer::where('email',$email)->first();
        // }
        // else if(PreDealer::where('email',$email)->exists()){
        //     $user = PreDealer::where('email',$email)->first();
        // }
        return  $user;
    }
}