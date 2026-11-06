<?php

namespace Tests\Feature;

use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoInternoTest extends TestCase
{
    use RefreshDatabase;

    private function clave(): string
    {
        return (string) env('GRPC_INTERNAL_KEY');
    }

    public function test_rechaza_sin_clave(): void
    {
        $producto = Producto::create(['nombre' => 'Teclado', 'precio' => 25000, 'stock' => 12]);

        $this->getJson('/api/interno/productos/'.$producto->id)->assertForbidden();
    }

    public function test_rechaza_clave_incorrecta(): void
    {
        $producto = Producto::create(['nombre' => 'Teclado', 'precio' => 25000, 'stock' => 12]);

        $this->getJson('/api/interno/productos/'.$producto->id, ['X-Internal-Key' => 'otra-clave'])
            ->assertForbidden();
    }

    public function test_devuelve_el_producto_con_la_clave_correcta(): void
    {
        $producto = Producto::create(['nombre' => 'Teclado', 'precio' => 25000, 'stock' => 12]);

        $this->getJson('/api/interno/productos/'.$producto->id, ['X-Internal-Key' => $this->clave()])
            ->assertOk()
            ->assertJsonFragment(['nombre' => 'Teclado', 'stock' => 12]);
    }

    public function test_devuelve_404_si_el_producto_no_existe(): void
    {
        $this->getJson('/api/interno/productos/999', ['X-Internal-Key' => $this->clave()])
            ->assertNotFound();
    }
}
