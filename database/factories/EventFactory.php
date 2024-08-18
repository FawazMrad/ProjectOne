<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Helpers\QR_CodeHelper;

class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $images = [
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR1FGN4kr4iWHRUMFDuZ25JzwMnpieSCRwUVw&s',
            'https://t4.ftcdn.net/jpg/01/99/21/01/360_F_199210113_PO4I2F3CAfEhCnVnWNveK9mlgWyxY8jn.jpg',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRS3WQDc7L1imAhgRoPsPgMBKm_XD2lkCFsvA&s',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSPbV7FcgNsVvEhetrjnh1ALJmdVI1tKAzwHw&s',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSYoCWtX1_P6teC6wu9T3b07Wz2-OLOPnHR-g&s',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTibo1UK0gtVhTyAF4MuIwxaVz69z1nNeZEJw&s',
            'https://media-api.xogrp.com/images/7bdee2ed-62a6-4e17-b616-d2e5dcf117bb~cr_0.0.2471.1655?quality=50',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQpbNqmTF-5zC3Sf5LpgA-Y1qf7mFYgHDBQ-w&s'
        ];

        $descriptionsEn = [
            'An exciting music festival featuring top artists from around the world.',
            'A gourmet food fair offering a variety of international cuisines.',
            'A fashion show displaying the latest trends in the industry.',
            'A tech conference exploring the future of artificial intelligence.',
            'A charity gala to raise funds for underprivileged communities.',
            'A vibrant cultural exhibition showcasing traditional art and crafts.',
            'A sports event bringing together the best teams in the league.',
            'An educational workshop on personal development and leadership skills.',
            'A film screening event celebrating the work of independent filmmakers.',
            'A networking event for professionals in the tech industry.',
        ];

        // Predefined Arabic descriptions
        $descriptionsAr = [
            'مهرجان موسيقي مثير يضم أفضل الفنانين من جميع أنحاء العالم.',
            'معرض طعام فاخر يقدم مجموعة متنوعة من المأكولات العالمية.',
            'عرض أزياء يعرض أحدث الاتجاهات في الصناعة.',
            'مؤتمر تقني يستكشف مستقبل الذكاء الاصطناعي.',
            'حفل خيري لجمع التبرعات للمجتمعات المحرومة.',
            'معرض ثقافي نابض بالحياة يعرض الفن والحرف التقليدية.',
            'حدث رياضي يجمع أفضل الفرق في الدوري.',
            'ورشة عمل تعليمية حول تطوير الذات ومهارات القيادة.',
            'عرض أفلام يحتفل بأعمال صناع الأفلام المستقلين.',
            'حدث للتواصل المهنيين في صناعة التكنولوجيا.',
        ];
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? null,
            'category_id' => Category::inRandomOrder()->first()->id ?? null,
            'title' => $this->faker->regexify('[A-Za-z]{6} [A-Za-z]{8}'),
            'description_ar' => $this->faker->randomElement($descriptionsAr),
            'description_en' => $this->faker->randomElement($descriptionsEn),
            'start_date' => $this->faker->dateTimeBetween(now()->subYears(3), now()->addYear()),
            'min_age' => $this->faker->numberBetween(10, 18),
            'is_paid' => $this->faker->boolean(),
            'is_private' => $this->faker->boolean(),
            'attendance_type' => $this->faker->randomElement(['INVITATION', 'TICKET']),
            'total_cost' => $this->faker->numberBetween(0, 2000),
            'ticket_price' => $this->faker->numberBetween(0, 100),
            'vip_ticket_price' => $this->faker->numberBetween(0, 200),
            'image' => $this->faker->randomElement($images),
            'rating' => $this->faker->randomFloat(1, 0, 5),
        ];
    }
}
