<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $names = [
            'Concert',
            'Workshop',
            'Conference',
            'Festival',
            'Seminar',
            'Meetup',
            'Trade Show',
            'Exhibition'
        ];

        $englishDescriptions = [
            'An exciting live performance featuring various artists.',
            'An interactive session for learning new skills and knowledge.',
            'A gathering of professionals to discuss industry trends and advancements.',
            'A large-scale event featuring multiple activities and performances.',
            'An educational event focused on specific topics or skills.',
            'A casual event for networking and meeting new people.',
            'A large event showcasing products and services from various industries.',
            'An event displaying artworks, crafts, or products from different creators.'
        ];

        $arabicDescriptions = [
            'أداء مباشر مثير يضم فنانين مختلفين.',
            'جلسة تفاعلية لتعلم مهارات ومعرفة جديدة.',
            'تجمع للمحترفين لمناقشة اتجاهات وصيحات الصناعة.',
            'حدث كبير يضم مجموعة من الأنشطة والعروض.',
            'حدث تعليمي يركز على مواضيع أو مهارات معينة.',
            'حدث غير رسمي للتواصل والتعرف على أشخاص جدد.',
            'حدث كبير يعرض المنتجات والخدمات من مختلف الصناعات.',
            'حدث يعرض الأعمال الفنية أو الحرف اليدوية أو المنتجات من صانعين مختلفين.'
        ];

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

        return [
            'name' => $this->faker->unique()->randomElement($names), // Randomly select a category name from predefined options
            'description_en' => $this->faker->randomElement($englishDescriptions), // Randomly select an English description
            'description_ar' => $this->faker->randomElement($arabicDescriptions), // Randomly select an Arabic description
            'icon' => $this->faker->randomElement($images),
        ];
    }
}
