<?php

namespace Database\Factories;

use App\Models\DecorationItem;
use App\Models\DecorationCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class DecorationItemFactory extends Factory
{
    protected $model = DecorationItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Example lists of descriptions
        $englishDescriptions = [
            'A beautiful arrangement of flowers to brighten up any space.',
            'Elegant lights that add a touch of sophistication to your decor.',
            'Colorful balloons that bring joy to any celebration.',
            'Decorative ribbons perfect for adding a festive touch.',
            'Scented candles that enhance the ambiance of your event.',
            'Traditional lanterns that provide a warm and inviting glow.'
        ];

        $arabicDescriptions = [
            'ترتيب زهور جميل يضيء أي مكان.',
            'أضواء أنيقة تضيف لمسة من الأناقة إلى ديكورك.',
            'بالونات ملونة تجلب الفرح لأي احتفال.',
            'أشرطة زخرفية مثالية لإضافة لمسة احتفالية.',
            'شموع معطرة تعزز من جو الحدث.',
            'فوانيس تقليدية تعطي توهجًا دافئًا وجذابًا.'
        ];

        return [
            'decoration_category_id' => DecorationCategory::inRandomOrder()->value('id'), // Get a random ID from the DecorationCategory table
            'name' => $this->faker->randomElement(['Flowers', 'Lights', 'Balloons', 'Ribbons', 'Candles', 'Lanterns']),
            'image' => $this->faker->imageUrl(640, 480, 'decoration', true, 'decoration'),
            'description_en' => $this->faker->randomElement($englishDescriptions), // English description from predefined list
            'description_ar' => $this->faker->randomElement($arabicDescriptions), // Arabic description from predefined list
            'quantity' => $this->faker->numberBetween(1, 100),
            'cost' => $this->faker->randomFloat(2, 5, 500), // Random float between 5.00 and 500.00
        ];
    }
}
