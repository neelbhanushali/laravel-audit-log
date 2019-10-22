<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_trail', function (Blueprint $table) {
            $table->string('entity_type');
            $table->string('entity_id');

            $table->string('related_type');
            $table->string('related_id');
            $table->string('relation');

            $table->string('activity');
            $table->string('user_id')->nullable();

            $table->text('before_transaction');
            $table->text('after_transaction');
            $table->text('difference');

            $table->text('token')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->string('ua')->nullable();
            $table->string('url')->nullable();

            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_trail');
    }
}
