<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProgramSeeder extends Seeder
{
    public function run()
    {
        $today = Carbon::now();

        $programs = [
            [
                'name' => 'Programma Onboarding Base',
                'description' => 'Programma di onboarding standard per tutti i nuovi dipendenti',
                'start_date' => $today->format('Y-m-d'),
                'end_date' => $today->copy()->addDays(30)->format('Y-m-d'),
                'is_active' => true,
            ],
            [
                'name' => 'Programma Sviluppatori',
                'description' => 'Programma di onboarding specifico per sviluppatori software',
                'start_date' => $today->format('Y-m-d'),
                'end_date' => $today->copy()->addDays(45)->format('Y-m-d'),
                'is_active' => true,
            ],
            [
                'name' => 'Programma Marketing',
                'description' => 'Programma di onboarding per il team marketing',
                'start_date' => $today->format('Y-m-d'),
                'end_date' => $today->copy()->addDays(25)->format('Y-m-d'),
                'is_active' => true,
            ],
            [
                'name' => 'Programma HR',
                'description' => 'Programma di onboarding per il team risorse umane',
                'start_date' => $today->format('Y-m-d'),
                'end_date' => $today->copy()->addDays(20)->format('Y-m-d'),
                'is_active' => true,
            ],
        ];

        foreach ($programs as $program) {
            Program::create($program);
        }
    }
}
