<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FtthSplinterLossesSeeder extends Seeder
{
    public function run()
    {
        DB::table('ftth_splinter_losses')->insert([
            [
                'id' => 1,
                'type' => '1/16',
                'derivations' => 16,
                'splinter_type' => 'balanced',
                'loss1' => 13.70,
                'loss2' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'type' => '1/8',
                'derivations' => 8,
                'splinter_type' => 'balanced',
                'loss1' => 10.50,
                'loss2' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'type' => '1/4',
                'derivations' => 4,
                'splinter_type' => 'balanced',
                'loss1' => 7.30,
                'loss2' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 4,
                'type' => '1/2',
                'derivations' => 2,
                'splinter_type' => 'balanced',
                'loss1' => 3.70,
                'loss2' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 5,
                'type' => '40/60',
                'derivations' => 2,
                'splinter_type' => 'unbalanced',
                'loss1' => 4.70,
                'loss2' => 2.70,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 6,
                'type' => '30/70',
                'derivations' => 2,
                'splinter_type' => 'unbalanced',
                'loss1' => 6.00,
                'loss2' => 1.90,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 7,
                'type' => '20/80',
                'derivations' => 2,
                'splinter_type' => 'unbalanced',
                'loss1' => 7.90,
                'loss2' => 1.40,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 8,
                'type' => '15/85',
                'derivations' => 2,
                'splinter_type' => 'unbalanced',
                'loss1' => 9.60,
                'loss2' => 1.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 9,
                'type' => '10/90',
                'derivations' => 2,
                'splinter_type' => 'unbalanced',
                'loss1' => 11.00,
                'loss2' => 0.7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 10,
                'type' => '5/95',
                'derivations' => 2,
                'splinter_type' => 'unbalanced',
                'loss1' => 14.60,
                'loss2' => 0.50,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 11,
                'type' => '2/98',
                'derivations' => 2,
                'splinter_type' => 'unbalanced',
                'loss1' => 18.70,
                'loss2' => 0.40,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 12,
                'type' => '1/99',
                'derivations' => 2,
                'splinter_type' => 'unbalanced',
                'loss1' => 21.60,
                'loss2' => 0.30,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}