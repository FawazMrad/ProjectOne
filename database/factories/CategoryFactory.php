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

        return [
            'name' => $this->faker->unique()->randomElement($names), // Randomly select a category name from predefined options
            'description_en' => $this->faker->randomElement($englishDescriptions), // Randomly select an English description
            'description_ar' => $this->faker->randomElement($arabicDescriptions), // Randomly select an Arabic description
            'icon' => $this->faker->imageUrl(256, 256, 'abstract', true, 'icon'), // Optional icon URL
        ];
    }
}
