<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('worker_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['1_hari', 'setengah_hari', 'tidak_bekerja', '2_hari', '1.5_hari'])->default('1_hari');
            $table->integer('overtime_hours')->default(0);
            $table->timestamps();

            // Add unique constraint to prevent duplicate attendance entries
            $table->unique(['project_id', 'worker_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
