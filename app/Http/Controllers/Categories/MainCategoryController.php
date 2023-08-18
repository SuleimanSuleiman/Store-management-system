<?php

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\MainCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MainCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $main_categories = MainCategory::all();
            return $this->response(True, 200, $main_categories);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }
    public function AllMainSameBranch(int $branch)
    {
        try {
            $main_categories = MainCategory::where('branch_id', $branch)->get();
            return $this->response(True, 200, $main_categories);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->response(False, 400, $validator->errors());
            }

            $branch_id =  DB::table('branches')->select('id')->where('admin_id', Auth::guard('api-admin')->user()->id)->first();
            if (!$branch_id) {
                return $this->response(False, 400, ['message' => "Error when fetch branch ID"]);
            }

            $category = new MainCategory();
            $category->name = $request->name;
            $category->branch_id = $branch_id->id;
            $category->save();
            return $this->response(True, 201, $category);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MainCategory $mainCategory)
    {
        try {
            $mainCategory->load('subcategories', 'BelongBranch');
            return $this->response(True, 200, $mainCategory);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }


    public function destroy(MainCategory $mainCategory)
    {
        try {
            $mainCategory->DeleteWithSubCategories();
            return $this->response(True, 200, ['message' => 'will delete after 30 day']);
        } catch (Exception $error) {

            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }
    public function restoredeletedFun(int $mainCategoryId)
    {
        $branch = MainCategory::withTrashed()->find($mainCategoryId);
        if ($mainCategoryId) {
            $branch->restore();
            return $this->response(True, 200, ['message' => 'restoreDeleted']);
        } else {
            return $this->response(False, 404, ['message' => 'Main Category not found']);
        }
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