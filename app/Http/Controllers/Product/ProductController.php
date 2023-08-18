<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShowMainCategory;
use App\Http\Resources\ShowProduct;
use App\Http\Resources\ShowSubCategory;
use App\Models\Branch;
use App\Models\MainCategory;
use App\Models\Product;
use App\Models\SubCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Type\Integer;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    //all product in same sub category
    public function index(SubCategory $subCategory)
    {
        try {
            $all_product = Product::where('sub_category_id', $subCategory->id)->get();
            return $this->response(True, 200, $all_product);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }

    //all product in same main category
    public function allProductSameMainCategory(Int $mainCategoryId)
    {
        try {
            $products = Product::where('main_category_id', $mainCategoryId)->get();
            $BelongSubCategory = $products->load('BelongSubCategory');
            return $this->response(True, 200, $BelongSubCategory);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }
    public function allProductSameBranch(Int $branchId)
    {
        try {
            $mainCategories = MainCategory::where('branch_id', $branchId)->get();
            $ArrayId = $mainCategories->pluck('id')->toArray();
            $products =  Product::whereIn('main_category_id', $ArrayId)->get();
            $BelongSubCategory = $products->load('BelongSubCategory');
            return $this->response(True, 200, $BelongSubCategory);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }
    public function allProduct()
    {
        try {
            $product = Product::all();
            $product->load(['BelongSubCategory', 'BelongMainCategory']);
            return $this->response(True, 200, $product);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $sub_category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response(False, 400, $validator->errors());
        }

        try {

            $sub_category_found = SubCategory::FindOrFail($sub_category);
            if (!$sub_category_found) return $this->response(False, 400, ['message' => 'Not Found This Sub Category !!']);


            $produt = new Product();
            $file = $request->file('image');

            $produt->name = $request->name;
            $produt->price = $request->price;
            $produt->sub_category_id = $sub_category;
            $produt->main_category_id = $sub_category_found->main_category_id;

            $sub_category_found->load('BelongMainCategory');
            $belongMainCategory = $sub_category_found->BelongMainCategory;
            $produt->image_path = '/uploads/' . $belongMainCategory->branch_id . '/' . $belongMainCategory->id . '/' . $sub_category . '/' . $file->getClientOriginalName();
            $destinationPath = 'uploads/' . $belongMainCategory->branch_id . '/' . $belongMainCategory->id . '/' . $sub_category;
            if ($file->move($destinationPath, $file->getClientOriginalName())) {
                $produt->save();
                return $this->response(True, 201, $produt);
            } else {
                return $this->response(False, 400, ['message' => 'error with move file']);
            }
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SubCategory $subCategory, Product $product)
    {
        try {
            if ($product->sub_category_id != $subCategory->id) {
                return $this->response(False, 404, ['message' => 'Not Found']);
            }
            $product->load('BelongSubCategory');
            $BelongSubCategory = $product->BelongSubCategory;

            $subCategory->load('BelongMainCategory');
            $BelongMainCategory = $subCategory->BelongMainCategory;

            $response_data = [
                'productData' =>  new ShowProduct($product),
                'subCategoryData' => new ShowSubCategory($BelongSubCategory),
                'MainCategoryData' => new ShowMainCategory($BelongMainCategory),
            ];
            return $this->response(True, 200, $response_data);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }


    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
    public function response($success, $status, $data)
    {
        return  response()->json([
            'success' => $success,
            'status' => $status,
            'data' =>  $data
        ])->setStatusCode($status);
    }
}