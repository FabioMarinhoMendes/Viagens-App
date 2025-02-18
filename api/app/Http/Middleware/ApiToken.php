<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;


class ApiToken extends BaseMiddleware
{
    private string $error;
    private string $status;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        //Verifica se o usuário esta autenticado
        if (!$user = $this->checkJwt()) {
            return response()->json([
                'error' => $this->error,
                'status' => $this->status,
            ]);
        }

        return $next($request);
       
    }


    private function checkJwt()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return $user;
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            $this->error = "tokenError";
            $this->status = "Token inválido";
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            $this->error = "tokenError";
            $this->status = "Token expirado";
        } catch (\Exception $e) {
            $this->error = "tokenError";
            $this->status = "Não foi possível fazer a autenticação desse token";
        }
        
        return false;
    }
}
