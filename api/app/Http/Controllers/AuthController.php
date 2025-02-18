<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {    
        // Validação dos dados
        $fields = Validator::make($request->all(), [      
            "email"     => "required|email",
            "password"  => "required|string|min:6"
        ]);  

        // Retorna erros de validação
        if ($fields->fails()) {
            return response()->json(["status" => false, "message" => "Para prosseguir, complete os campos obrigatórios destacados em vermelho. "], 422);   
        }

        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(["status" => false, "message" => "Email ou senha incorretos. Verifique se você digitou as informações corretamente ou se já possui uma conta."], 200);
            }
        } catch (JWTException $e) {
            return response()->json(["status" => false, "message" => "Erro ao fazer login"], 400);
        }      

        return response()->json(["status" => true, "token" => $token, "userName" =>  Auth::user()->name], 200);
       
    }
    

    public function cadastrar(Request $request)
    {    
        // Validação dos dados
        $fields = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required|email",
            "password" => "required|string|min:6"
        ]);        

        // Retorna erros de validação
        if ($fields->fails()) {
            return response()->json(["status" => false, "message" => "Para prosseguir, complete os campos obrigatórios destacados em vermelho. "], 422);   
        }

        //Verifica se o email ja esta cadastrado
        $usuario = User::where([ 
            'email'  => $request["email"],       
        ])->first();

        if ($usuario) {
            // O e-mail já existe
            return response()->json(["status" => false, "message" => "Este e-mail já está cadastrado."], 200);                
        }
        
        // O e-mail é único, pode prosseguir com o cadastro
        $user = User::create([
            'name' => $request["name"],
            'email' => $request["email"],
            'password' => Hash::make($request["password"]),
        ]);        

        return response()->json(["status" => true, "message" => "Usuário cadastrado com sucesso"], 200);      

    }

    public function logout(Request $request)
    {
        try{
            auth("api")->logout();
        }catch(\Exception $e){
            return response()->json(["status" => false, "message" => "Erro ao fazer o logout"], 400);
        }

        return response()->json(["status" => true, "message" => "Logout efetuado com sucesso"], 200);
    }   
    
}
