<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

/**
 * @OA\Tag(
 *     name="Email Verification",
 *     description="Endpoints para verificação de e-mail"
 * )
 */
class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     * 
     * @OA\Get(
     *     path="/api/verify-email/{id}/{hash}",
     *     operationId="verifyEmail",
     *     summary="Verificar endereço de e-mail",
     *     description="Verifica o endereço de e-mail do usuário usando o link enviado por e-mail",
     *     tags={"Email Verification"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do usuário",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="hash",
     *         in="path",
     *         required=true,
     *         description="Hash de verificação",
     *         @OA\Schema(type="string", example="a1b2c3d4e5f6g7h8i9j0")
     *     ),
     *     @OA\Parameter(
     *         name="expires",
     *         in="query",
     *         required=false,
     *         description="Timestamp de expiração",
     *         @OA\Schema(type="integer", example=1640995200)
     *     ),
     *     @OA\Parameter(
     *         name="signature",
     *         in="query",
     *         required=false,
     *         description="Assinatura da URL",
     *         @OA\Schema(type="string", example="abc123def456")
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="E-mail verificado com sucesso - redirecionamento",
     *         @OA\Header(
     *             header="Location",
     *             description="URL de redirecionamento para o frontend",
     *             @OA\Schema(type="string", example="http://frontend.app/dashboard?verified=1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Link inválido ou expirado",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Invalid verification link."
     *             )
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
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Requisição inválida"
     *     )
     * )
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(
                config('app.frontend_url').'/dashboard?verified=1'
            );
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(
            config('app.frontend_url').'/dashboard?verified=1'
        );
    }
}