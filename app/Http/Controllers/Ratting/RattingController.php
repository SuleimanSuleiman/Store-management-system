<?php

namespace App\Http\Controllers\Ratting;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Ratting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RattingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $userId = Auth::guard('api-user')->user();
            if (!$userId) return $this->response(False, 403, ['message' => 'unAuthorization']);
            $payload = Ratting::where('user_id', $userId->id)->get();
            $payload->load('Product');
            return $this->response(True, 200, $payload);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'ratting' => 'required|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return $this->response(False, 400, $validator->errors());
        }
        try {

            $product_id = $request->product_id;
            Product::FindOrFail($product_id);

            $userId = Auth::guard('api-user')->user()->id;
            if (!$userId) return $this->response(False, 403, ['message' => 'unAuthorization']);

            $already_ratting = Ratting::where('product_id', $product_id)->where('user_id', $userId)->exists();
            if ($already_ratting) return $this->response(False, 400, ['message' => 'your already ratting this product']);

            $new_ratting = new Ratting();
            $new_ratting->user_id = $userId;
            $new_ratting->ratting = $request->ratting;
            $new_ratting->product_id = $product_id;
            $new_ratting->save();


            return $this->response(True, 201, $new_ratting);
        } catch (Exception $error) {

            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }


    public function show(int $productId)
    {
        try {
            $Product = Ratting::where('product_id', $productId)->get();
            return $this->response(True, 200, $Product);
        } catch (Exception $error) {

            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ratting $ratting)
    {

        if (!Auth::guard('api-user')->user()->id == $ratting->user_id) {
            return $this->response(False, 403, ['message' => 'unAuthorization']);
        }

        $validator = Validator::make($request->all(), [
            'ratting' => 'required|integer|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return $this->response(False, 500, $validator->errors());
        }
        try {
            $ratting->ratting = $request->ratting;
            $ratting->save();
            return $this->response(True, 200, $ratting);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }

    public function destroy(Ratting $ratting)
    {
        try {
            if (!Auth::guard('api-user')->user()->id == $ratting->user_id) {
                return $this->response(False, 403, ['message' => 'You are not authorized to delete this Ratting']);
            }
            $ratting->delete();
            return $this->response(True, 200, ['message' => 'Ratting deleted successfully']);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
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
