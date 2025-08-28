<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Email Verification",
 *     description="Endpoints para verificação de e-mail"
 * )
 */
class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     * 
     * @OA\Post(
     *     path="/api/email/verification-notification",
     *     operationId="sendEmailVerification",
     *     summary="Reenviar notificação de verificação de e-mail",
     *     description="Envia um novo link de verificação para o e-mail do usuário",
     *     tags={"Email Verification"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Link de verificação enviado",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="verification-link-sent"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirecionamento - E-mail já verificado",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="redirect",
     *                 type="string",
     *                 example="/dashboard"
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
     *         response=403,
     *         description="E-mail já verificado",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Your email is already verified."
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended('/dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['status' => 'verification-link-sent']);
    }
}