<?php

// API pública que consume la landing (todas las rutas empiezan con /api).
// Cada recurso tiene su propio archivo en routes/api/.
foreach (glob(__DIR__.'/api/*.php') as $recurso) {
    require $recurso;
}
