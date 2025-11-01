<?php
declare(strict_types=1);

namespace Tests\Unit\Modelo;

use Modelo\Productos;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Modelo\Traits\MaliciousDataProvidersTrait;

class ProductosStub extends Productos
{
    public function __construct() {}
    public function conectarBD()   { /* no-op */ }
    public function desconectarBD(){ /* no-op */ }
}

#[Group('unit'),Group('Productos')]
final class ProductosTest extends TestCase
{
    use MaliciousDataProvidersTrait;
    
    private ProductosStub $sut;

    protected function setUp(): void
    {
        $this->sut = new ProductosStub();
    }

    // ========================================
    // PRUEBAS DE ESTADO INICIAL
    // ========================================
    
    public function testInicialmenteGettersSonNull(): void
    {
        $this->assertNull($this->sut->getNombre());
        $this->assertNull($this->sut->getMarca());
        $this->assertNull($this->sut->getPresentacion());
        $this->assertNull($this->sut->getCantPresentacion());
        $this->assertNull($this->sut->getCosto());
        $this->assertNull($this->sut->getGanancia());
        $this->assertNull($this->sut->getExcento());
    }

    // ========================================
    // PRUEBAS VÁLIDAS - CASOS QUE DEBEN PASAR
    // ========================================

    // DataProvider para nombres válidos
    public static function nombresValidos(): array
    {
        return [
            'minimo 2' => ['Hi'],
            'medio' => ['Producto ABC'],
            'maximo 50' => [str_repeat('a', 50)],
            'con acentos' => ['Producto con acentos'],
        ];
    }

    #[DataProvider('nombresValidos')]
    public function testSetNombreConValorValidoNoLanza(string $nombre): void
    {
        $this->sut->setNombre($nombre);
        $this->sut->check();
        $this->assertSame($nombre, $this->sut->getNombre());
    }

    // DataProvider para marcas válidas (opcional)
    public static function marcasValidas(): array
    {
        return [
            'numero entero' => ['123'],
            'numero grande' => ['9999'],
            'vacio' => [''], // Marca es opcional
            'null' => [null], // Marca puede ser null
        ];
    }

    #[DataProvider('marcasValidas')]
    public function testSetMarcaConValorValidoNoLanza($marca): void
    {
        $this->sut->setMarca($marca);
        $this->sut->check();
        $this->assertSame($marca === '' ? null : $marca, $this->sut->getMarca());
    }

    // DataProvider para presentaciones válidas (opcional)
    public static function presentacionesValidas(): array
    {
        return [
            'minimo 2' => ['Hi'],
            'medio' => ['Presentación ABC'],
            'maximo 50' => [str_repeat('a', 50)],
            'vacio' => [''], // Presentación es opcional
        ];
    }

    #[DataProvider('presentacionesValidas')]
    public function testSetPresentacionConValorValidoNoLanza(string $presentacion): void
    {
        $this->sut->setPresentacion($presentacion);
        $this->sut->check();
        $this->assertSame($presentacion === '' ? null : $presentacion, $this->sut->getPresentacion());
    }

    // DataProvider para cantidades válidas (opcional)
    public static function cantidadesValidas(): array
    {
        return [
            'minimo 1' => ['1'],
            'medio' => ['12 unidades'],
            'maximo 20' => [str_repeat('a', 20)],
            'vacio' => [''], // Cantidad es opcional
        ];
    }

    #[DataProvider('cantidadesValidas')]
    public function testSetCantPresentacionConValorValidoNoLanza(string $cantidad): void
    {
        $this->sut->setCantPresentacion($cantidad);
        $this->sut->check();
        $this->assertSame($cantidad === '' ? null : $cantidad, $this->sut->getCantPresentacion());
    }

    // DataProvider para costos válidos
    public static function costosValidos(): array
    {
        return [
            'cero' => ['0'],
            'entero' => ['100'],
            'decimal' => ['99.99'],
            'vacio' => [''], // Se convierte a 0
        ];
    }

