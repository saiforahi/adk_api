<?php

namespace App\Http\Controllers\v1\product;

use App\Events\v1\UploadImageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\AdminStock;
use App\Models\Dealer;
use App\Models\DealerProductStock;
use App\Models\DealerType;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $products = Product::with(['stock', 'category', 'brand'])->select('*','products.unit_price as price')->latest()->get();
        $products = $products->transform(function ($item) {
            return $this->getImages($item);
        });
        return $this->success($products);
    }

    /**
     * @param ProductRequest $request
     * @return JsonResponse
     */
    public function store(ProductRequest $request): JsonResponse
    {
        try {
            $data = Arr::except($request->validated(), 'image');
            $data['slug'] = Str::slug($data['name']);
            $product = Product::query()->create($data);
            if ($request->hasFile('image')) {
                $this->uploadImage($request, $product);
            }
            return $this->success($product);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param Product $product
     * @return JsonResponse
     */
    public function show(Product $product): JsonResponse
    {
        $this->getImages($product);
        return $this->success($product->load('brand', 'category', 'sub_category', 'sub_sub_category'));
    }

    /**
     * @param Product $product
     * @param ProductRequest $request
     * @return JsonResponse
     */
    public function update(Product $product, ProductRequest $request): JsonResponse
    {
        try {
            $data = Arr::except($request->validated(), 'image');
            $data['slug'] = Str::slug($data['name']);
            $product->update($data);
            $product->getMedia();
            if ($request->hasFile('image')) {
                $this->uploadImage($request, $product);
            }
            return $this->success($product);
        } catch (\Exception $exception) {
            return $this->failed(null, $exception->getMessage(), 500);
        }
    }

    /**
     * @param Product $product
     * @return JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->getMedia();
        if ($product->media) {
            $product->media->each(function ($item) {
                $item->delete();
            });
        }
        $product->delete();
        return $this->success($product, 'Product Deleted Successfully');
    }

    protected function uploadImage(Request $request, $product)
    {
        if ($product->media) {
            $product->media->each(function ($item) {
                $item->delete();
            });
        }
        $images = [];
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $images[] = $request->file('image');
        }
        event(new UploadImageEvent($product, $images, 'image'));
    }

    protected function getImages($product)
    {
        $product['image'] = null;
        $product->getMedia();
        if ($product->media) {
            $product->media->each(function ($item) use ($product) {
                $product['image'] = $item->getFullUrl();
            });
        }
        $product->makeHidden('media');
        return $product;
    }
    protected function getStockImages($product)
    {
        $product['image'] = null;
        if (count($product->product->getMedia())>0) {
            $product->product->media->each(function ($item) use ($product) {
                $product['image'] = $item->getFullUrl();
            });
        }
        $product->makeHidden('media');
        return $product;
    }
    protected function getDealerStockImages($dealer)
    {
        $dealer['image'] = null;
        if ($dealer->product->media) {
            $dealer->product->media->each(function ($item) use ($dealer) {
                $dealer['image'] = $item->getFullUrl();
            });
        }
        $dealer->makeHidden('media');
        return $dealer;
    }

    public function stockable_product_list_dealer(){
        try{
            $admin_stocks=AdminStock::with(['product','product.media'])->get();
            // $admin_stocks = $admin_stocks->transform(function ($item) {
            //     return $this->getStockImages($item);
            // });
            $dealer_stocks=[];
            $type=Auth::user()->dealer_type_id;
            if($type==1){
                $dealer_stocks = [];
            }
            elseif($type==2){
                $dealer_stocks=Dealer::with('stocked_products')->where('division_id',Auth::user()->division_id)->where('division_id','!=',null)->get();
            }
            elseif($type==3){
                $dealer_stocks=Dealer::with('stocked_products')->where('district_id',Auth::user()->district_id)->where('division_id','!=',null)->get();
            }
            // swicth($type){
            //     case 1:
            //         $dealer_stocks = [];
            //         break;
            //     case 2:
            //         $dealer_stocks=Dealer::with('stocked_products')->where('division_id',Auth::user()->division_id)->get()->toArray();
            //         break;
            //     case 3:
            //         $dealer_stocks=Dealer::with('stocked_products')->where('division_id',Auth::user()->district_id)->get()->toArray();
            //         break;
            //     default:
            //         $dealer_stocks = [];
            // }

            foreach ($dealer_stocks as $value) {
                $stocked_products = $value->stocked_products->transform(function ($item) {
                    return $this->getDealerStockImages($item);
                });
                $value->stocked_products = $stocked_products;
            }

            $data=['ADK'=>$admin_stocks,'dealers'=>$dealer_stocks];
            return $this->success($data, 'Stockable product list for tycoon');  
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }

    public function stockable_product_list_tycoon(){
        try{
            $admin_stocks=AdminStock::with(['product','product.media'])->get();
            $all_upazilla_dealers=Dealer::with(['stocked_products','stocked_products.product.media'])->where('dealer_type_id',DealerType::where('name','Upazilla Dealer')->first()->id)->get();
            // foreach ($all_upazilla_dealers as $value) {
            //     $stocked_products = $value->stocked_products->transform(function ($item) {
            //         return $this->getDealerStockImages($item);
            //     });
            //     $value->stocked_products = $stocked_products;
            // }
            
            $data=['ADK'=>$admin_stocks,'dealers'=>$all_upazilla_dealers];
            return $this->success($data, 'Stockable product list for tycoon');
        }
        catch(Exception $e){
            return $this->failed(null, $e->getMessage(), 500);
        }
    }
}