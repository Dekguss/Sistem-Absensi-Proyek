<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyAttendancesTable extends Migration
{
    public function up()
    {
        // Drop columns if they exist
        if (Schema::hasColumn('attendances', 'check_in')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn('check_in');
            });
        }
        if (Schema::hasColumn('attendances', 'check_out')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn('check_out');
            });
        }
        if (Schema::hasColumn('attendances', 'notes')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn('notes');
            });
        }

        // Add new columns if they don't exist
        if (!Schema::hasColumn('attendances', 'status')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->enum('status', ['hadir', 'tidak_hadir', 'setengah_hari', '2_hari_kerja'])->default('hadir');
            });
        }
        if (!Schema::hasColumn('attendances', 'overtime_hours')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->integer('overtime_hours')->default(0);
            });
        }
    }

    public function down()
    {
        // Add back the dropped columns if they don't exist
        if (!Schema::hasColumn('attendances', 'check_in')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->time('check_in')->nullable();
            });
        }
        if (!Schema::hasColumn('attendances', 'check_out')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->time('check_out')->nullable();
            });
        }
        if (!Schema::hasColumn('attendances', 'notes')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->text('notes')->nullable();
            });
        }

        // Drop the new columns if they exist
        if (Schema::hasColumn('attendances', 'status')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
        if (Schema::hasColumn('attendances', 'overtime_hours')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn('overtime_hours');
            });
        }
    }
}
