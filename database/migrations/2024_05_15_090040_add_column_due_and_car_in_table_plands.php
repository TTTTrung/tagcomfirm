<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plandues', function (Blueprint $table) {
            $table->datetime('duedate')->nullable();
            $table->string('car')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plandues', function (Blueprint $table) {
            $table->dropColumn('duedate');
            $table->dropColumn('car');
        });
    }
};
