<?php

use App\Http\Controllers\Api\ProductoController;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// REST: CRUD completo de productos.
Route::apiResource('productos', ProductoController::class);

// Endpoint interno que consume el servidor gRPC (protegido por clave compartida).
Route::get('/interno/productos/{producto}', function (Request $request, Producto $producto) {
    $clave = (string) env('GRPC_INTERNAL_KEY', '');
    abort_if($clave === '' || ! hash_equals($clave, (string) $request->header('X-Internal-Key')), 403);

    return response()->json($producto);
});
