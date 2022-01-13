<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use App\Models\UserOrder;
use App\Models\WishList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Register user.
     *
     * @param Illuminate\Http\Request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fname' => 'required|alpha',
            'lname' => 'required|alpha',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|regex:/^[\w-]+$/|min:8|max:12',
            'password_confirmation' => 'required',
        ], [
            'fname.required' => 'First name is required',
            'fname.alpha' => 'Only alphabets are allowed.',
            'lname.required' => 'Last name is required',
            'lname.alpha' => 'Only alphabets are allowed.',
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email address',
            'email.unique' => 'Email address is already taken',
            'password.required' => 'Password is required',
            'passowrd.regex' => 'Only alphanumeric characters are allowed',
            'password.min' => 'Minimum password length should be 8 characters',
            'password.max' => 'Maximum password length should be 12 characters',
            'password_confirmation.required' => 'Re-enter password',
            // 'password_confirmation.confirmed' => 'Password does not match',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'firstname' => 'sanket  ',
            'lastname' => $request->lname,
            'email' => $request->email,
            'role_id' => 5,
            'active' => true,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'access_token' => JWTAuth::fromUser($user),
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('api')->factory()->getTTL() * 60,
            'user' => $user->firstname,
            'user_id' => $user->id,
        ], 201);
    }

    /**
     * login user
     *
     * @param Illuminate\Http\Request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8|max:12',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->guard('api')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token, auth()->guard('api')->user());
    }

    /**
     * Logout user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->guard('api')->logout();

        return response()->json(['message' => 'User successfully logged out.']);
    }

    /**
     * Refresh token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->guard('api')->refresh());
    }

    /**
     * Get user profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return response()->json(auth()->guard('api')->user());
    }

    /**
     * update user profile.
     * 
     * @param Illuminate\Http\Request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fname' => 'required|alpha',
            'lname' => 'required|alpha',
            'contact' => 'regex:/^[6-9][0-9]{9}$/'
        ], [
            'fname.required' => 'First name is required',
            'fname.alpha' => 'Only alphabets are allowed.',
            'lname.required' => 'Last name is required',
            'lname.alpha' => 'Only alphabets are allowed.',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $user = User::find($request->id);
            $user->firstname = $request->fname;
            $user->lastname = $request->lname;
            if ($user->save()) {
                return response()->json(['message' => 'Your account details updated successfully.']);
            }
        }
    }

    /**
     * update user profile.
     * 
     * @param Illuminate\Http\Request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current' => 'required|regex:/^[\w-]+$/|min:8|max:12',
            'new' => 'required|regex:/^[\w-]+$/|min:8|max:12',
            'confirm' => 'required',
        ], [
            'current.required' => 'Current password is required',
            'current.regex' => 'Only alphanumeric characters are allowed',
            'new.required' => 'New password is required',
            'new.regex' => 'Only alphanumeric characters are allowed',
            'confirm.required' => 'Confirm password is required',
        ]);
        if ($validator) {
            $user = User::find($request->id);
            if (Hash::check($request->current, $user->password)) {
                $user->password = Hash::make($request->new);
                $user->save();
                return response()->json(['message' => 'Password Change Successfully.', 'status' => 1]);
            } else {
                return response()->json(['message' => 'Password is Incorrect.', 'status' => 0]);
            }
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('api')->factory()->getTTL() * 60,
            'user' => $user->firstname,
            'user_id' => $user->id,
        ]);
    }

    /**
     * .
     *
     * @param  Illuminate\Http\Request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToWishlist(Request $request)
    {
        $wishlist_product = new WishList();
        $wishlist_product->user_id = $request->user_id;
        $wishlist_product->product_name = $request->product_name;
        $wishlist_product->product_image = $request->product_image;
        $wishlist_product->product_price = $request->product_price;
        $wishlist_product->product_id = $request->product_id;
        // $wishlist_product->product_brand = $request->product_brand;
        $wishlist_product->save();
        return response()->json(['message' => ' added to wishlist'], 200);
    }

    /**
     * .
     *
     * @param  Illuminate\Http\Request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userWishlist($user_id)
    {
        return response()->json(['product' => WishList::where('user_id', $user_id)->get(), 'message' => 'Fetched Wishlist'], 200);
    }

    /**
     * .
     *
     * @param  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteWishlist($id)
    {
        $product = Wishlist::find($id);
        $product->delete();
        return response()->json(['message' => 'Wishlist product deleted successfully.'], 200);
    }
    /**
     * 
     * @param  Illuminate\Http\Request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'product_id' => 'required',
            'product' => 'required',
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required|email',
            'contact' => 'required|regex:/^[6-9][0-9]{9}$/',
            'address' => 'required',
            'price' => 'numeric',
            'quantity' => 'numeric',
            'payment_method' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $order = new UserOrder();
            $order->order_id = substr(time(), 0, 4) . "-" . substr(rand(), 0, 4) . "-" . substr(rand(), 0, 4);
            $order->user_id = $request->user_id;
            $order->product_id = $request->product_id;
            $order->user_name = $request->fname . " " . $request->lname;
            $order->status = "Yet to be Dispatched.";
            $order->product = $request->product;
            $order->user_email = $request->email;
            $order->user_contact = $request->contact;
            $order->user_address = $request->address;
            $order->order_price = $request->price;
            $order->order_quantity = $request->quantity;
            $order->coupon = $request->coupon;
            $order->payment_method = $request->payment_method;
            $order->save();
            return response()->json(['message' => 'Order registered'], 201);
        }
    }
}
