<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ToDo;
use Carbon\Carbon;

class GenerateRecurringTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-recurring-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate recurring tasks automatically';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for recurring tasks...');

        $today = Carbon::today();

        // Dohvatamo sve recurring taskove
        $recurringTasks = ToDo::where('is_recurring', true)
            ->where(function($query) use ($today) {
                $query->whereNull('last_generated_at')
                      ->orWhereDate('last_generated_at', '<', $today);
            })
            ->get();

        foreach ($recurringTasks as $task) {
            // Kreiramo novi zadatak kao kopiju
            ToDo::create([
                'task' => $task->task,
                'status' => 'pending',
                'priority' => $task->priority,
                "user_id" => $task->user_id,
                'is_recurring' => true,
                'recurrence' => $task->recurrence,
            ]);

            // Ažuriramo original da znamo kad je poslednji put generisan
            $task->update([
                'last_generated_at' => $today,
            ]);

            $this->info("Generated recurring task: {$task->task}");
        }

        $this->info('Recurring tasks check complete!');
    }
}
