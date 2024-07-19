<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Organisation;
use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user1 = User::factory()->create();
        $full_name1 = explode(" ", $user1->name);

        $profile1 = Profile::factory()->create(['user_id' => $user1->id, 'first_name' => $full_name1[0], 'last_name' => $full_name1[1]]);

        $product1 = Product::factory()->create(['user_id' => $user1->id]);
        $product2 = Product::factory()->create(['user_id' => $user1->id]);
        // $user1->profile()->save(Profile::factory()->make(['user_id' => $user1->id, 'first_name' => $full_name1[0], 'last_name' => $full_name1[1]]));
        // $user1->profile()->create(['user_id' => $user1->id, 'first_name' => $full_name1[0], 'last_name' => $full_name1[1]]);
        // $user1->products()->save(Product::factory(2)->make(['user_id' => $user1->id]));

        $user2 = User::factory()->create();
        $full_name2 = explode(" ", $user2->name);

        $profile2 = Profile::factory()->create(['user_id' => $user2->id, 'first_name' => $full_name2[0], 'last_name' => $full_name2[1]]);

        $product3 = Product::factory()->create(['user_id' => $user2->id]);
        $product4 = Product::factory()->create(['user_id' => $user2->id]);
        // $user2->profile()->save(Profile::factory()->make(['user_id' => $user2->id, 'first_name' => $full_name2[0], 'last_name' => $full_name2[1]]));
        // $user2->profile()->create(['user_id' => $user1->id, 'first_name' => $full_name2[0], 'last_name' => $full_name2[1]]);
        // $user1->products()->save(Product::factory(2)->make(['user_id' => $user2->id]));

        $organisation1 = Organisation::factory()->create();
        $organisation2 = Organisation::factory()->create();
        $organisation3 = Organisation::factory()->create();

        $organisation1->users()->attach([$user1->id, $user2->id]);
        $organisation2->users()->attach([$user1->id, $user2->id]);
        $organisation3->users()->attach($user2->id);

    }
}
