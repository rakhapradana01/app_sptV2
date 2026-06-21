<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest can access signup page and register successfully', function () {
    $role = Role::create(['name' => 'kepala_sub_bidang']);

    $response = $this->get(route('register'));
    $response->assertStatus(200);

    $responseStore = $this->post(route('register.store'), [
        'name' => 'New User Test',
        'username' => 'newuser',
        'password' => 'Password123',
        'password_confirmation' => 'Password123',
        'role_id' => $role->id,
    ]);

    $responseStore->assertRedirect(route('nota-dinas.index'));
    $this->assertDatabaseHas('users', [
        'name' => 'New User Test',
        'username' => 'newuser',
        'role_id' => $role->id,
    ]);
});

test('super admin can access master accounts CRUD', function () {
    $superAdminRole = Role::create(['name' => 'super_admin']);
    $userRole = Role::create(['name' => 'user']);

    $admin = User::create([
        'name' => 'Admin Test',
        'username' => 'admin_test',
        'password' => bcrypt('password'),
        'role_id' => $superAdminRole->id,
    ]);

    $this->actingAs($admin);

    // List users
    $response = $this->get(route('users.index'));
    $response->assertStatus(200);

    // Create user via Master Akun
    $responseStore = $this->post(route('users.store'), [
        'name' => 'Staff Created',
        'username' => 'staff_created',
        'password' => 'Password123',
        'role_id' => $userRole->id,
    ]);
    $responseStore->assertRedirect(route('users.index'));
    $this->assertDatabaseHas('users', [
        'name' => 'Staff Created',
        'username' => 'staff_created',
    ]);

    $createdUser = User::where('username', 'staff_created')->first();

    // Update user
    $responseUpdate = $this->put(route('users.update', $createdUser->id), [
        'name' => 'Staff Updated',
        'username' => 'staff_updated',
        'role_id' => $userRole->id,
    ]);
    $responseUpdate->assertRedirect(route('users.index'));
    $this->assertDatabaseHas('users', [
        'name' => 'Staff Updated',
        'username' => 'staff_updated',
    ]);

    // Delete user
    $responseDelete = $this->delete(route('users.destroy', $createdUser->id));
    $responseDelete->assertRedirect(route('users.index'));
    $this->assertDatabaseMissing('users', [
        'id' => $createdUser->id,
    ]);
});

test('regular user cannot access master accounts CRUD', function () {
    $superAdminRole = Role::create(['name' => 'super_admin']);
    $userRole = Role::create(['name' => 'user']);

    $regularUser = User::create([
        'name' => 'Regular User',
        'username' => 'reg_user',
        'password' => bcrypt('password'),
        'role_id' => $userRole->id,
    ]);

    $this->actingAs($regularUser);

    $response = $this->get(route('users.index'));
    $response->assertStatus(403);

    $responseStore = $this->post(route('users.store'), [
        'name' => 'Spy Account',
        'username' => 'spy',
        'password' => 'Password123',
        'role_id' => $superAdminRole->id,
    ]);
    $responseStore->assertStatus(403);
});

test('cannot register or create accounts with super admin or admin roles', function () {
    $superAdminRole = Role::create(['name' => 'super_admin']);
    $adminRole = Role::create(['name' => 'admin']);
    $userRole = Role::create(['name' => 'user']);

    // 1. Guest Signup using super_admin role
    $response = $this->post(route('register.store'), [
        'name' => 'Fake Super Admin',
        'username' => 'fakesuper',
        'password' => 'Password123',
        'password_confirmation' => 'Password123',
        'role_id' => $superAdminRole->id,
    ]);
    $response->assertSessionHasErrors('role_id');

    // 2. Guest Signup using admin role
    $responseAdmin = $this->post(route('register.store'), [
        'name' => 'Fake Admin',
        'username' => 'fakeadmin',
        'password' => 'Password123',
        'password_confirmation' => 'Password123',
        'role_id' => $adminRole->id,
    ]);
    $responseAdmin->assertSessionHasErrors('role_id');

    // 3. Super Admin creating user with super_admin role
    $currentAdmin = User::create([
        'name' => 'Current Admin',
        'username' => 'curr_admin',
        'password' => bcrypt('password'),
        'role_id' => $superAdminRole->id,
    ]);

    $this->actingAs($currentAdmin);

    $responseStore = $this->post(route('users.store'), [
        'name' => 'New Super Admin',
        'username' => 'newsuper',
        'password' => 'Password123',
        'role_id' => $superAdminRole->id,
    ]);
    $responseStore->assertSessionHasErrors('role_id');
});
