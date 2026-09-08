<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImpersonationLogsTable extends Migration
{
    public function up()
    {
        Schema::create('impersonation_logs', function (Blueprint $table) {
            $table->id();
            $table->string('admin_user_id', 50);
            $table->string('target_user_id', 50);
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();

            $table->foreign('admin_user_id')->references('id')->on('users');
            $table->foreign('target_user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('impersonation_logs');
    }
}
