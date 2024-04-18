<?php

use App\Models\User;
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
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('partname');
            $table->string('outpart')->unique();
            $table->string('trupart');
            $table->smallInteger('snp');
            $table->smallInteger('weight');
            $table->foreignIdFor(User::class,'created_by')->constrained('users');
            $table->foreignIdFor(User::class,'updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};
