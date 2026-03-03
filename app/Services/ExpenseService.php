<?php

namespace App\Services;

use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class ExpenseService
{
    // Kreiranje troška / prihoda
    public static function create(array $data)
    {
        return Expense::create([
            'name' => $data['name'],
            'amount' => $data['amount'],
            'type' => $data['type'],
            'date' => $data['date'],
            'user_id' => Auth::id(),
        ]);
    }

    // Brisanje po Expense modelu
    public static function delete(Expense $expense)
    {
        $expense->delete();
    }

    // Dohvata sve expenses za korisnika
    public static function listAllByUser(int $userId)
    {
        return Expense::ownedBy($userId)->orderBy('date', 'desc')->get();
    }

    // Generiše ukupne sume i mesečne grafikone
    public static function monthlySummary(int $userId)
    {
        $summary = [
            'totalIncome' => Expense::ownedBy($userId)->ofType('income')->sum('amount'),
            'totalExpense' => Expense::ownedBy($userId)->ofType('expense')->sum('amount'),
        ];

        $summary['balance'] = $summary['totalIncome'] - $summary['totalExpense'];
        $summary['months'] = [];
        $summary['monthlyIncome'] = [];
        $summary['monthlyExpense'] = [];

        for ($i = 1; $i <= 12; $i++) {
            $summary['months'][] = date('M', mktime(0,0,0,$i,1));
            $summary['monthlyIncome'][] = Expense::ownedBy($userId)->ofType('income')->month($i)->sum('amount');
            $summary['monthlyExpense'][] = Expense::ownedBy($userId)->ofType('expense')->month($i)->sum('amount');
        }

        return $summary;
    }
}
