<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            ['name' => 'Office of the Regional Director',            'code' => 'ORD'],
            ['name' => 'Finance and Administrative Division',                   'code' => 'FAD'],
            ['name' => 'Mine Management Division',           'code' => 'MMD'],
            ['name' => 'Mine Safety, Environment, and Social Development Division',    'code' => 'MSESDD'],
            ['name' => 'Geosciences Division',                     'code' => 'GD'],

        ];

        foreach ($divisions as $division) {
            Division::firstOrCreate(
                ['name' => $division['name']],
                $division
            );
        }
    }
}