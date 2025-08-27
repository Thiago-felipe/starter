<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints para autenticação de usuários"
 * )
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     * 
     * @OA\Post(
     *     path="/api/login",
     *     summary="Realizar login",
     *     description="Autentica um usuário e retorna a sessão",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Credenciais de login",
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 example="usuario@example.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 example="senha123"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Login realizado com sucesso",
     *         @OA\Header(
     *             header="Set-Cookie",
     *             description="Cookie de sessão configurado",
     *             @OA\Schema(type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Credenciais inválidas",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The provided credentials are incorrect."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="The provided credentials are incorrect.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Dados de entrada inválidos"
     *     )
     * )
     */
    public function store(LoginRequest $request): Response
    {
        $request->authenticate();

        $request->session()->regenerate();

        return response()->noContent();
    }

    /**
     * Destroy an authenticated session.
     * 
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Realizar logout",
     *     description="Encerra a sessão do usuário autenticado",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=204,
     *         description="Logout realizado com sucesso",
     *         @OA\Header(
     *             header="Set-Cookie",
     *             description="Cookie de sessão removido",
     *             @OA\Schema(type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated."
     *             )
     *         )
     *     )
     * )
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}