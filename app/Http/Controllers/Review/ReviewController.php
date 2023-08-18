<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\MainCategory;
use App\Models\Product;
use App\Models\Review;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpParser\NodeVisitor\FirstFindingVisitor;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = Auth::guard('api-user')->user();
            if (!$user->id) return $this->response(False, 403, ['message' => 'You are not authorized']);
            $reviews = Review::where('user_id', $user->id)->get();
            return $this->response(True, 200, $reviews);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "product_id" => 'required',
            "Review" => 'required'
        ]);

        if ($validator->fails()) {
            return $this->response(False, 500, $validator->errors());
        };

        try {

            $product_id = $request->product_id;
            Product::FindOrFail($product_id);

            $new_review = new Review();
            $new_review->product_id = $product_id;
            $new_review->user_id = Auth::guard('api-user')->user()->id;
            $new_review->Review = $request->Review;
            $new_review->submission_date = date('Y-m-d H:t:s');
            $new_review->store_response = 'Non Response Yet';
            $new_review->save();
            return $this->response(True, 201, $new_review);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $productId)
    {
        try {
            if (!$productId) return $this->response(False, 404, ['message' => 'product ID is required']);
            if (!Product::find($productId)) return $this->response(False, 404, ['message' => 'Not Found This Product']);
            $reviews = Review::where('product_id', $productId)->get();
            return $this->response(True, 200, $reviews);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        if (!Auth::guard('api-user')->user()->id == $review->user_id) {
            return $this->response(False, 403, ['message' => 'You are not authorized']);
        }

        $validator = Validator::make($request->all(), [
            'Review' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->response(False, 500, $validator->errors());
        }
        try {
            $review->Review = $request->Review;
            $review->edit = true;
            $review->save();
            return $this->response(True, 200, $review);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }


    public function ResponseAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'review_id' => 'required',
            'store_response' => 'required|nullable',
            'Approved_by_Admin' => 'boolean|required'
        ]);

        if ($validator->fails()) {
            return $this->response(False, 400, $validator->errors());
        }
        try {
            $review = Review::find($request->review_id);
            if (!$review) return $this->response(False, 404, ['message' => 'not Found This Review']);

            $adminId = Auth::guard('api-admin')->user()->id;
            if (!$adminId) return $this->response(False, 403, ['message' => 'unAuthorization']);

            $chickIfProductInSameBranch = $this->chickIfProductInSameBranch($adminId, $review);

            if ($chickIfProductInSameBranch['success'] == false) {
                return $this->response(False, 400, ['message' => $chickIfProductInSameBranch['message']]);
            }

            if ($request->Approved_by_Admin == true) {
                $review->Approved_by_Admin = true;
                $review->save();
            } else {
                $review->store_response = $request->store_response;
                $review->Approved_by_Admin = false;
                $review->save();
            }
            return $this->response(True, 200, $review);
        } catch (Exception $error) {
            return $this->response(False, 500, ['message' => $error->getMessage()]);
        }
    }

    public function chickIfProductInSameBranch($adminId, $review)
    {
        try {

            $branchAdmin = Branch::where('admin_id', $adminId)->first();
            if (!$branchAdmin)
                return [
                    'success' => false,
                    'message' => 'branchAdmin Not Found'
                ];

            $product = Product::find($review->product_id);
            if (!$product) return [
                'success' => false,
                'message' => 'product Not Found'
            ];

            $product->load('BelongMainCategory');
            $branchProduct = $product->BelongMainCategory;
            if (!$branchProduct) return [
                'success' => false,
                'message' => 'branchProduct Not Found'
            ];
            if ($branchProduct->id == $branchAdmin->id) {
                return [
                    'success' => true,
                    'message' => 'true verify'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'You can not Response to a user review beacuse you are not admin this branch'
                ];
            }
        } catch (Exception $error) {
            return
                [
                    'success' => false,
                    'message' => $error->getMessage()
                ];
        }
    }

    public function destroy(Review $review)
    {
        try {
            if (!Auth::guard('api-user')->user()->id == $review->user_id) {
                return $this->response(False, 403, ['message' => 'You are not authorized to delete this Review']);
            }
            $review->delete();
            return $this->response(True, 200, ['message' => 'Review deleted successfully']);
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