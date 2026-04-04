<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Social Media API",
    version: "1.0.0",
    description: "Documentation for Social Media API",
    contact: new OA\Contact(email: "admin@example.com"),
    
)]
#[OA\Server(url: 'http://localhost:8081/api', description: "Local Server")]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
)]
abstract class Controller
{
    //
}