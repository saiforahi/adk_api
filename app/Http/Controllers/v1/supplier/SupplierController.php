<?php

namespace App\Http\Controllers\v1\supplier;

use App\Events\v1\UploadImageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth:admin'])->except([]);
        // $this->middleware('role:super-admin|unit-admin')->except([]);
    }
    
    
    // storing new supplier
    public function _store(SupplierRequest $req):JsonResponse
    {
        try{
            $new_supplier = Supplier::create(array_merge($req->except('company_name','company_contact'),['company'=>array('name'=>$req->company_name,'contact'=>$req->company_contact)]));
            $images=array();
            if($req->has('total_images') && $req->total_images>0){
                for($index=1;$index<=$req->total_images;$index++){
                    if($req->hasFile('image'.$index) && $req->file('image'.$index)->isValid()){
                        array_push($images,$req->file('image'.$index));
                    }
                }
            }
            event(new UploadImageEvent($new_supplier,$images));
            if($new_supplier){
                return $this->success($new_supplier);
            }
            else{
                return $this->failed(null, 'New supplier creation failed', 500);
            }
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }


    //all supplier list fetch
    public function _all():JsonResponse
    {
        try{
            return $this->success(Supplier::all());
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }


    // update supplier details
    public function _update(SupplierRequest $req):JsonResponse
    {
        try{
            return $this->success(Supplier::all());
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }


    //show specific suppler details
    public function _details(Supplier $supplier):JsonResponse
    {
        try{
            return $this->success($supplier);
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
}
