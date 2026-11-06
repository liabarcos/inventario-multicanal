<?php

namespace Tests\Feature;

use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoGraphQLTest extends TestCase
{
    use RefreshDatabase;

    public function test_consulta_productos(): void
    {
        Producto::create(['nombre' => 'Teclado', 'precio' => 25000, 'stock' => 12]);

        $this->postJson('/graphql', [
            'query' => '{ productos { id nombre precio stock } }',
        ])
            ->assertOk()
            ->assertJsonPath('data.productos.0.nombre', 'Teclado')
            ->assertJsonPath('data.productos.0.stock', 12);
    }

    public function test_crea_un_producto_con_mutacion(): void
    {
        $this->postJson('/graphql', [
            'query' => 'mutation { crearProducto(nombre: "Mouse", precio: 18000, stock: 20) { id nombre stock } }',
        ])
            ->assertOk()
            ->assertJsonPath('data.crearProducto.nombre', 'Mouse')
            ->assertJsonPath('data.crearProducto.stock', 20);

        $this->assertDatabaseHas('productos', ['nombre' => 'Mouse']);
    }

    public function test_actualiza_el_stock_con_mutacion(): void
    {
        $producto = Producto::create(['nombre' => 'Monitor', 'precio' => 145000, 'stock' => 6]);

        $this->postJson('/graphql', [
            'query' => "mutation { actualizarStock(id: {$producto->id}, stock: 8) { id stock } }",
        ])
            ->assertOk()
            ->assertJsonPath('data.actualizarStock.stock', 8);

        $this->assertDatabaseHas('productos', ['id' => $producto->id, 'stock' => 8]);
    }

    public function test_valida_datos_en_la_mutacion(): void
    {
        $this->postJson('/graphql', [
            'query' => 'mutation { crearProducto(nombre: "", precio: -1, stock: -5) { id } }',
        ])
            ->assertOk()
            ->assertJsonStructure(['errors']);

        $this->assertDatabaseCount('productos', 0);
    }
}