    #[DataProvider('costosValidos')]
    public function testSetCostoConValorValidoNoLanza(string $costo): void
    {
        $this->sut->setCosto($costo);
        $this->sut->check();
        
        if ($costo === '') {
            $this->assertEquals(0, $this->sut->getCosto());
        } else {
            $this->assertEquals((float)$costo, $this->sut->getCosto());
        }
    }

    // DataProvider para ganancias válidas
    public static function gananciasValidas(): array
    {
        return [
            'cero' => ['0'],
            'entero' => ['25'],
            'vacio' => [''], // Se convierte a 0
        ];
    }

    #[DataProvider('gananciasValidas')]
    public function testSetGananciaConValorValidoNoLanza(string $ganancia): void
    {
        $this->sut->setGanancia($ganancia);
        $this->sut->check();
        
        if ($ganancia === '') {
            $this->assertEquals(0, $this->sut->getGanancia());
        } else {
            $this->assertEquals((float)$ganancia, $this->sut->getGanancia());
        }
    }

    // ========================================
    // PRUEBAS INVÁLIDAS - CASOS QUE DEBEN FALLAR
    // ========================================

    // DataProvider para nombres inválidos
    public static function nombresInvalidos(): array
    {
        return [
            'vacio' => [''],
            'solo 1 char' => ['A'],
            'mas de 50' => [str_repeat('a', 51)],
        ];
    }

