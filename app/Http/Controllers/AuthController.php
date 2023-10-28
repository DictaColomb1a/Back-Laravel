<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;



class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'login_tienda', 'login_trabajador']]);
    }
 
 
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register() {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            
        ]);
 
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
 
        $user = new User;
        $user->name = request()->name;
        $user->email = request()->email;
        $user->password = bcrypt(request()->password);
        $user->save();
 
        return response()->json($user, 201);
    }
 
 
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    //login abministrador
    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);
        //codigo que asigna el tipo de usuario abmin
        if (! $token = auth('api')->attempt(["email" => $request -> email, "password" => $request-> password, "type_user" => 3, "state" => 1 ])) {
            return response()->json(['error' => 'No autorizado solo abminsitradores o contraseña incorrecta'], 401);
        }
 
        return $this->respondWithToken($token);
    }
    //login trabajador
    public function login_trabajador(Request $request){
        $credentials = request(['email', 'password']);
        //codigo que asigna el tipo de usuario trabajador
        if(!$token = auth('api')->attempt(["email" => $request->email, "password" => $request->password, "type_user" => 2, "state" => 1])){
            return response()->json(['error' =>'No autorizado solo trabajadores o contraseña incorrecta'], 401);
        }

        return $this->respondWithToken($token);
    }
    //login usuario
    public function login_tienda(Request $request){
        $credentials = request(['email', 'password']);
        //codigo que asigna el tipo de usuario usuario
        if(! $token = auth('api')->attempt(["email" => $request->email, "password" => $request->password, "type_user" => 1,   "state" => 1])){
            return response()->json(['error' => 'no autorizado o contraseña incorrecta '], 401);
        }

        return $this->respondWithToken($token);
    }
 
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }
 
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();
 
        return response()->json(['message' => 'Successfully logged out']);
    }
 
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }
 
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            "user" => [
                "name" => auth('api')->user()->name,
                "email" => auth('api')->user()->email,
                "password" => auth('api')->user()->password,
            ]
        ]);
    }
}
