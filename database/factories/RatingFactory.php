<?php

namespace Database\Factories;

use App\Models\Rating;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    protected $model = Rating::class;

    public function definition(): array
    {
        // Generate individual ratings
        $venueRating = $this->faker->randomFloat(2, 0, 5);
        $decorRating = $this->faker->randomFloat(2, 0, 5);
        $musicRating = $this->faker->randomFloat(2, 0, 5);
        $foodRating = $this->faker->randomFloat(2, 0, 5);
        $drinkRating = $this->faker->randomFloat(2, 0, 5);

        // Calculate the aggregate rating
        $totalRatings = 5;
        $sumRatings = $venueRating + $decorRating + $musicRating + $foodRating + $drinkRating;
        $aggregateRating = $sumRatings / $totalRatings;

        // Define an array of comments in both English and Arabic
        $comments = [
            'en' => [
                'Great event, really enjoyed the music!',
                'The food was excellent, but the venue was too crowded.',
                'I had a fantastic time, the decor was stunning!',
                'The drinks were subpar, but the overall experience was good.',
                'Amazing event, will definitely come again!',
            ],
            'ar' => [
                'حدث رائع، استمتعت حقاً بالموسيقى!',
                'الطعام كان ممتازاً، ولكن المكان كان مزدحماً جداً.',
                'لقد قضيت وقتاً رائعاً، كان الديكور مذهلاً!',
                'المشروبات كانت دون المستوى، ولكن التجربة العامة كانت جيدة.',
                'حدث مذهل، سأعود بالتأكيد!',
            ],
        ];

        // Randomly select a comment from either the English or Arabic array
        $language = $this->faker->randomElement(['en', 'ar']);
        $comment = $this->faker->randomElement($comments[$language]);

        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'comment' => $comment,
            'venue_rating' => $venueRating,
            'decor_rating' => $decorRating,
            'music_rating' => $musicRating,
            'food_rating' => $foodRating,
            'drink_rating' => $drinkRating,
            'aggregate_rating' => round($aggregateRating, 2),
        ];
    }
}
