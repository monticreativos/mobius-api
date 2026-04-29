<?php

test('la ruta raiz devuelve 404 sin exponer tecnologia', function () {
    $response = $this->get('/');

    $response->assertNotFound();
});
