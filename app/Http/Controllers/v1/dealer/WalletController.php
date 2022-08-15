<?php

namespace App\Http\Controllers\v1\dealer;

use App\Http\Controllers\Controller;
use App\Http\Requests\WalletTopUpRequest;
use App\Models\TopUpRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WalletController extends Controller
{
    //
    public function submit_topup_request(WalletTopUpRequest $req){
        try{
            $new_request = TopUpRequest::create($req->all());
            $new_request->request_from()->associate(Auth::user());
            if ($req->hasFile('document') && $req->file('document')->isValid()) {
                $path = request()->file('document')->store('public/topup_request_docs');
                $new_request->document_download_link=env("APP_URL").Storage::url($path);
                $new_request->document=$path;
                $new_request->save();
            }
            return $this->success($new_request, 'data', 200);
        }
        catch (Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
}
