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
        Schema::table('parts', function (Blueprint $table) {
            $table->string('order_type')->nullable();
            $table->string('sale_reps')->nullable();
            $table->string('price_list')->nullable();
            $table->string('bill_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropColumn('order_type');
            $table->dropColumn('sale_reps');
            $table->dropColumn('price_list');
            $table->dropColumn('bill_to');
        });
    }
};
