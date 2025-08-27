<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Laravel API Documentation",
 *     description="Documentação da API Laravel para integração com frontend",
 *     @OA\Contact(
 *         email="admin@example.com",
 *         name="API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Local development server"
 * )
 * @OA\Server(
 *     url="https://api.example.com",
 *     description="Production server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Use o token JWT para autenticação"
 * )
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints para autenticação de usuários"
 * )
 * @OA\Tag(
 *     name="Registration",
 *     description="Endpoints para registro de novos usuários"
 * )
 * @OA\Tag(
 *     name="Email Verification",
 *     description="Endpoints para verificação de e-mail"
 * )
 * @OA\Tag(
 *     name="Password Reset",
 *     description="Endpoints para redefinição de senha"
 * )
 */
class SwaggerController extends Controller
{
    // Este controller serve apenas para documentação Swagger
}