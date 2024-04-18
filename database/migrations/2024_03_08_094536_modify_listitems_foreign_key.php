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
        Schema::table('listitems', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['plandue_id']);
            $table->dropColumn(['plandue_id']);
    
            // Add a new foreign key constraint with the desired onDelete behavior
            // $table->foreignIdFor(Plandue::class)->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
