<?php
declare(strict_types=1);

namespace Tests\Unit\Modelo;

use Modelo\Carga;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

class CargaStub extends Carga
{
    public function __construct() {}
    public function conectarBD()   { /* no-op */ }
    public function desconectarBD(){ /* no-op */ }
}

#[Group('unit')]
final class CargaTest extends TestCase
{
    private CargaStub $sut;

    protected function setUp(): void
    {
        $this->sut = new CargaStub();
    }

    // ========================================
    // PRUEBAS DE ESTADO INICIAL
    // ========================================
    
    public function testInicialmenteGettersSonNull(): void
    {
        $this->assertNull($this->sut->getCod());
        $this->assertNull($this->sut->getFecha());
        $this->assertNull($this->sut->getDes());
        $this->assertNull($this->sut->getStatus());
        $this->assertNull($this->sut->getlote());
        $this->assertNull($this->sut->getFechaV());
        $this->assertNull($this->sut->getCodp());
    }

    // ========================================
    // PRUEBAS VÁLIDAS - CASOS QUE DEBEN PASAR
    // ========================================

    // DataProvider para descripciones válidas
    public static function descripcionesValidas(): array
    {
        return [
            'minimo 2'      => ['Hi'],
            'medio'         => ['Descripción de carga'],
            'maximo 50'     => [str_repeat('a', 50)],
            'con numeros'   => ['Carga 2024-001'],
            'con guiones'   => ['Carga-productos'],
            'con puntos'    => ['Carga.productos'],
            'con comas'     => ['Carga, productos'],
            'con acentos'   => ['Descripción con acentos'],
        ];
    }

    #[DataProvider('descripcionesValidas')]
    public function testSetDesConValorValidoNoLanza(string $descripcion): void
    {
        $this->sut->setDes($descripcion);
        $this->sut->check();
        $this->assertSame($descripcion, $this->sut->getDes());
    }

    // ========================================
    // PRUEBAS INVÁLIDAS - CASOS QUE DEBEN FALLAR
    // ========================================

    // DataProvider para descripciones inválidas
    public static function descripcionesInvalidas(): array
    {
        return [
            'vacio'         => [''],
            'solo 1 char'    => ['A'],
            'mas de 50'      => [str_repeat('a', 51)],
            'solo espacios'  => ['   '],
            'solo tab'       => ["\t"],
            'solo newline'   => ["\n"],
            'mezcla espacios' => [" \t\n\r "],
        ];
    }

