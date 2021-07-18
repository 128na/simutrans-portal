<?php

namespace Database\Seeders;

use App\Models\Firebase\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->seeds() as $seed) {
            Project::updateOrCreate([
                'name' => $seed['name'],
            ], [
                'credential' => file_get_contents($seed['credential']),
            ]);
        }
    }

    private function seeds()
    {
        yield [
           'name' => 'addon-builder',
           'credential' => './addon-builder-firebase-adminsdk.json',
        ];
    }
}
