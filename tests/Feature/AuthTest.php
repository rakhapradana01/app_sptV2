<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ==========================================
// AUTH - Login
// ==========================================

test('halaman login dapat diakses', function () {
    $response = $this->get(route('login'));
    $response->assertStatus(200);
});

test('user dapat login dengan kredensial yang benar', function () {
    $role = Role::create(['name' => 'super_admin']);
    $user = User::create([
        'name'     => 'Admin Test',
        'username' => 'admin_test',
        'password' => bcrypt('password123'),
        'role_id'  => $role->id,
    ]);

    $response = $this->post(route('login.authenticated'), [
        'username' => 'admin_test',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('super_admin diarahkan ke dashboard setelah login', function () {
    $role = Role::create(['name' => 'super_admin']);
    $user = User::create([
        'name'     => 'Admin Test',
        'username' => 'admin_test',
        'password' => bcrypt('password123'),
        'role_id'  => $role->id,
    ]);

    $response = $this->post(route('login.authenticated'), [
        'username' => 'admin_test',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('dashboard'));
});

test('non-super_admin diarahkan ke nota-dinas setelah login', function () {
    $role = Role::create(['name' => 'kepala_sub_bidang']);
    $user = User::create([
        'name'     => 'Kasubid Test',
        'username' => 'kasubid_test',
        'password' => bcrypt('password123'),
        'role_id'  => $role->id,
    ]);

    $response = $this->post(route('login.authenticated'), [
        'username' => 'kasubid_test',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('nota-dinas.index'));
});

test('login gagal jika password salah', function () {
    $role = Role::create(['name' => 'super_admin']);
    User::create([
        'name'     => 'Admin Test',
        'username' => 'admin_test',
        'password' => bcrypt('password123'),
        'role_id'  => $role->id,
    ]);

    $response = $this->post(route('login.authenticated'), [
        'username' => 'admin_test',
        'password' => 'wrongpassword',
    ]);

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});

test('login gagal jika username tidak ada', function () {
    $response = $this->post(route('login.authenticated'), [
        'username' => 'tidakada',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});

test('login membutuhkan username dan password', function () {
    $response = $this->post(route('login.authenticated'), []);
    $response->assertSessionHasErrors(['username', 'password']);
});

// ==========================================
// AUTH - Logout
// ==========================================

test('user yang login dapat logout', function () {
    $role = Role::create(['name' => 'super_admin']);
    $user = User::create([
        'name'     => 'Admin Test',
        'username' => 'admin_test',
        'password' => bcrypt('password123'),
        'role_id'  => $role->id,
    ]);

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});

// ==========================================
// AUTH - Redirect tamu
// ==========================================

test('tamu tidak bisa mengakses halaman dashboard', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('tamu tidak bisa mengakses halaman nota dinas', function () {
    $response = $this->get(route('nota-dinas.index'));
    $response->assertRedirect(route('login'));
});
