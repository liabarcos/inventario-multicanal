<?php

namespace Tests\Feature;

use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoRestTest extends TestCase
{
    use RefreshDatabase;

    public function test_lista_productos(): void
    {
        Producto::create(['nombre' => 'Teclado', 'precio' => 25000, 'stock' => 12]);

        $this->getJson('/api/productos')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['nombre' => 'Teclado']);
    }

    public function test_crea_un_producto_y_devuelve_201(): void
    {
        $this->postJson('/api/productos', [
            'nombre' => 'Mouse',
            'precio' => 18000,
            'stock' => 20,
        ])
            ->assertCreated()
            ->assertJsonFragment(['nombre' => 'Mouse', 'stock' => 20]);

        $this->assertDatabaseHas('productos', ['nombre' => 'Mouse']);
    }

    public function test_valida_los_datos_y_devuelve_422(): void
    {
        $this->postJson('/api/productos', ['nombre' => '', 'precio' => -1, 'stock' => -5])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['nombre', 'precio', 'stock']);
    }

    public function test_muestra_un_producto(): void
    {
        $producto = Producto::create(['nombre' => 'Monitor', 'precio' => 145000, 'stock' => 6]);

        $this->getJson('/api/productos/'.$producto->id)
            ->assertOk()
            ->assertJsonFragment(['nombre' => 'Monitor']);
    }

    public function test_devuelve_404_si_no_existe(): void
    {
        $this->getJson('/api/productos/999')->assertNotFound();
    }

    public function test_actualiza_el_stock(): void
    {
        $producto = Producto::create(['nombre' => 'Auriculares', 'precio' => 32000, 'stock' => 3]);

        $this->patchJson('/api/productos/'.$producto->id, ['stock' => 10])
            ->assertOk()
            ->assertJsonFragment(['stock' => 10]);

        $this->assertDatabaseHas('productos', ['id' => $producto->id, 'stock' => 10]);
    }

    public function test_elimina_y_devuelve_204(): void
    {
        $producto = Producto::create(['nombre' => 'Borrar', 'precio' => 1000, 'stock' => 1]);

        $this->deleteJson('/api/productos/'.$producto->id)->assertNoContent();

        $this->assertDatabaseMissing('productos', ['id' => $producto->id]);
    }
}
