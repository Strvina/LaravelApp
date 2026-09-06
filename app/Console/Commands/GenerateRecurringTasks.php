<?php

namespace App\Console\Commands;

use App\Models\ToDo;
use Illuminate\Console\Command;

class GenerateRecurringTasks extends Command
{
    protected $signature = 'tasks:generate-recurring';

    protected $description = 'Generate new pending tasks from recurring todo templates.';

    public function handle(): int
    {
        $created = 0;

        ToDo::query()
            ->where('is_recurring', true)
            ->whereNotNull('recurrence')
            ->chunkById(100, function ($tasks) use (&$created): void {
                foreach ($tasks as $task) {
                    if (! $this->isDue($task)) {
                        continue;
                    }

                    ToDo::create([
                        'task' => $task->task,
                        'status' => 'pending',
                        'priority' => $task->priority,
                        'user_id' => $task->user_id,
                        'is_recurring' => false,
                        'recurrence' => null,
                        'last_generated_at' => null,
                    ]);

                    $task->forceFill(['last_generated_at' => now()->toDateString()])->save();
                    $created++;
                }
            });

        $this->info("Generated {$created} recurring task(s).");

        return self::SUCCESS;
    }

    private function isDue(ToDo $task): bool
    {
        if (! $task->last_generated_at) {
            return true;
        }

        $lastGenerated = $task->last_generated_at;

        return match ($task->recurrence) {
            'daily' => $lastGenerated->lte(now()->subDay()),
            'weekly' => $lastGenerated->lte(now()->subWeek()),
            'monthly' => $lastGenerated->lte(now()->subMonth()),
            default => false,
        };
    }
}
