<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $branchs = Branch::all();
            return response()->json([
                'success' => True,
                'data' => $branchs
            ])->setStatusCode(200);
        } catch (Exception $error) {
            return response()->json([
                'success' => False,
                'data' => [
                    'message' => $error->getMessage()
                ]
            ])->setStatusCode(500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5|unique:branches',
            'address' => 'required|min:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => False,
                'data' => $validator->errors()
            ])->setStatusCode(400);
        }

        $new_branch = new Branch();

        $new_branch->name = $request->name;
        $new_branch->address = $request->address;

        if (Auth::guard("api-admin")->check()) {

            $id = Auth::guard('api-admin')->user()->id;

            // if (!Branch::where('admin_id', $id)->exists()) {

            $new_branch->admin_id = $id;
            // } else {
            // return response()->json([
            //     'success' => False,
            //     'data' => [
            //         'message' => 'The admin alreedy admin on a branch'
            //     ]
            // ])->setStatusCode(400);
            // }
            $new_branch->save();
            return response()->json([
                'success' => True,
                'data' => [
                    'message' => $new_branch
                ]
            ])->setStatusCode(201);
        } else {
            return response()->json([
                'success' => False,
                'data' => [
                    'message' => 'unAuthorization'
                ]
            ])->setStatusCode(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        try {
            $validator = Validator::make($request->all(), [
                'address' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => False,
                    'data' => $validator->errors()
                ])->setStatusCode(400);
            }

            $branch = Branch::FindOrFail($branch->id);

            if ($branch && $branch->admin_id == Auth::guard('api-admin')->user()->id) {
                $branch->update([
                    'address' => $request->get('address')
                ]);
                return response()->json([
                    'sucess' => True,
                    'data' => $branch
                ]);
            } else {
                return response()->json([
                    'success' => False,
                    'data' =>
                    [
                        "message" => "UnAuthoriztion"
                    ]
                ])->setStatusCode(403);
            }
        } catch (Exception $error) {
            return response()->json([
                'success' => False,
                'data' => [
                    "message" => $error->getMessage()
                ]
            ])->setStatusCode(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        try {
            $branch->DeleteWithCategories();
            return $this->response(True, 200, ['message' => 'will delete after 30 day']);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }
    public function restoredeletedFun(int $branchId)
    {
        $branch = Branch::withTrashed()->find($branchId);
        if ($branch) {
            $branch->restore();
            return $this->response(True, 200, ['message' => 'restoreDeleted']);
        } else {
            return $this->response(False, 404, ['message' => 'Branch not found']);
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