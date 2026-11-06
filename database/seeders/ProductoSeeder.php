<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            ['nombre' => 'Teclado mecanico', 'precio' => 25000, 'stock' => 12],
            ['nombre' => 'Mouse inalambrico', 'precio' => 18000, 'stock' => 20],
            ['nombre' => 'Monitor 24 pulgadas', 'precio' => 145000, 'stock' => 6],
            ['nombre' => 'Auriculares USB', 'precio' => 32000, 'stock' => 3],
        ];

        foreach ($productos as $producto) {
            Producto::firstOrCreate(['nombre' => $producto['nombre']], $producto);
        }
    }
}
