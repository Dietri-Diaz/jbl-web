<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * La raíz del ERP no muestra nada: manda al panel.
     */
    public function test_la_raiz_redirige_al_panel(): void
    {
        $this->get('/')->assertRedirect('/panel');
    }

    /**
     * Quien no inició sesión no entra al panel: va al login.
     */
    public function test_el_panel_exige_iniciar_sesion(): void
    {
        $this->get('/panel')->assertRedirect('/login');
    }

    /**
     * La pantalla de login carga sin errores.
     */
    public function test_el_login_responde_correctamente(): void
    {
        $this->get('/login')->assertStatus(200);
    }
}
