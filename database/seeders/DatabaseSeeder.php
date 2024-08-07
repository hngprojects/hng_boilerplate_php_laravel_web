<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Job;
use App\Models\Organisation;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserSubscription;
use Database\Factories\BlogFactory;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //create an admin user
        $this->call([
            AdminSeeder::class
        ]);

        $user1 = User::factory()->has(
            Profile::factory()
                    ->state(function (array $attributes, User $user) {
                        $full_name = explode(" ", $user->name);
                        return ['first_name' => $full_name[0], 'last_name' => $full_name[1]];
                    })
        // )->hasProducts(2)->hasPreferences(5)->create();
        )->hasProducts(2)->create();

        $user2 = User::factory()->has(
            Profile::factory()
                    ->state(function (array $attributes, User $user) {
                        $full_name = explode(" ", $user->name);
                        return ['first_name' => $full_name[0], 'last_name' => $full_name[1]];
                    })
        )->hasProducts(2)->create();

        $organisation1 = Organisation::factory()->create();
        $organisation2 = Organisation::factory()->create();
        $organisation3 = Organisation::factory()->create();

        $organisation1->users()->attach([$user1->id, $user2->id]);
        $organisation2->users()->attach([$user1->id, $user2->id]);
        $organisation3->users()->attach($user2->id);

        $job1 = Job::factory()->create();
        $job2 = Job::factory()->create();
        $job1->users()->attach($user1->id);
        $job2->users()->attach($user2->id);


        $this->call(CategoriesTableSeeder::class);
        $this->call([ArticlesTableSeeder::class]);
        $this->call(UserJobSeeder::class);
        $this->call(BlogSeeder::class);


        UserSubscription::factory()->create();

        $this->call(BillingPlanSeeder::class);
        $this->call(SubscriptionPlanSeeder::class);

        // Call individual seeders here
        $this->call([
            SizeSeeder::class,
            ProductSeeder::class,
            ProductVariantSeeder::class,
            ProductVariantSizeSeeder::class,
            FaqSeeder::class,
            UserNotificationSeeder::class,
            NotificationSettingSeeder::class,
            OrderSeeder::class,
            SqueezePageSeeder::class,
            TimezoneSeeder::class,
        ]);

        $this->call([
            QuestsSeeder::class,
            QuestMessageSeeder::class,
        ]);

    }
}
