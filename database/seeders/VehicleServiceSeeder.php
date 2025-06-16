<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\VehicleService;

class VehicleServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $services = [
            'car' => ['Troca de óleo', 'Alinhamento', 'Balanceamento'],
            'motorcycle' => ['Revisão geral', 'Troca de corrente'],
            'truck' => ['Verificação de freios', 'Calibração de pneus'],
        ];

        foreach ($services as $type => $names) {
            foreach ($names as $name) {
                VehicleService::create([
                    'vehicle_type' => $type,
                    'name' => $name,
                ]);
            }
        }
    }
}
