<?php

namespace App\Http\Controllers\v1\supplier;

use App\Events\v1\UploadImageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Exception;
use Illuminate\Support\Facades\Validator;
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
            
            $new_supplier = Supplier::create(array_merge($req->except('company_name','company_contact','image'),array('company'=>array('name'=>$req->company_name,'contact'=>$req->company_contact))));
            $images=array();
            if($req->hasFile('image') && $req->file('image')->isValid()){
                array_push($images,$req->file('image'));
            }
            // dd($images);
            event(new UploadImageEvent($new_supplier,$images,'image'));
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
    public function _update(Supplier $supplier,Request $req):JsonResponse
    {
        try{
            // dd($req->all());
            $req->validate([
                'company_name'=>'required|string|max:255',
                'company_contact'=>'required|string|max:255',
                'first_name'=>'required|string|max:255',
                'last_name'=>'sometimes|nullable|string|max:255',
                'email'=>'required|email|exists:suppliers,email',
                'phone'=>'required|string|max:20|min:9||unique:suppliers,phone',
                'address'=>'sometimes|nullable',
                // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
                'image' => 'nullable',
            ]);
            $supplier->update(array_merge($req->except('company_name','company_contact','image'),array('company'=>array('name'=>$req->company_name,'contact'=>$req->company_contact))));
            return $this->success($supplier);
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
    public function _delete(Supplier $supplier): JsonResponse
    {
        $supplier->delete();
        return $this->success($supplier, 'Supplier Deleted Successfully');
    }
}
