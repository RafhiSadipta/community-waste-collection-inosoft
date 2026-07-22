<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mongodb')->create('payments', function (Blueprint $collection) {
            $collection->index('status');
            $collection->index('household_id');
            $collection->index('waste_id');
        });
    }

    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('payments');
    }
};
