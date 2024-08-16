<?php

namespace Database\Factories;

use App\Models\DecorationCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class DecorationCategoryFactory extends Factory
{
    protected $model = DecorationCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $names = [
            'Balloons',
            'Floral Arrangements',
            'Lighting',
            'Table Centerpieces',
            'Wall Art',
            'Thematic Decorations',
            'Curtains and Drapes',
            'Banners and Signage',
            'Tableware',
            'Candles and Holders',
            'Ribbons and Bows',
            'Confetti and Streamers',
            'Photo Booth Props',
            'Garlands and Wreaths',
            'Table Linens',
            'Seasonal Decorations',
            'Chandeliers',
            'Party Hats and Accessories',
            'Backdrops',
            'Favor Decorations',
        ];

        $englishDescriptions = [
            'Elegant and sophisticated items for special occasions.',
            'Bright and colorful decorations to liven up any event.',
            'Unique and creative decor for a memorable celebration.',
            'Classic and timeless decorations that never go out of style.',
            'Luxurious and high-quality items for an extravagant look.',
            'Fun and playful decorations perfect for any party.'
        ];

        $arabicDescriptions = [
            'أدوات أنيقة وراقية للمناسبات الخاصة.',
            'زينة مشرقة وملونة لإضفاء الحيوية على أي حدث.',
            'ديكورات فريدة ومبتكرة لحفل لا يُنسى.',
            'زينة كلاسيكية وأنيقة لا تخرج عن الموضة.',
            'أدوات فاخرة وعالية الجودة للحصول على مظهر فاخر.',
            'زينة ممتعة ومرحة مثالية لأي حفلة.'
        ];

        return [
            'name' => $this->faker->unique()->randomElement($names), // Randomly select a category name from predefined options
            'description_en' => $this->faker->randomElement($englishDescriptions), // English description from predefined list
            'description_ar' => $this->faker->randomElement($arabicDescriptions), // Arabic description from predefined list
            'icon' => $this->faker->imageUrl(100, 100, 'icon', true, 'icon'), // Placeholder icon image URL
        ];
    }
}
