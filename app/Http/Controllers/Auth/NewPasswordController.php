<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Password Reset",
 *     description="Endpoints para redefinição de senha"
 * )
 */
class NewPasswordController extends Controller
{
    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     * 
     * @OA\Post(
     *     path="/api/reset-password",
     *     summary="Redefinir senha",
     *     description="Redefine a senha do usuário usando o token de redefinição",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados para redefinição de senha",
     *         @OA\JsonContent(
     *             required={"token", "email", "password", "password_confirmation"},
     *             @OA\Property(
     *                 property="token",
     *                 type="string",
     *                 description="Token de redefinição recebido por e-mail",
     *                 example="a1b2c3d4e5f6g7h8i9j0"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 description="E-mail do usuário",
     *                 example="usuario@example.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 description="Nova senha",
     *                 example="NovaSenha123!",
     *                 minLength=8
     *             ),
     *             @OA\Property(
     *                 property="password_confirmation",
     *                 type="string",
     *                 format="password",
     *                 description="Confirmação da nova senha",
     *                 example="NovaSenha123!",
     *                 minLength=8
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Senha redefinida com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="password-reset"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados inválidos ou token expirado",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The given data was invalid."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         example="This password reset token is invalid."
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Requisição inválida"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->string('password')),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json(['status' => __($status)]);
    }
}