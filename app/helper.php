<?php

use App\Models\Admin;
use App\Models\Dealer;
use App\Models\Tycoon;
use App\Models\User;

if (!function_exists('get_first_user_by_email')) {
    function get_first_user_by_email($email)
    {
        $user = null;
        if(Admin::where('email',$email)->exists()){
            $user = Admin::with('roles')->where('email',$email)->first();
        }
        if(Dealer::where('email',$email)->exists()){
            $user = Dealer::with('roles')->with('wallet')->where('email',$email)->first();
        }
        else if(Tycoon::where('email',$email)->exists()){
            $user = Tycoon::with('roles')->where('email',$email)->first();
        }
        // else if(PreDealer::where('email',$email)->exists()){
        //     $user = PreDealer::where('email',$email)->first();
        // }
        return  $user;
    }
}
if (!function_exists('generate_user_id')) {
    function generate_user_id($model)
    {
        $id=date('Y').date('m').date('d').$model->all()->count();
        while($model->where('user_id',$id)->exists()){
            $id=date('Y').date('m').date('d').$model->all()->count();
        }
    }
}
