<?php

class TestAssertionFailed extends RuntimeException
{
}

abstract class TestCase
{
    protected $pdo;
    private $resultados = [];
    private $pasadas = 0;
    private $fallidas = 0;
    private $asercionesPasadas = 0;
    private $asercionesFallidas = 0;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    abstract public function ejecutar(): void;

    public function obtenerResultados(): array
    {
        return $this->resultados;
    }

    public function obtenerTotales(): array
    {
        return [
            'pasadas' => $this->pasadas,
            'fallidas' => $this->fallidas,
            'aserciones_pasadas' => $this->asercionesPasadas,
            'aserciones_fallidas' => $this->asercionesFallidas
        ];
    }

    protected function probar(string $id, string $historia, string $nombre, callable $prueba): void
    {
        $asercionesAntes = $this->asercionesPasadas + $this->asercionesFallidas;

        try {
            $prueba();
            $this->pasadas++;
            $this->resultados[] = [
                'id' => $id,
                'historia' => $historia,
                'prueba' => $nombre,
                'resultado' => 'OK'
            ];
        } catch (Throwable $error) {
            $this->fallidas++;
            if ($this->asercionesPasadas + $this->asercionesFallidas === $asercionesAntes) {
                $this->asercionesFallidas++;
            }
            $this->resultados[] = [
                'id' => $id,
                'historia' => $historia,
                'prueba' => $nombre . ': ' . $error->getMessage(),
                'resultado' => 'FALLA'
            ];
        }
    }

    protected function omitida(string $id, string $historia, string $nombre, string $motivo): void
    {
        $this->resultados[] = [
            'id' => $id,
            'historia' => $historia,
            'prueba' => $nombre . ': ' . $motivo,
            'resultado' => 'OMITIDA'
        ];
    }

    protected function assertTrue($valor, string $mensaje = 'Se esperaba true'): void
    {
        $this->verificar($valor === true, $mensaje . ' (obtenido: ' . var_export($valor, true) . ')');
    }

    protected function assertFalse($valor, string $mensaje = 'Se esperaba false'): void
    {
        $this->verificar($valor === false, $mensaje . ' (obtenido: ' . var_export($valor, true) . ')');
    }

    protected function assertEquals($esperado, $actual, string $mensaje = ''): void
    {
        $detalle = $mensaje !== '' ? $mensaje : 'Los valores no coinciden';
        $detalle .= ' (esperado: ' . var_export($esperado, true)
            . ', obtenido: ' . var_export($actual, true) . ')';
        $this->verificar($esperado === $actual, $detalle);
    }

    protected function assertNull($valor, string $mensaje = 'Se esperaba null'): void
    {
        $this->verificar($valor === null, $mensaje);
    }

    protected function assertNotNull($valor, string $mensaje = 'No se esperaba null'): void
    {
        $this->verificar($valor !== null, $mensaje);
    }

    protected function expectException(callable $accion, string $claseEsperada): Throwable
    {
        try {
            $accion();
        } catch (Throwable $error) {
            $this->verificar(
                $error instanceof $claseEsperada,
                'Se esperaba ' . $claseEsperada . ', se obtuvo ' . get_class($error)
            );
            return $error;
        }

        $this->verificar(false, 'Se esperaba una excepción de tipo ' . $claseEsperada);
    }

    protected function enTransaccion(callable $accion)
    {
        $this->pdo->beginTransaction();

        try {
            return $accion();
        } finally {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
        }
    }

    protected function sufijoUnico(): string
    {
        return date('YmdHis') . '_' . bin2hex(random_bytes(4));
    }

    private function verificar(bool $correcto, string $mensaje): void
    {
        if (!$correcto) {
            $this->asercionesFallidas++;
            throw new TestAssertionFailed($mensaje);
        }

        $this->asercionesPasadas++;
    }
}