    #[DataProvider('nombresInvalidos')]
    public function testSetNombreConValorInvalidoLanza(string $nombre): void
    {
        $this->sut->setNombre($nombre);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta nombres inválidos sin validación: '$nombre'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // DataProvider para marcas inválidas
    public static function marcasInvalidas(): array
    {
        return [
            'no numerico' => ['abc'],
            'mas de 9999 digitos' => [str_repeat('1', 10000)],
        ];
    }

    #[DataProvider('marcasInvalidas')]
    public function testSetMarcaConValorInvalidoLanza(string $marca): void
    {
        $this->sut->setMarca($marca);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta marcas inválidas sin validación: '$marca'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // DataProvider para presentaciones inválidas
    public static function presentacionesInvalidas(): array
    {
        return [
            'solo 1 char' => ['A'],
            'mas de 50' => [str_repeat('a', 51)],
        ];
    }

    #[DataProvider('presentacionesInvalidas')]
    public function testSetPresentacionConValorInvalidoLanza(string $presentacion): void
    {
        $this->sut->setPresentacion($presentacion);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta presentaciones inválidas sin validación: '$presentacion'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // DataProvider para cantidades inválidas
    public static function cantidadesInvalidas(): array
    {
        return [
            'mas de 20' => [str_repeat('a', 21)],
        ];
    }

    #[DataProvider('cantidadesInvalidas')]
    public function testSetCantPresentacionConValorInvalidoLanza(string $cantidad): void
    {
        $this->sut->setCantPresentacion($cantidad);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta cantidades inválidas sin validación: '$cantidad'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // DataProvider para costos inválidos
    public static function costosInvalidos(): array
    {
        return [
            'negativo' => ['-10'],
            'mas de 20 chars' => [str_repeat('1', 21)],
        ];
    }

    #[DataProvider('costosInvalidos')]
    public function testSetCostoConValorInvalidoLanza(string $costo): void
    {
        $this->sut->setCosto($costo);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta costos inválidos sin validación: '$costo'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // DataProvider para ganancias inválidas
    public static function gananciasInvalidas(): array
    {
        return [
            'negativo' => ['-10'],
            'mas de 20 chars' => [str_repeat('1', 21)],
        ];
    }

    #[DataProvider('gananciasInvalidas')]
    public function testSetGananciaConValorInvalidoLanza(string $ganancia): void
    {
        $this->sut->setGanancia($ganancia);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta ganancias inválidas sin validación: '$ganancia'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // ========================================
    // PRUEBAS DE PRODUCTO MÍNIMO
    // ========================================

    // DataProvider para productos mínimos válidos
    public static function productosMinimosValidos(): array
    {
        return [
            'solo nombre y excento' => [
                'nombre' => 'Producto Mínimo',
                'excento' => 1
            ],
            'con excento 0' => [
                'nombre' => 'Producto Sin IVA',
                'excento' => 0
            ],
            'nombre largo' => [
                'nombre' => str_repeat('a', 50),
                'excento' => 1
            ],
        ];
    }

    #[DataProvider('productosMinimosValidos')]
    public function testProductoMinimoValidoNoLanza(string $nombre, int $excento): void
    {
        $this->sut->setNombre($nombre);
        $this->sut->setExcento($excento);
        
        $this->sut->check(); // No debe lanzar excepción
        
        $this->assertSame($nombre, $this->sut->getNombre());
        $this->assertSame($excento, $this->sut->getExcento());
        // Los demás campos pueden ser null
        $this->assertNull($this->sut->getMarca());
        $this->assertNull($this->sut->getPresentacion());
        $this->assertNull($this->sut->getCantPresentacion());
        $this->assertNull($this->sut->getCosto());
        $this->assertNull($this->sut->getGanancia());
    }

    // ========================================
    // PRUEBAS DE NUEVAS PRESENTACIONES
    // ========================================

    // DataProvider para nuevas presentaciones válidas
    public static function nuevasPresentacionesValidas(): array
    {
        return [
            'presentacion basica' => [
                'presentacion' => 'Pieza',
                'cantidad' => '1',
                'costo' => '25.50',
                'ganancia' => '30',
                'excento' => 1
            ],
            'presentacion con decimales' => [
                'presentacion' => 'Bloque',
                'cantidad' => '4.5',
                'costo' => '100.00',
                'ganancia' => '25',
                'excento' => 0
            ],
            'presentacion minima' => [
                'presentacion' => 'Unidad',
                'cantidad' => '',
                'costo' => '',
                'ganancia' => '',
                'excento' => 1
            ],
        ];
    }

    #[DataProvider('nuevasPresentacionesValidas')]
    public function testNuevaPresentacionValidaNoLanza(string $presentacion, string $cantidad, string $costo, string $ganancia, int $excento): void
    {
        $this->sut->setPresentacion($presentacion);
        $this->sut->setCantPresentacion($cantidad);
        $this->sut->setCosto($costo);
        $this->sut->setGanancia($ganancia);
        $this->sut->setExcento($excento);
        
        $this->sut->check(); // No debe lanzar excepción
        
        $this->assertSame($presentacion, $this->sut->getPresentacion());
        $this->assertSame($cantidad === '' ? null : $cantidad, $this->sut->getCantPresentacion());
        $this->assertEquals($costo === '' ? 0 : (float)$costo, $this->sut->getCosto());
        $this->assertEquals($ganancia === '' ? 0 : (float)$ganancia, $this->sut->getGanancia());
        $this->assertSame($excento, $this->sut->getExcento());
    }

    // ========================================
    // PRUEBAS EDGE CASES Y MALICIOSAS
    // ========================================

    // DataProvider para casos límite
    public static function casosLimite(): array
    {
        return [
            'nombre minimo exacto' => [str_repeat('a', 2)],
            'nombre maximo exacto' => [str_repeat('a', 50)],
            'presentacion minimo exacto' => [str_repeat('a', 2)],
            'presentacion maximo exacto' => [str_repeat('a', 50)],
            'cantidad minimo exacto' => [str_repeat('a', 1)],
            'cantidad maximo exacto' => [str_repeat('a', 20)],
            'unicode' => ['áéíóú'],
            'emojis' => ['😀😀'],
            'caracteres especiales' => ['!@#$%^&*()'],
        ];
    }

    #[DataProvider('casosLimite')]
    public function testCasosLimite($valor): void
    {
        $this->sut->setNombre($valor);
        
        try {
            $this->sut->check();
            $this->assertSame($valor, $this->sut->getNombre());
        } catch (\Exception $e) {
            $this->assertStringContainsString('nombre', $e->getMessage());
        }
    }

    // DataProvider para casos maliciosos - usando trait

    #[DataProvider('casosMaliciosos')]
    public function testCasosMaliciosos($valor): void
    {
        $this->sut->setNombre($valor);
        
        try {
            $this->sut->check();
            $this->assertSame($valor, $this->sut->getNombre());
        } catch (\Exception $e) {
            $this->assertStringContainsString('nombre', $e->getMessage());
        }
    }

    // DataProvider para tipos de datos problemáticos - usando trait

    #[DataProvider('tiposDatosProblematicos')]
    public function testTiposDatosProblematicos($valor): void
    {
        // DEFECTO DOCUMENTADO: El sistema no valida tipos de datos
        if (is_array($valor) || is_object($valor) || is_resource($valor) || is_callable($valor)) {
            $this->expectException(\TypeError::class);
            $this->sut->setNombre($valor);
        } else {
            // DEFECTO: Float e Integer se convierten a string y luego se validan
            $this->sut->setNombre($valor);
            
            try {
                $this->sut->check();
                $this->fail("DEFECTO: El sistema acepta tipos incorrectos como " . gettype($valor) . " sin validación");
            } catch (\Exception $e) {
                // El sistema convierte float/integer a string y luego valida
                $this->assertStringContainsString('nombre', $e->getMessage());
            }
        }
    }

    // ========================================
    // PRUEBAS DE setDatos
    // ========================================

    // DataProvider para setDatos válidos
    public static function datosValidos(): array
    {
        return [
            'producto completo' => [[
                'nombre' => 'Producto Test',
                'marca' => '123',
                'presentacion' => 'Presentación Test',
                'cant_presentacion' => '10 unidades',
                'costo' => '100.50',
                'ganancia' => '25',
                'excento' => 1
            ]],
            'producto minimo' => [[
                'nombre' => 'Producto Básico',
                'excento' => 0
            ]],
            'nueva presentacion' => [[
                'presentacion' => 'Caja',
                'cant_presentacion' => '12',
                'costo' => '150.75',
                'ganancia' => '40',
                'excento' => 1
            ]],
        ];
    }

    #[DataProvider('datosValidos')]
    public function testSetDatosConDatosValidosNoLanza(array $datos): void
    {
        $this->sut->setDatos($datos);
        $this->sut->check();
        
        // Verificar que los datos se asignaron correctamente
        if (isset($datos['nombre'])) {
            $this->assertSame($datos['nombre'], $this->sut->getNombre());
        }
        if (isset($datos['marca'])) {
            $this->assertSame($datos['marca'], $this->sut->getMarca());
        }
        if (isset($datos['presentacion'])) {
            $this->assertSame($datos['presentacion'], $this->sut->getPresentacion());
        }
        if (isset($datos['cant_presentacion'])) {
            $this->assertSame($datos['cant_presentacion'], $this->sut->getCantPresentacion());
        }
        if (isset($datos['costo'])) {
            $this->assertEquals((float)$datos['costo'], $this->sut->getCosto());
        }
        if (isset($datos['ganancia'])) {
            $this->assertEquals((float)$datos['ganancia'], $this->sut->getGanancia());
        }
        if (isset($datos['excento'])) {
            $this->assertSame($datos['excento'], $this->sut->getExcento());
        }
    }

    // DataProvider para setDatos inválidos
    public static function datosInvalidos(): array
    {
        return [
            'nombre muy corto' => [[
                'nombre' => 'A',
                'excento' => 1
            ]],
            'marca no numerica' => [[
                'nombre' => 'Producto Test',
                'marca' => 'abc',
                'excento' => 1
            ]],
            'presentacion muy corta' => [[
                'nombre' => 'Producto Test',
                'presentacion' => 'B',
                'excento' => 1
            ]],
            'costo negativo' => [[
                'nombre' => 'Producto Test',
                'costo' => '-10',
                'excento' => 1
            ]],
        ];
    }

    #[DataProvider('datosInvalidos')]
    public function testSetDatosConDatosInvalidosLanza(array $datos): void
    {
        $this->sut->setDatos($datos);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta datos inválidos sin validación: " . json_encode($datos));
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // ========================================
    // PRUEBAS DE MÚLTIPLES ERRORES
    // ========================================

    public function testMultiplesErroresAgrupaMensajes(): void
    {
        // Múltiples campos inválidos
        $this->sut->setNombre('A'); // Inválido
        $this->sut->setMarca('abc'); // Inválido
        $this->sut->setPresentacion('B'); // Inválido
        $this->sut->setCantPresentacion(str_repeat('a', 21)); // Inválido
        $this->sut->setCosto('xyz'); // Inválido
        $this->sut->setGanancia('abc'); // Inválido

        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta múltiples datos inválidos sin validación");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // ========================================
    // PRUEBAS DE CAMPOS OPCIONALES
    // ========================================

    public function testCamposOpcionalesPuedenSerVacios(): void
    {
        $this->sut->setNombre('Producto ABC');
        $this->sut->setExcento(0);
        // Campos opcionales vacíos
        $this->sut->setMarca('');
        $this->sut->setPresentacion('');
        $this->sut->setCantPresentacion('');
        $this->sut->setCosto('');
        $this->sut->setGanancia('');

        $this->sut->check(); // No debe lanzar excepción

        $this->assertSame('Producto ABC', $this->sut->getNombre());
        $this->assertSame(0, $this->sut->getExcento());
        $this->assertNull($this->sut->getMarca());
        $this->assertNull($this->sut->getPresentacion());
        $this->assertNull($this->sut->getCantPresentacion());
        $this->assertEquals(0, $this->sut->getCosto());
        $this->assertEquals(0, $this->sut->getGanancia());
    }

    // ========================================
    // PRUEBAS DE CONVERSIÓN DE TIPOS
    // ========================================

    public function testConversionTiposCostoYGanancia(): void
    {
        // Costo con decimal
        $this->sut->setCosto('99.99');
        $this->sut->check();
        $this->assertEquals(99.99, $this->sut->getCosto());
        
        // Costo entero
        $this->sut->setCosto('100');
        $this->sut->check();
        $this->assertEquals(100.0, $this->sut->getCosto());
        
        // Ganancia entero
        $this->sut->setGanancia('25');
        $this->sut->check();
        $this->assertEquals(25.0, $this->sut->getGanancia());
    }

    // ========================================
    // PRUEBAS DE CASOS DE USO REALES
    // ========================================

    public function testPrepararMultiplesPresentacionesParaMismoProducto(): void
    {
        // Simular preparación de múltiples presentaciones para el mismo producto
        
        // Presentación 1: Pieza individual
        $this->sut->setPresentacion('Pieza');
        $this->sut->setCantPresentacion('1');
        $this->sut->setCosto('5.00');
        $this->sut->setGanancia('20');
        $this->sut->setExcento(1);
        
        $this->sut->check();
        $this->assertSame('Pieza', $this->sut->getPresentacion());
        $this->assertEquals(5.00, $this->sut->getCosto());
        
        // Limpiar para siguiente presentación
        $this->sut = new ProductosStub();
        
        // Presentación 2: Caja de 12
        $this->sut->setPresentacion('Caja');
        $this->sut->setCantPresentacion('12');
        $this->sut->setCosto('50.00');
        $this->sut->setGanancia('25');
        $this->sut->setExcento(1);
        
        $this->sut->check();
        $this->assertSame('Caja', $this->sut->getPresentacion());
        $this->assertEquals(50.00, $this->sut->getCosto());
    }

    public function testPrepararPresentacionesConDiferentesCostos(): void
    {
        // Presentación económica
        $this->sut->setPresentacion('Básica');
        $this->sut->setCantPresentacion('1');
        $this->sut->setCosto('10.00');
        $this->sut->setGanancia('10');
        $this->sut->setExcento(0);
        
        $this->sut->check();
        $this->assertEquals(10.00, $this->sut->getCosto());
        $this->assertEquals(10.0, $this->sut->getGanancia());
        
        // Limpiar para siguiente presentación
        $this->sut = new ProductosStub();
        
        // Presentación premium
        $this->sut->setPresentacion('Premium');
        $this->sut->setCantPresentacion('1');
        $this->sut->setCosto('50.00');
        $this->sut->setGanancia('50');
        $this->sut->setExcento(1);
        
        $this->sut->check();
        $this->assertEquals(50.00, $this->sut->getCosto());
        $this->assertEquals(50.0, $this->sut->getGanancia());
    }
}