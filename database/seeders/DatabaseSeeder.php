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
use App\Models\Favourite;
use App\Models\Food;
use App\Models\FoodReservation;
use App\Models\Friendship;
use App\Models\Furniture;
use App\Models\FurnitureReservation;
use App\Models\Preference;
use App\Models\Rating;
use App\Models\Security;
use App\Models\SecurityReservation;
use App\Models\Sound;
use App\Models\SoundReservation;
use App\Models\Station;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueReservation;
use App\Models\Wallet;
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
        //Seeding Providers
        Drink::factory()->count(10)->create();
        Food::factory()->count(10)->create();
        Venue::factory()->count(10)->create();
        Furniture::factory()->count(10)->create();
        DecorationCategory::factory()->count(10)->create();
        DecorationItem::factory()->count(10)->create();
        Sound::factory()->count(10)->create();
        Security::factory()->count(10)->create();
        Station::factory()->count(10)->create();


        //Seeding Users With Wallets,Preferences And Friendships
        User::factory()->count(15)->create()->each(function (User $user) {
            $qrData = [
                'id' => $user->id,
                'user name' => " $user->first_name  $user->last_name",
                'phone number' => $user->phone_number,
                'user email' => $user->email
            ];
            QR_CodeHelper::generateAndSaveQrCode($qrData, 'User');
            $user->save();

            Preference::factory()->create([
                'user_id' => $user->id,
                'theme' => ['light', 'dark'][array_rand(['light', 'dark'])],
                'language' => ['en', 'ar'][array_rand(['en', 'ar'])],
                'notification_enabled' => rand(0, 1),
            ]);

            Wallet::factory()->create([
                'user_id' => $user->id,
            ]);
            for ($i = 0; $i < 3; $i++) {
                do {
                    $receiver = User::inRandomOrder()->first();
                    $status = fake()->randomElement(['FOLLOWING', 'MUTUAL', 'BLOCKED']);
                    $mutualAt = $status === 'MUTUAL' ? fake()->dateTimeBetween('-1 year', 'now') : null;
                    $blockerId = $status === 'BLOCKED' ? fake()->randomElement([$user->id, $receiver->id]) : null;
                } while ($user->id === $receiver->id);
                if (!Friendship::query()->where('sender_id', $user->id)->where('receiver_id', $receiver->id)->first()) {
                    Friendship::factory()->create([
                        'sender_id' => $user->id,
                        'receiver_id' => $receiver->id,
                        'status' => $status,
                        'mutual_at' => $mutualAt,
                        'blocker_id' => $blockerId,
                    ]);
                }
            }
            for ($i = 0; $i < 3; $i++) {
                do {
                    $sender = User::inRandomOrder()->first();
                    $status = fake()->randomElement(['FOLLOWING', 'MUTUAL', 'BLOCKED']);
                    $mutualAt = $status === 'MUTUAL' ? fake()->dateTimeBetween('-1 year', 'now') : null;
                    $blockerId = $status === 'BLOCKED' ? fake()->randomElement([$sender->id, $user->id]) : null;

                } while ($user->id === $sender->id);
                if (!Friendship::query()->where('sender_id', $sender->id)->where('receiver_id', $user->id)->first()) {
                    Friendship::factory()->create([
                        'sender_id' => $sender->id,
                        'receiver_id' => $user->id,
                        'status' => $status,
                        'mutual_at' => $mutualAt,
                        'blocker_id' => $blockerId,
                    ]);
                }
            }
        });


        //Seeding Categories and Events With Reservations and Attendees
        Category::factory()->count(8)->create();
        Event::factory()->count(1)->create()->each(function (Event $event) {
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
            DecorationItemReservation::factory()->count(3)->create([
                'event_id' => $event->id,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,]);
            FoodReservation::factory()->count(5)->create([
                'event_id' => $event->id,
                'serving_date' => $event->start_date,]);
            DrinkReservation::factory()->count(5)->create([
                'event_id' => $event->id,
                'serving_date' => $event->start_date,]);
            SoundReservation::factory()->count(4)->create([
                'event_id' => $event->id,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,]);
            SecurityReservation::factory()->create([
                'event_id' => $event->id,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,]);

            $uniqueUsers = User::inRandomOrder()
                ->take(15)
                ->get();
            foreach ($uniqueUsers as $uniqueUser) {
                // Create an attendee for each unique user
                Attendee::factory()->create([
                    'user_id' => $uniqueUser->id,
                    'event_id' => $event->id,
                    'purchase_date' => $eventStartDate->copy()->subDays(3),
                ]);
            }

// Update created attendees
            Attendee::where('event_id', $event->id)->whereIn('user_id', $uniqueUsers->pluck('id'))->each(function (Attendee $attendee) use ($eventStartDate) {
                $qrData = [
                    'id' => $attendee->id,
                    'userId' => $attendee->user_id,
                    'eventId' => $attendee->event_id,
                    'seatNumber' => $attendee->seat_number
                ];
                QR_CodeHelper::generateAndSaveQrCode($qrData, 'Attendee');
                $attendee->checked_in = $eventStartDate->isPast() && $attendee->status === 'ATTENDING';
                $attendee->save();
            });
        });

        //Seeding Favourites
        $users = User::query()->get();
        foreach ($users as $user) {
            Favourite::factory()->count(5)->create([
                'user_id' => $user->id,
            ]);
        }


        // Seed ratings
        $ratableAttendees = Attendee::where('checked_in', true)->get();

        foreach ($ratableAttendees as $attendee) {
            Rating::factory()->create([
                'user_id' => $attendee->user_id,
                'event_id' => $attendee->event_id,
            ]);
        }
    }
}
