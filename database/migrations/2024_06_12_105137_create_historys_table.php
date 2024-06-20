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
        Schema::create('historys', function (Blueprint $table) {
            $table->id();
            $table->string('planid');
            $table->string('customer');
            $table->string('outside');
            $table->string('thpart');
            $table->string('qty');
            $table->string('status');
            $table->foreignIdFor(User::class , 'created_by')->constrained('users');
            $table->foreignIdFor(User::class , 'updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historys');
    }
};
