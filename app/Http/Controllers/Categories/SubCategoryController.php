<?php

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShowMainCategory;
use App\Http\Resources\ShowSubCategory;
use App\Models\MainCategory;
use App\Models\SubCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        try {
            $sub_categories = SubCategory::all();
            return $this->response(True, 200, $sub_categories);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }
    public function AllSubOnly($id)
    {
        try {
            $sub_categories = SubCategory::all()->where('main_category_id', $id);
            return $this->response(True, 200, $sub_categories);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'type' => 'required',
                'Desc' => 'required|min:10',
                'model' => 'required',
                'main_category_id' => 'required|exists:main_categories,id',
                'size' => 'required',
                'market' => 'required|in:foreign,national,national-foreign,foreign-national',
            ]);

            if ($validator->fails()) {
                return $this->response(False, 400, $validator->errors());
            }

            $category = new SubCategory();

            $category->name = $request->name;
            $category->type = $request->type;
            $category->Desc = $request->Desc;
            $category->model = $request->model;
            $category->size = $request->size;
            $category->market = $request->market;
            $category->main_category_id = $request->main_category_id;

            $category->save();

            return $this->response(True, 201, $category);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SubCategory $subCategory)
    {
        try {
            $subCategory->load('BelongMainCategory');
            $BelongMainCategory = $subCategory->BelongMainCategory;
            $response_data =  [
                'subCategoryData' => new ShowSubCategory($subCategory),
                'MainCategoryData' => new ShowMainCategory($BelongMainCategory),
            ];
            return $this->response(True, 200, $response_data);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }



    public function update(Request $request, SubCategory $subCategory)
    {
        try {

            $category = SubCategory::FindOrFail($subCategory->id);
            $category->update($request->all());
            $category->save();
            return $this->response(True, 200, $subCategory);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubCategory $subCategory)
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