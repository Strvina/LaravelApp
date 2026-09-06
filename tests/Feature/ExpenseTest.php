<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_add_expense(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('expenses.add'), [
            'name' => 'Primanje test',
            'amount' => 500,
            'type' => 'income',
            'date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('expenses.index'));
        $this->assertDatabaseHas('expenses', [
            'name' => 'Primanje test',
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_user_can_delete_expense(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $expense = Expense::factory()->create(['user_id' => $user->id]);

        $response = $this->delete(route('expenses.delete', $expense->id));

        $response->assertRedirect(route('expenses.index'));
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }

    public function test_guest_cannot_add_or_delete_expense(): void
    {
        $expense = Expense::factory()->create();

        $response = $this->post(route('expenses.add'), []);
        $response->assertRedirect('/login');

        $response = $this->delete(route('expenses.delete', $expense->id));
        $response->assertRedirect('/login');
    }
}
