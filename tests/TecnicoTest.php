<?php

class TecnicoTest extends TestCase
{
    public function ejecutar(): void
    {
        $this->probar('TEC-01', 'Técnico', 'Vistas vw_usuarios/vw_catalogo_productos y roles', function () {
            $this->assertNotNull($this->pdo->query('SELECT * FROM vw_usuarios LIMIT 1'));
            $this->assertNotNull($this->pdo->query('SELECT * FROM vw_catalogo_productos LIMIT 1'));

            $roles = $this->pdo->query('SELECT * FROM rol')->fetchAll(PDO::FETCH_ASSOC);
            $nombres = [];
            foreach ($roles as $fila) {
                foreach ($fila as $valor) {
                    if (is_string($valor)) {
                        $nombres[] = $valor;
                    }
                }
            }

            foreach (['Administrador', 'Almacenista', 'Vendedor'] as $rol) {
                $this->assertTrue(in_array($rol, $nombres, true), 'Falta el rol ' . $rol . ' en la tabla rol.');
            }
        });
    }
}