    #[DataProvider('descripcionesInvalidas')]
    public function testSetDesConValorInvalidoLanza(string $descripcion): void
    {
        $this->sut->setDes($descripcion);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta descripciones inválidas sin validación: '$descripcion'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // DataProvider para setters válidos (sin validación)
    public static function valoresValidosParaSetters(): array
    {
        return [
            'string numerico' => ['123'],
            'string decimal' => ['123.45'],
            'string fecha' => ['2024-01-15'],
            'string datetime' => ['2024-01-15 14:30:00'],
            'string email' => ['test@example.com'],
            'string url' => ['https://example.com'],
            'string ip' => ['192.168.1.1'],
            'string uuid' => ['550e8400-e29b-41d4-a716-446655440000'],
            'string con espacios' => ['valor con espacios'],
            'string con acentos' => ['valor con acentos'],
            'string alfanumerico' => ['ABC123'],
            'string con guiones' => ['COD-001'],
            'string con puntos' => ['COD.001'],
            'string con guiones bajos' => ['COD_001'],
        ];
    }

    // DataProvider para valores válidos para COD (debería ser entero)
    public static function valoresValidosParaCod(): array
    {
        return [
            'numero entero positivo' => ['123'],
            'numero entero cero' => ['0'],
            'numero entero grande' => ['999999'],
            'numero entero maximo' => ['2147483647'], // PHP_INT_MAX
        ];
    }

    // DataProvider para valores inválidos para COD (defecto del sistema)
    public static function valoresInvalidosParaCod(): array
    {
        return [
            // Valores no numéricos
            'string texto' => ['ABC'],
            'string fecha' => ['2024-01-15'],
            'string email' => ['test@example.com'],
            'string url' => ['https://example.com'],
            'string con espacios' => ['valor con espacios'],
            'string con acentos' => ['valor con acentos'],
            'string con simbolos' => ['COD@#$%'],
            'string vacio' => [''],
            'string null' => ['null'],
            'string booleano' => ['true'],
            
            // Valores numéricos pero no enteros
            'numero decimal' => ['123.45'],
            'numero decimal negativo' => ['-123.45'],
            'numero cientifico' => ['1.23e+10'],
            'numero con punto' => ['123.0'],
            'numero con coma' => ['123,45'],
            
            // Números negativos (si COD debe ser positivo)
            'numero negativo' => ['-123'],
            'numero negativo grande' => ['-999999'],
        ];
    }

    // Pruebas para COD - valores que DEBERÍAN ser válidos
    #[DataProvider('valoresValidosParaCod')]
    public function testSetCodConValoresValidosAsignaValor($valor): void
    {
        $this->sut->setCod($valor);
        $this->assertSame($valor, $this->sut->getCod());
    }

    // Pruebas para COD - valores que DEBERÍAN ser inválidos (defecto del sistema)
    #[DataProvider('valoresInvalidosParaCod')]
    public function testSetCodConValoresInvalidosAsignaValor($valor): void
    {
        // DEFECTO DOCUMENTADO: El sistema acepta valores no enteros para COD
        // Esto debería fallar, pero el sistema no tiene validación
        $this->sut->setCod($valor);
        $this->assertSame($valor, $this->sut->getCod());
        
        // Si llega aquí, documentamos el defecto
        $this->fail("DEFECTO: El sistema acepta valores no enteros para COD: " . $valor);
    }

    // Prueba específica para documentar que COD debería ser entero
    public function testCodDeberiaSerEnteroPeroNoTieneValidacion(): void
    {
        // DEFECTO: El sistema no valida que COD sea entero
        // Debería implementar validación de entero
        
        $valoresNoEnteros = [
            'ABC',                    // texto
            'test@example.com',       // email
            '2024-01-15',            // fecha
            'true',                  // booleano
            '',                      // vacío
            '123.45',                // decimal
            '-123.45',               // decimal negativo
            '1.23e+10',              // científico
            '123.0',                 // con punto
            '123,45',                // con coma
            '-123',                  // negativo
        ];
        
        foreach ($valoresNoEnteros as $valor) {
            $this->sut->setCod($valor);
            $this->assertSame($valor, $this->sut->getCod());
        }
        
        $this->fail("DEFECTO CRÍTICO: El sistema acepta valores no enteros para COD sin validación");
    }

    #[DataProvider('valoresValidosParaSetters')]
    public function testSetFechaAsignaValor($valor): void
    {
        $this->sut->setFecha($valor);
        $this->assertSame($valor, $this->sut->getFecha());
    }

    #[DataProvider('valoresValidosParaSetters')]
    public function testSetStatusAsignaValor($valor): void
    {
        $this->sut->setStatus($valor);
        $this->assertSame($valor, $this->sut->getStatus());
    }

    #[DataProvider('valoresValidosParaSetters')]
    public function testSetloteAsignaValor($valor): void
    {
        $this->sut->setlote($valor);
        $this->assertSame($valor, $this->sut->getlote());
    }

    #[DataProvider('valoresValidosParaSetters')]
    public function testSetFechaVAsignaValor($valor): void
    {
        $this->sut->setFechaV($valor);
        $this->assertSame($valor, $this->sut->getFechaV());
    }

    #[DataProvider('valoresValidosParaSetters')]
    public function testSetCodpAsignaValor($valor): void
    {
        $this->sut->setCodp($valor);
        $this->assertSame($valor, $this->sut->getCodp());
    }

    // DataProvider para casos que deben fallar en check()
    public static function casosInvalidosParaCheck(): array
    {
        return [
            'muy corta' => ['A'],
            'vacia' => [''],
            'solo espacios' => ['  '],
            'muy larga' => [str_repeat('a', 51)],
        ];
    }

    #[DataProvider('casosInvalidosParaCheck')]
    public function testCheckConDescripcionInvalidaLanza(string $descripcion): void
    {
        $this->sut->setDes($descripcion);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta descripciones inválidas sin validación: '$descripcion'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    #[DataProvider('descripcionesValidas')]
    public function testCheckSinErroresNoLanza(string $descripcion): void
    {
        $this->sut->setDes($descripcion);
        $this->sut->check(); // No debe lanzar excepción
        $this->assertTrue(true);
    }

    // Pruebas de getErrores
    #[DataProvider('casosInvalidosParaCheck')]
    public function testGetErroresRetornaArray(string $descripcion): void
    {
        $this->sut->setDes($descripcion);
        $errores = $this->sut->getErrores();
        $this->assertIsArray($errores);
        $this->assertArrayHasKey('descripcion', $errores);
    }

    #[DataProvider('descripcionesValidas')]
    public function testGetErroresVacioSinErrores(string $descripcion): void
    {
        $this->sut->setDes($descripcion);
        $errores = $this->sut->getErrores();
        $this->assertEmpty($errores);
    }

    // DataProvider para combinaciones válidas
    public static function combinacionesCamposValidos(): array
    {
        return [
            'caso basico' => [
                'cod' => '123',
                'fecha' => '2024-01-15',
                'descripcion' => 'Descripción de carga completa',
                'status' => 1,
                'lote' => 'LOTE001',
                'fechaV' => '2025-12-31',
                'codp' => 456
            ],
            'caso con strings' => [
                'cod' => 'ABC123',
                'fecha' => '2024-12-31',
                'descripcion' => 'Carga especial',
                'status' => '1',
                'lote' => 'LOTE-2024-001',
                'fechaV' => '2026-01-01',
                'codp' => '789'
            ],
            'caso con decimales' => [
                'cod' => '123.45',
                'fecha' => '2024-06-15',
                'descripcion' => 'Carga con decimales',
                'status' => 0,
                'lote' => 'LOTE-001.5',
                'fechaV' => '2025-06-15',
                'codp' => 123.45
            ],
        ];
    }

    #[DataProvider('combinacionesCamposValidos')]
    public function testTodosLosCamposValidosNoLanza($cod, $fecha, $descripcion, $status, $lote, $fechaV, $codp): void
    {
        $this->sut->setCod($cod);
        $this->sut->setFecha($fecha);
        $this->sut->setDes($descripcion);
        $this->sut->setStatus($status);
        $this->sut->setlote($lote);
        $this->sut->setFechaV($fechaV);
        $this->sut->setCodp($codp);

        $this->sut->check(); // No debe lanzar excepción

        $this->assertSame($cod, $this->sut->getCod());
        $this->assertSame($fecha, $this->sut->getFecha());
        $this->assertSame($descripcion, $this->sut->getDes());
        $this->assertSame($status, $this->sut->getStatus());
        $this->assertSame($lote, $this->sut->getlote());
        $this->assertSame($fechaV, $this->sut->getFechaV());
        $this->assertSame($codp, $this->sut->getCodp());
    }

    // Pruebas de campos opcionales
    #[DataProvider('descripcionesValidas')]
    public function testCamposOpcionalesPuedenSerNull(string $descripcion): void
    {
        $this->sut->setDes($descripcion);
        $this->sut->check(); // No debe lanzar excepción

        $this->assertSame($descripcion, $this->sut->getDes());
        $this->assertNull($this->sut->getCod());
        $this->assertNull($this->sut->getFecha());
        $this->assertNull($this->sut->getStatus());
        $this->assertNull($this->sut->getlote());
        $this->assertNull($this->sut->getFechaV());
        $this->assertNull($this->sut->getCodp());
    }

    // ========================================
    // PRUEBAS EDGE CASES Y MALICIOSAS
    // ========================================

    // DataProvider para caracteres especiales
    public static function descripcionesConCaracteresEspeciales(): array
    {
        return [
            'con parentesis' => ['Carga #123 - Productos varios (50 unidades)'],
            'con corchetes' => ['Carga [2024-001] con productos'],
            'con llaves' => ['Carga {especial} de productos'],
            'con arroba' => ['Carga @empresa productos varios'],
            'con porcentaje' => ['Carga 100% productos nuevos'],
            'con dolar' => ['Carga $5000 en productos'],
            'con ampersand' => ['Carga & productos varios'],
            'con asterisco' => ['Carga * productos especiales'],
            'con mas' => ['Carga + productos adicionales'],
            'con igual' => ['Carga = productos iguales'],
            'con exclamacion' => ['Carga! productos urgentes'],
            'con interrogacion' => ['Carga? productos dudosos'],
            'con comillas' => ['Carga "productos" especiales'],
            'con apostrofe' => ["Carga 'productos' varios"],
            'con barra' => ['Carga / productos varios'],
            'con backslash' => ['Carga \\ productos varios'],
            'con pipe' => ['Carga | productos varios'],
            'con tilde' => ['Carga ~ productos varios'],
            'con acento grave' => ['Carga ` productos varios'],
            'con simbolos multiples' => ['Carga #123 - Productos (50) @empresa $1000'],
        ];
    }

    #[DataProvider('descripcionesConCaracteresEspeciales')]
    public function testDescripcionConCaracteresEspeciales(string $descripcion): void
    {
        $this->sut->setDes($descripcion);
        
        // Algunos caracteres pueden ser válidos, otros no
        // El test debe revelar qué caracteres acepta realmente el sistema
        try {
            $this->sut->check();
            $this->assertSame($descripcion, $this->sut->getDes());
        } catch (\Exception $e) {
            // Si falla, verificar que el mensaje de error sea descriptivo
            $this->assertStringContainsString('descripcion', $e->getMessage());
        }
    }

    // prueba descripcion con numeros y casos edge
    public static function descripcionesConNumeros(): array
    {
        return [
            'solo numeros' => ['1234567890'],
            'numeros con espacios' => ['123 456 789'],
            'numeros con guiones' => ['123-456-789'],
            'numeros con puntos' => ['123.456.789'],
            'numeros con comas' => ['123,456,789'],
            'numeros grandes' => ['999999999999999999'],
            'numeros negativos' => ['-123'],
            'numeros decimales' => ['123.45'],
            'numeros cientificos' => ['1.23e+10'],
            'numeros hexadecimales' => ['0x123ABC'],
            'numeros binarios' => ['101010'],
            'numeros octales' => ['0123'],
            'numeros con ceros' => ['000123'],
            'numeros con signos' => ['+123'],
            'numeros con parentesis' => ['(123)'],
            'numeros con porcentaje' => ['123%'],
            'numeros con dolar' => ['$123'],
            'numeros con euro' => ['€123'],
            'numeros con peso' => ['$123.45'],
            'numeros con exponente' => ['1.23E+10'],
        ];
    }

    #[DataProvider('descripcionesConNumeros')]
    public function testDescripcionConNumeros(string $descripcion): void
    {
        $this->sut->setDes($descripcion);
        
        try {
            $this->sut->check();
            $this->assertSame($descripcion, $this->sut->getDes());
        } catch (\Exception $e) {
            $this->assertStringContainsString('descripcion', $e->getMessage());
        }
    }

    // prueba descripcion con acentos
    public static function descripcionesConAcentos(): array
    {
        return [
            'español' => ['Carga de productos con descripción'],
            'frances' => ['Chargement de produits avec description'],
            'aleman' => ['Ladung von Produkten mit Beschreibung'],
            'portugues' => ['Carregamento de produtos com descrição'],
            'italiano' => ['Caricamento di prodotti con descrizione'],
            'catalan' => ['Càrrega de productes amb descripció'],
            'gallego' => ['Carga de produtos con descrición'],
            'vascuence' => ['Produktuen karga deskribapenarekin'],
            'mixto' => ['Carga de productos con descripción y acentos'],
        ];
    }

    #[DataProvider('descripcionesConAcentos')]
    public function testDescripcionConAcentos(string $descripcion): void
    {
        $this->sut->setDes($descripcion);
        $this->sut->check();
        $this->assertSame($descripcion, $this->sut->getDes());
    }

    // prueba casos limite de longitud
    public static function casosLimiteLongitud(): array
    {
        return [
            'minimo exacto' => [str_repeat('a', 2)],
            'minimo mas uno' => [str_repeat('a', 3)],
            'maximo exacto' => [str_repeat('a', 50)],
            'maximo mas uno' => [str_repeat('a', 51)],
            'maximo mas diez' => [str_repeat('a', 60)],
            'maximo mas cien' => [str_repeat('a', 150)],
            'un caracter' => ['a'],
            'cero caracteres' => [''],
            'solo espacios' => ['  '],
            'solo espacios minimo' => ['  '],
            'solo espacios maximo' => [str_repeat(' ', 50)],
            'solo espacios mas uno' => [str_repeat(' ', 51)],
            'tabs' => ["\t\t"],
            'newlines' => ["\n\n"],
            'carriage return' => ["\r\r"],
            'mezcla espacios' => [" \t\n\r "],
            'unicode' => ['áéíóú'],
            'unicode maximo' => [str_repeat('á', 50)],
            'unicode mas uno' => [str_repeat('á', 51)],
            'emojis' => ['😀😀'],
            'emojis maximo' => [str_repeat('😀', 25)],
            'emojis mas uno' => [str_repeat('😀', 26)],
        ];
    }

    #[DataProvider('casosLimiteLongitud')]
    public function testCasosLimiteLongitud(string $descripcion): void
    {
        $this->sut->setDes($descripcion);
        
        try {
            $this->sut->check();
            $this->assertSame($descripcion, $this->sut->getDes());
        } catch (\Exception $e) {
            $this->assertStringContainsString('descripcion', $e->getMessage());
        }
    }

    // prueba descripcion vacia y casos edge
    public static function descripcionesVaciasYEdge(): array
    {
        return [
            'vacia' => [''],
            'solo espacios' => ['   '],
            'solo tab' => ["\t"],
            'solo newline' => ["\n"],
            'solo carriage return' => ["\r"],
            'mezcla espacios' => [" \t\n\r "],
            'null string' => [null],
            'false string' => [false],
            'zero string' => ['0'],
            'boolean true' => [true],
            'boolean false' => [false],
            'array vacio' => [[]],
            'object vacio' => [new \stdClass()],
        ];
    }

    #[DataProvider('descripcionesVaciasYEdge')]
    public function testDescripcionVaciaYEdge($descripcion): void
    {
        // Esta prueba documenta defectos del sistema
        // Algunos casos deberían fallar con TypeError, otros con Exception
        
        if (is_array($descripcion) || is_object($descripcion)) {
            // DEFECTO DOCUMENTADO: Arrays y objects causan TypeError
            $this->expectException(\TypeError::class);
            $this->sut->setDes($descripcion);
        } else {
            // Casos normales que deberían fallar con validación
            $this->sut->setDes($descripcion);
            
            try {
                $this->sut->check();
                $this->fail("DEFECTO: El sistema acepta descripciones inválidas sin validación: " . var_export($descripcion, true));
            } catch (\Exception $e) {
                $this->assertStringContainsString('Errores de validación', $e->getMessage());
            }
        }
    }

    // DataProvider para tipos de datos problemáticos
    public static function tiposDatosProblematicos(): array
    {
        return [
            'array vacio' => [[]],
            'object vacio' => [new \stdClass()],
            'array con datos' => [['key' => 'value']],
            'object con propiedades' => [(object)['key' => 'value']],
            'resource' => [fopen('php://memory', 'r')],
            'callable' => [function() { return 'test'; }],
            'float' => [123.45],
            'integer' => [123],
            'array numerico' => [[1, 2, 3]],
            'array asociativo' => [['a' => 1, 'b' => 2]],
        ];
    }

    #[DataProvider('tiposDatosProblematicos')]
    public function testTiposDatosProblematicos($descripcion): void
    {
        // DEFECTO DOCUMENTADO: El sistema no valida tipos de datos correctamente
        
        if (is_array($descripcion) || is_object($descripcion) || is_resource($descripcion) || is_callable($descripcion)) {
            // Estos casos SÍ causan TypeError - defecto del sistema
            $this->expectException(\TypeError::class);
            $this->sut->setDes($descripcion);
        } else {
            // DEFECTO: Float e Integer se convierten a string sin validación
            // El sistema debería rechazar estos tipos, pero los acepta
            $this->sut->setDes($descripcion);
            
            try {
                $this->sut->check();
                $this->fail("DEFECTO: El sistema acepta tipos incorrectos como " . gettype($descripcion) . " sin validación");
            } catch (\Exception $e) {
                $this->assertStringContainsString('Errores de validación', $e->getMessage());
            }
        }
    }

    // DataProvider para casos maliciosos de seguridad
    public static function casosMaliciosos(): array
    {
        return [
            'sql injection' => ["'; DROP TABLE carga; --"],
            'xss script' => ['<script>alert("xss")</script>'],
            'html tags' => ['<b>negrita</b> <i>cursiva</i>'],
            'null bytes' => ["\0\0\0"],
            'control characters' => ["\x00\x01\x02\x03\x04\x05"],
            'backslash escape' => ['\\n\\t\\r'],
            'unicode null' => ["\u0000"],
            'bom' => ["\xEF\xBB\xBF"],
            'zero width' => ["\u200B\u200C\u200D"],
            'right to left' => ["\u202E"],
            'invisible chars' => ["\u2060\u2061\u2062"],
            'very long line' => [str_repeat('a', 1000)],
            'mixed nulls' => ["a\0b\0c\0"],
            'json injection' => ['{"malicious": "data"}'],
            'xml injection' => ['<xml><data>test</data></xml>'],
            'path traversal' => ['../../../etc/passwd'],
            'command injection' => ['; rm -rf /'],
            'php code' => ['<?php echo "hack"; ?>'],
            'javascript' => ['javascript:alert(1)'],
            'data uri' => ['data:text/html,<script>alert(1)</script>'],
        ];
    }

    #[DataProvider('casosMaliciosos')]
    public function testCasosMaliciosos(string $descripcion): void
    {
        $this->sut->setDes($descripcion);
        
        try {
            $this->sut->check();
            $this->assertSame($descripcion, $this->sut->getDes());
            // Si llega aquí, el sistema aceptó datos maliciosos - esto puede ser un defecto de seguridad
            $this->fail("DEFECTO DE SEGURIDAD: El sistema acepta datos maliciosos sin validación: '$descripcion'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('descripcion', $e->getMessage());
        }
    }

    // prueba setters con diferentes tipos de datos
    public static function tiposDatosDiferentes(): array
    {
        return [
            'string numerico' => ['123'],
            'string decimal' => ['123.45'],
            'string negativo' => ['-123'],
            'string booleano' => ['true'],
            'string null' => ['null'],
            'string undefined' => ['undefined'],
            'string array' => ['[1,2,3]'],
            'string object' => ['{"key":"value"}'],
            'string fecha' => ['2024-01-15'],
            'string hora' => ['14:30:00'],
            'string datetime' => ['2024-01-15 14:30:00'],
            'string email' => ['test@example.com'],
            'string url' => ['https://example.com'],
            'string ip' => ['192.168.1.1'],
            'string mac' => ['00:11:22:33:44:55'],
            'string uuid' => ['550e8400-e29b-41d4-a716-446655440000'],
            'string base64' => ['SGVsbG8gV29ybGQ='],
            'string hex' => ['48656c6c6f'],
            'string binary' => ['01001000'],
            'string octal' => ['0123'],
        ];
    }

    #[DataProvider('tiposDatosDiferentes')]
    public function testSettersConDiferentesTipos(string $valor): void
    {
        $this->sut->setCod($valor);
        $this->sut->setStatus($valor);
        $this->sut->setCodp($valor);
        $this->sut->setDes('Descripción válida');

        $this->sut->check();

        $this->assertSame($valor, $this->sut->getCod());
        $this->assertSame($valor, $this->sut->getStatus());
        $this->assertSame($valor, $this->sut->getCodp());
        $this->assertSame('Descripción válida', $this->sut->getDes());
    }
}
