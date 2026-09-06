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

        $response = $this->actingAs($user)->post(route('todo.save'), [
            'task' => 'Test zadatak',
            'priority' => 'medium',
            'is_recurring' => true,
            'recurrence' => 'daily',
        ]);

        $response->assertRedirect(route('todo.index'));

        $this->assertDatabaseHas('todos', [
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

        $response = $this->actingAs($user)->delete(route('todo.delete', $todo->id));
        $response->assertRedirect(route('todo.index'));

        $this->assertSoftDeleted('todos', [
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

        $response = $this->actingAs($user)->patch(route('todo.updateStatus', $todo->id), [
            'status' => 'completed',
        ]);

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'status' => 'completed',
        ]);
    }

    public function test_guest_cannot_add_or_delete_todo(): void
    {
        $response = $this->post(route('todo.save'), [
            'task' => 'Neautorizovan zadatak',
            'priority' => 'low',
        ]);
        $response->assertRedirect('/login');

        $user = User::factory()->create();
        $todo = ToDo::create([
            'task' => 'Postojeci zadatak',
            'status' => 'pending',
            'priority' => 'low',
            'user_id' => $user->id,
            'is_recurring' => false,
            'recurrence' => null,
        ]);

        $response2 = $this->delete(route('todo.delete', $todo->id));
        $response2->assertRedirect('/login');
    }

    public function test_authenticated_user_can_add_todo_with_factory(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $todo = ToDo::factory()->create([
            'user_id' => $user->id,
            'task' => 'Test zadatak preko factory',
            'priority' => 'medium',
            'is_recurring' => true,
            'recurrence' => 'daily',
        ]);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'task' => 'Test zadatak preko factory',
            'user_id' => $user->id,
        ]);
    }

    public function test_todo_index_filters_by_status_priority_and_search(): void
    {
        $user = User::factory()->create();
        ToDo::factory()->create([
            'user_id' => $user->id,
            'task' => 'Prepare invoice batch',
            'status' => 'pending',
            'priority' => 'high',
        ]);
        ToDo::factory()->create([
            'user_id' => $user->id,
            'task' => 'Archive completed notes',
            'status' => 'completed',
            'priority' => 'low',
        ]);

        $response = $this->actingAs($user)->get(route('todo.index', [
            'search' => 'invoice',
            'status' => 'pending',
            'priority' => 'high',
        ]));

        $response->assertOk();
        $response->assertSee('Prepare invoice batch');
        $response->assertDontSee('Archive completed notes');
    }

    public function test_user_cannot_update_another_users_todo(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $todo = ToDo::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->patch(route('todo.updateStatus', $todo->id), [
            'status' => 'completed',
        ]);

        $response->assertNotFound();
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'status' => $todo->status,
        ]);
    }

    public function test_admin_can_see_all_users_todos(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        ToDo::factory()->create([
            'user_id' => $user->id,
            'task' => 'Visible to admin',
        ]);

        $response = $this->actingAs($admin)->get(route('todo.index'));

        $response->assertOk();
        $response->assertSee('Visible to admin');
    }

    public function test_kanban_board_shows_all_statuses_even_when_table_is_filtered(): void
    {
        $user = User::factory()->create();
        ToDo::factory()->create(['user_id' => $user->id, 'status' => 'pending', 'task' => 'Pending task']);
        ToDo::factory()->create(['user_id' => $user->id, 'status' => 'completed', 'task' => 'Completed task']);

        $response = $this->actingAs($user)->get(route('todo.index', ['status' => 'pending']));

        $response->assertOk();
        $response->assertSee('Pending task');
        $response->assertSee('Completed task');
    }

    public function test_recurring_todo_requires_recurrence(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from(route('todo.index'))->post(route('todo.save'), [
            'task' => 'Recurring without cadence',
            'priority' => 'medium',
            'is_recurring' => true,
        ]);

        $response->assertRedirect(route('todo.index'));
        $response->assertSessionHasErrors('recurrence');
    }
}
