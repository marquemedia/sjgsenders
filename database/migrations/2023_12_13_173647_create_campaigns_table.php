<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->integer('sender_id')->nullable();
            $table->string('name', 191);
            $table->longText('location', 191)->nullable();
            $table->string('subject', 191)->nullable();
            $table->string('from_name', 255)->nullable();
            $table->string('reply_to_email', 255)->nullable();
            $table->string('sms_type', 255)->nullable();
            $table->json('post_data')->nullable();
            $table->longText('body')->nullable();
            $table->longText('json_body')->nullable();
            $table->datetime('schedule_time')->nullable();
            $table->enum('schedule_status', ['Now', 'Later'])->nullable();
            $table->enum('channel', ['email', 'sms', 'whatsapp'])->nullable();
            $table->enum('status', ['Active', 'DeActive', 'Completed', 'Ongoing'])->default('Active');
            $table->timestamp('last_corn_run')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaigns');
    }
}
