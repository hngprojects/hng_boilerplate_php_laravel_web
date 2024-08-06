<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NotificationSetting>
 */
class NotificationSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mobile_push_notifications' => $this->faker->boolean,
            'email_notification_activity_in_workspace' => $this->faker->boolean,
            'email_notification_always_send_email_notifications' => $this->faker->boolean,
            'email_notification_email_digest' => $this->faker->boolean,
            'email_notification_announcement_and_update_emails' => $this->faker->boolean,
            'slack_notifications_activity_on_your_workspace' => $this->faker->boolean,
            'slack_notifications_always_send_email_notifications' => $this->faker->boolean,
            'slack_notifications_announcement_and_update_emails' => $this->faker->boolean,
            'user_id' => User::factory(),
        ];
    }
}
