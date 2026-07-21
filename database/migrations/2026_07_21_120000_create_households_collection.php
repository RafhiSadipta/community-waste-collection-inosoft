<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mongodb')->create('households', function (Blueprint $collection) {
            $collection->index('block');
            $collection->index('no');
        });
    }

    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('households');
    }
};
