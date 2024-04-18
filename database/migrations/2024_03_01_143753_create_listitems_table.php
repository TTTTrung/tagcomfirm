<?php

use App\Models\Plandue;
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
        Schema::create('listitems', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Plandue::class)->constrained();
            $table->foreignIdFor(User::class , 'created_by')->constrained('users');
            $table->foreignIdFor(User::class , 'updated_by')->nullable()->constrained('users');
            $table->date('duedate');
            $table->string('issue');
            $table->string('outpart');
            $table->smallInteger('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listitems');
    }
};
