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
            $table->string('uri')->default('');
            $table->string('table_name')->default('');
            $table->unsignedTinyInteger('enabled')->default('1')->comment('1=Y 0=N');
            $table->unsignedTinyInteger('soft_deleted')->default('1')->comment('1=Y 0=N');
            $table->unsignedTinyInteger('response_json')->default('1')->comment('1=Y 0=N');
            $table->string('model_name')->nullable();
            $table->text('calls')->nullable();
            $table->text('grid_model_calls')->nullable();
            $table->text('detail_model_calls')->nullable();
            $table->text('grid_calls')->nullable();
            $table->text('filter_calls')->nullable();
            $table->text('form_calls')->nullable();
            $table->timestamps();
        });
        Schema::create($this->config('database.omni_column_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('column_name')->default('');
            $table->string('table_name')->default('');
            $table->string('label')->default('');
            $table->string('input_type')->default('');
            $table->string('default')->nullable();
            $table->text('dict')->nullable();
            $table->unsignedTinyInteger('grid_showed')->default('1')->comment('1=Y 0=N');
            $table->unsignedTinyInteger('mode')->default('1')->comment('0=CU 1=C 2=U 3=-');
            $table->text('rules')->nullable();
            $table->text('grid_column_calls')->nullable();
            $table->text('form_column_calls')->nullable();
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
