<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSettings extends Model
{
    use HasFactory, HasUuids;

    // database/migrations/create_notification_settings_table
    public function up()
    {
    Schema::create('notification_settings', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->boolean('email_notification_activity_in_workspace')->default(false);
        $table->boolean('email_notification_always_send_email_notifications')->default(false);
        $table->boolean('email_notification_email_digest')->default(false);
        $table->boolean('email_notification_announcement_and_update_emails')->default(false);
        $table->boolean('slack_notifications_activity_on_your_workspace')->default(false);
        $table->boolean('slack_notifications_always_send_email_notifications')->default(false);
        $table->boolean('slack_notifications_announcement_and_update_emails')->default(false);
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->timestamps();
    });
    }
}
