<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('ticket_status_id')->references('id')->on('ticket_statuses')->onDelete('cascade');
            $table->foreign('priority_id')->references('id')->on('priorities')->onDelete('cascade');
            $table->foreign('app_channel_id')->references('id')->on('app_channel')->onDelete('cascade');
            $table->foreign('app_channel_category_id')->references('id')->on('app_channel_categories')->onDelete('cascade');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('attachments_messages', function (Blueprint $table) {
            $table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade');
            $table->foreign('file_id')->references('id')->on('files')->onDelete('cascade');
        });

        Schema::table('app_channel', function (Blueprint $table) {
            $table->foreign('app_id')->references('id')->on('apps')->onDelete('cascade');
            $table->foreign('channel_id')->references('id')->on('channels')->onDelete('cascade');
        });

        Schema::table('app_channel_categories', function (Blueprint $table) {
            $table->foreign('app_channel_id')->references('id')->on('app_channel')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['ticket_status_id']);
            $table->dropForeign(['priority_id']);
            $table->dropForeign(['app_channel_id']);
            $table->dropForeign(['app_channel_category_id']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['ticket_id']);
        });

        Schema::table('attachments_messages', function (Blueprint $table) {
            $table->dropForeign(['message_id']);
            $table->dropForeign(['file_id']);
        });

        Schema::table('app_channel', function (Blueprint $table) {
            $table->dropForeign(['app_id']);
            $table->dropForeign(['channel_id']);
        });

        Schema::table('app_channel_categories', function (Blueprint $table) {
            $table->dropForeign(['app_channel_id']);
        });
    }
};
