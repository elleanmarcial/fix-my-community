<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->string('location');

            $table->boolean('is_anonymous')->default(false);

            $table->string('status')->default('Submitted');

            $table->string('assigned_to')->nullable();
            $table->text('admin_notes')->nullable();

            $table->timestamp('reported_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};