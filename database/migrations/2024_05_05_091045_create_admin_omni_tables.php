<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function getConnection()
    {
        return $this->config('database.connection') ?: config('database.default');
    }

    public function config($key)
    {
        return config('admin.' . $key);
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->config('database.omni_route_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
        });
        Schema::create($this->config('database.omni_column_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->config('database.omni_route_table'));
        Schema::dropIfExists($this->config('database.omni_column_table'));
    }
};
