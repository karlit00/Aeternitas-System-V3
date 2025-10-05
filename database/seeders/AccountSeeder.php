<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a special admin account (not linked to any employee)
        Account::create([
            'employee_id' => null, // Admin account not linked to employee
            'email' => 'jersondev03@gmail.com',
            'password' => Hash::make('jersondev03'),
            'role' => 'hr',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
