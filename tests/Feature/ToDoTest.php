<?php

namespace Tests\Feature;

use App\Models\ToDo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToDoTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_add_todo(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('todo.save'), [ // koristi tvoju rutu
            'task' => 'Test zadatak',
            'priority' => 'medium',
            'is_recurring' => true,
            'recurrence' => 'daily',
        ]);

        $response->assertRedirect(route('todo.index'));

        $this->assertDatabaseHas('ToDo', [ // koristi tvoju tabelu
            'task' => 'Test zadatak',
            'priority' => 'medium',
            'user_id' => $user->id,
            'status' => 'pending',
            'is_recurring' => 1,
            'recurrence' => 'daily',
        ]);
    }

    public function test_authenticated_user_can_delete_their_todo(): void
    {
        $user = User::factory()->create();
        $todo = ToDo::create([
            'task' => 'Zadatak za brisanje',
            'status' => 'pending',
            'priority' => 'low',
            'user_id' => $user->id,
            'is_recurring' => false,
            'recurrence' => null,
        ]);

        $this->actingAs($user);
        $response = $this->get(route('todo.delete', $todo->id)); // GET metoda
        $response->assertRedirect(route('todo.index'));

        $this->assertDatabaseMissing('ToDo', [
            'id' => $todo->id,
        ]);
    }

    public function test_authenticated_user_can_update_status(): void
    {
        $user = User::factory()->create();
        $todo = ToDo::create([
            'task' => 'Zadatak za update',
            'status' => 'pending',
            'priority' => 'medium',
            'user_id' => $user->id,
            'is_recurring' => false,
            'recurrence' => null,
        ]);

        $this->actingAs($user);
        $response = $this->patch(route('todo.updateStatus', $todo->id), [
            'status' => 'completed',
        ]);

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('ToDo', [
            'id' => $todo->id,
            'status' => 'completed',
        ]);
    }

    public function test_guest_cannot_add_or_delete_todo(): void
{
    // pokušaj da doda ToDo kao guest
    $response = $this->post(route('todo.save'), [
        'task' => 'Neautorizovan zadatak',
        'priority' => 'low',
    ]);
    $response->assertRedirect('/login');

    // pokušaj da obriše ToDo kao guest → kreiramo validan user + todo
    $user = User::factory()->create();
    $todo = ToDo::create([
        'task' => 'Postojeci zadatak',
        'status' => 'pending',
        'priority' => 'low',
        'user_id' => $user->id,
        'is_recurring' => false,
        'recurrence' => null,
    ]);

    $response2 = $this->get(route('todo.delete', $todo->id));
    $response2->assertRedirect('/login');
}

public function test_authenticated_user_can_add_todo_with_factory(): void
{
    $user = User::factory()->create();
    $this->actingAs($user);

    // Kreiranje ToDo direktno preko factory
    $todo = ToDo::factory()->create([
        'user_id' => $user->id,
        'task' => 'Test zadatak preko factory',
        'priority' => 'medium',
        'is_recurring' => true,
        'recurrence' => 'daily',
    ]);

    $this->assertDatabaseHas('ToDo', [
        'id' => $todo->id,
        'task' => 'Test zadatak preko factory',
        'user_id' => $user->id,
    ]);
}
}
