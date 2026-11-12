<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->text('address')->nullable()->after('email');
        $table->string('role')->default('resident')->after('password');
    });
}

    public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['address', 'role']);
    });
}
};
