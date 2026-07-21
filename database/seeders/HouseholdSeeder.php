<?php

namespace Database\Seeders;

use App\Models\Household;
use Illuminate\Database\Seeder;

class HouseholdSeeder extends Seeder
{
    public function run(): void
    {
        // Cuma seed sekali, saat collection benar-benar kosong. Setelah itu
        // seeder tidak pernah menyentuh data lagi walau container di-restart,
        // supaya data yang sudah diedit/ditambah manual tidak terganggu.
        if (Household::count() > 0) {
            return;
        }

        $households = [
            ['owner_name' => 'Budi Santoso', 'address' => 'Jl. Mawar No. 12', 'block' => 'A', 'no' => '12'],
            ['owner_name' => 'Siti Rahayu', 'address' => 'Jl. Melati No. 5', 'block' => 'A', 'no' => '5'],
            ['owner_name' => 'Agus Wijaya', 'address' => 'Jl. Anggrek No. 8', 'block' => 'B', 'no' => '8'],
            ['owner_name' => 'Dewi Lestari', 'address' => 'Jl. Kenanga No. 3', 'block' => 'B', 'no' => '3'],
            ['owner_name' => 'Hendra Kurniawan', 'address' => 'Jl. Dahlia No. 21', 'block' => 'C', 'no' => '21'],
            ['owner_name' => 'Rina Marlina', 'address' => 'Jl. Tulip No. 17', 'block' => 'C', 'no' => '17'],
        ];

        foreach ($households as $household) {
            Household::create($household);
        }
    }
}
