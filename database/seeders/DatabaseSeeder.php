<?php

namespace Database\Seeders;

use App\Models\Attendee;
use App\Models\Category;
use App\Models\DecorationCategory;
use App\Models\DecorationItem;
use App\Models\DecorationItemReservation;
use App\Models\Drink;
use App\Models\DrinkReservation;
use App\Models\Event;
use App\Models\Food;
use App\Models\FoodReservation;
use App\Models\Furniture;
use App\Models\FurnitureReservation;
use App\Models\Preference;
use App\Models\Security;
use App\Models\SecurityReservation;
use App\Models\Sound;
use App\Models\SoundReservation;
use App\Models\Station;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueReservation;
use Carbon\Carbon;
use Database\Factories\PreferenceFactory;
use Illuminate\Database\Seeder;
use App\Helpers\QR_CodeHelper;

// Make sure to include the QR_CodeHelper

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Drink::factory()->count(44)->create();
        Food::factory()->count(25)->create();
        Venue::factory()->count(64)->create();
        Furniture::factory()->count(36)->create();
        DecorationCategory::factory()->count(20)->create();
        DecorationItem::factory()->count(50)->create();
        Sound::factory()->count(50)->create();
        Security::factory()->count(50)->create();
        Station::factory()->count(10)->create();
        User::factory()->count(15)->create()->each(function (User $user) {
            Preference::factory()->create([
                'user_id' => $user->id,
                'theme' => ['light', 'dark'][array_rand(['light', 'dark'])],
                'language' => ['en', 'ar'][array_rand(['en', 'ar'])],
                'notification_enabled' => rand(0, 1),
            ]);
        });
        Category::factory()->count(8)->create();
        Event::factory()->count(20)->create()->each(function (Event $event) {
            $data = [
                'id' => $event->id,
                'Description_ar' => $event->description_ar,
                'Description_en' => $event->description_en,
            ];
            QR_CodeHelper::generateAndSaveQrCode($data, 'Event');

            $eventStartDate = Carbon::parse($event->start_date);
            $randomHours = rand(2, 6);
            $event->end_date = $eventStartDate->copy()->addHours($randomHours);
            $eventCreatedAt = $eventStartDate->copy()->subYears(1)->addSeconds(rand(0, $eventStartDate->timestamp - $eventStartDate->copy()->subYears(1)->timestamp));
            $event->created_at = $eventCreatedAt;
            $event->save();

            VenueReservation::factory()->create([
                'event_id' => $event->id,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date]);
            FurnitureReservation::factory()->count(3)->create([
                'event_id' => $event->id,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,]);
            DecorationItemReservation::factory()->count(10)->create([
                'event_id' => $event->id,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,]);
            FoodReservation::factory()->count(5)->create([
                'event_id' => $event->id,
                'serving_date' => $event->start_date,]);
            DrinkReservation::factory()->count(5)->create([
                'event_id' => $event->id,
                'serving_date' => $event->start_date,]);
            SoundReservation::factory()->count(6)->create([
                'event_id' => $event->id,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,]);
            SecurityReservation::factory()->create([
                'event_id' => $event->id,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,]);

//            $usersCount = User::count();
//            $randomUsers = User::inRandomOrder()->take(min(10, $usersCount / 2))->get();
//            $randomUsers->each(function ($user) use ($eventStartDate, $event) {
//                Attendee::factory()->create([
//                    'user_id' => $user->id,
//                    'event_id' => $event->id,
//                    'purchase_date' => $eventStartDate->subDays(3),
//                ])->each(function (Attendee $attendee) use ($eventStartDate, $event) {
//                    $attendee->checked_in = now() >= $eventStartDate && $attendee->status != 'CANCELLED';
//                    $attendee->save();
//                });
//            });
        });
//        VenueReservation::factory()->count(20)->create();
//        FurnitureReservation::factory()->count(20)->create();
//        DecorationItemReservation::factory()->count(20)->create();
//        FoodReservation::factory()->count(20)->create();
//        DrinkReservation::factory()->count(20)->create();
//        SoundReservation::factory()->count(20)->create();
//        SecurityReservation::factory()->count(20)->create();

    }
}
