<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            ['name' => 'Risorse Umane', 'description' => 'Gestione del personale'],
            ['name' => 'Sviluppo Software', 'description' => 'Sviluppo di applicazioni e software'],
            ['name' => 'Marketing', 'description' => 'Gestione delle attività di marketing'],
            ['name' => 'Amministrazione', 'description' => 'Gestione amministrativa e finanziaria'],
            ['name' => 'Vendite', 'description' => 'Gestione delle vendite e dei clienti'],
            ['name' => 'Supporto Tecnico', 'description' => 'Assistenza tecnica ai clienti'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}