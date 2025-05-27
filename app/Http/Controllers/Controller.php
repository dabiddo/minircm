<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Mini CRM",
 *     version="1.0.0",
 *     description="Api Documentation"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     in="header",
 *     name="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="JWT Bearer Token"
 * )
 */
abstract class Controller
{
    //
}
