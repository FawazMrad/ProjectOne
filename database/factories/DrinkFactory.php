<?php

namespace Database\Factories;

use App\Models\Drink;
use Illuminate\Database\Eloquent\Factories\Factory;

class DrinkFactory extends Factory
{
    protected $model = Drink::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $descriptions_en = [
            "A refreshing blend of citrus and tropical flavors.",
            "A classic cola taste with a hint of vanilla.",
            "Rich and full-bodied red wine, perfect for a cozy evening.",
            "Crisp and clean sparkling water, naturally sourced.",
            "Smooth whiskey with a smoky finish.",
            "A zesty and tangy orange juice, freshly squeezed.",
            "A minty and sweet mojito, perfect for summer."
        ];

        $descriptions_ar = [
            "مزيج منعش من نكهات الحمضيات والفواكه الاستوائية.",
            "طعم كولا كلاسيكي مع لمسة من الفانيليا.",
            "نبيذ أحمر غني وكامل الجسم، مثالي لأمسية مريحة.",
            "مياه غازية نقية ومنعشة، مستخرجة من الطبيعة.",
            "ويسكي ناعم مع لمسة نهائية مدخنة.",
            "عصير برتقال حامض ومنعش، معصور طازج.",
            "موهيتو منعش ونعناعي، مثالي لفصل الصيف."
        ];
        $type = $this->faker->randomElement(['soft drink', 'juice', 'alcohol', 'water']);
        $ageRequired = ($type === 'alcohol') ? $this->faker->numberBetween(18, 21) : 10;

        return [
            'name' => $this->faker->randomElement(['Coca-Cola', 'Pepsi', 'Orange Juice', 'Red Wine', 'Whiskey', 'Sparkling Water', 'Mojito']),
            'type' => $type,
            'description_en' => $this->faker->randomElement($descriptions_en),
            'description_ar' => $this->faker->randomElement($descriptions_ar),
            'cost' => $this->faker->randomFloat(2, 1, 20),
            'age_required' => $ageRequired,
            'image' => 'https://img.freepik.com/free-photo/fresh-cocktails-with-ice-lemon-lime-fruits-generative-ai_188544-12370.jpg'
        ];
    }
}
