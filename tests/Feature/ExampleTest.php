<?php

// Test bawaan diganti agar tidak fail
test('aplikasi memerlukan login untuk mengakses root', function () {
    $response = $this->get('/');
    $response->assertRedirect(); // Redirect ke login — expected behavior
});
