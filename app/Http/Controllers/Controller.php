<?php

namespace App\Http\Controllers;
use OpenApi\Annotations as OA;
 /**
 * @OA\Info(
 * version="1.0.0",
 * title="StockManager API Documentation",
 * description="Documentation des points d'entrée pour la gestion de stock et le scanner mobile.",
 * @OA\Contact(email="ton-email@exemple.com")
 * )
 * @OA\Server(
 * url=L5_SWAGGER_CONST_HOST,
 * description="Serveur API Principal"
 * )
 */
abstract class Controller
{
    //
}
