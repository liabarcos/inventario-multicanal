<?php

namespace Tests\Feature;

use App\Events\ProductoActualizado;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ProductoEventoTest extends TestCase
{
    use RefreshDatabase;

    public function test_crear_un_producto_dispara_el_evento(): void
    {
        Event::fake([ProductoActualizado::class]);

        $this->postJson('/api/productos', [
            'nombre' => 'Teclado',
            'precio' => 25000,
            'stock' => 12,
        ])->assertCreated();

        Event::assertDispatched(ProductoActualizado::class, function (ProductoActualizado $evento) {
            return $evento->accion === 'creado' && $evento->producto['nombre'] === 'Teclado';
        });
    }

    public function test_actualizar_el_stock_dispara_el_evento(): void
    {
        $producto = Producto::create(['nombre' => 'Mouse', 'precio' => 18000, 'stock' => 20]);

        Event::fake([ProductoActualizado::class]);

        $this->patchJson('/api/productos/'.$producto->id, ['stock' => 21])->assertOk();

        Event::assertDispatched(ProductoActualizado::class, function (ProductoActualizado $evento) {
            return $evento->accion === 'actualizado' && $evento->producto['stock'] === 21;
        });
    }

    public function test_el_evento_se_difunde_por_el_canal_inventario(): void
    {
        $producto = Producto::create(['nombre' => 'Monitor', 'precio' => 145000, 'stock' => 6]);
        $evento = new ProductoActualizado($producto, 'actualizado');

        $canales = $evento->broadcastOn();

        $this->assertSame('inventario', $canales[0]->name);
        $this->assertSame('producto.actualizado', $evento->broadcastAs());
        $this->assertSame(6, $evento->producto['stock']);
    }
}
