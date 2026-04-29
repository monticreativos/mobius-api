<?php

it('no expone traza ni rutas en errores de rutas API', function (): void {
    $response = $this->getJson('/api/ruta-inexistente-xyz');

    $response->assertNotFound()
        ->assertJsonStructure(['message'])
        ->assertJsonMissing(['exception', 'file', 'line', 'trace']);
});
