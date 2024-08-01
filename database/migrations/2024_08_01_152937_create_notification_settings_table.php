<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users');
            $table->boolean('email_notification_activity_in_workspace')->default(false);
            $table->boolean('mobile_push_notifications')->default(false);
            $table->boolean('email_notification_always_send_email_notifications')->default(false);
            $table->boolean('email_notification_email_digest')->default(false);
            $table->boolean('email_notification_announcement_and_update_emails')->default(false);
            $table->boolean('slack_notifications_activity_on_your_workspace')->default(false);
            $table->boolean('slack_notifications_always_send_email_notifications')->default(false);
            $table->boolean('slack_notifications_announcement_and_update_emails')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
