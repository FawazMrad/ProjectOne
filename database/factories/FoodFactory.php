<?php

namespace Database\Factories;

use App\Models\Food;
use Illuminate\Database\Eloquent\Factories\Factory;

class FoodFactory extends Factory
{
    protected $model = Food::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $descriptions_en = [
            "A delicious blend of fresh ingredients, perfect for any meal.",
            "A savory dish with rich flavors and a hint of spice.",
            "A classic comfort food that will warm your soul.",
            "A light and healthy option, packed with nutrients.",
            "A gourmet dish with an elegant presentation.",
            "A sweet treat that's perfect for dessert.",
            "A hearty meal that will keep you satisfied."
        ];

        $descriptions_ar = [
            "مزيج لذيذ من المكونات الطازجة، مثالي لأي وجبة.",
            "طبق شهي ذو نكهات غنية ولمسة من التوابل.",
            "طعام مريح كلاسيكي سيسعد قلبك.",
            "خيار خفيف وصحي مليء بالعناصر الغذائية.",
            "طبق فاخر ذو تقديم أنيق.",
            "حلوى شهية مثالية للتحلية.",
            "وجبة دسمة ستشعرك بالشبع."
        ];


        return [
            'name' => $this->faker->randomElement(['Pizza', 'Burger', 'Pasta', 'Salad', 'Sushi', 'Steak', 'Ice Cream']),
            'type' => $this->faker->randomElement(['main course', 'appetizer', 'dessert']),
            'description_en' => $this->faker->randomElement($descriptions_en),
            'description_ar' => $this->faker->randomElement($descriptions_ar),
            'cost' => $this->faker->numberBetween(5, 20),
            'image' => 'https://media.istockphoto.com/id/1316145932/photo/table-top-view-of-spicy-food.jpg?s=612x612&w=0&k=20&c=eaKRSIAoRGHMibSfahMyQS6iFADyVy1pnPdy1O5rZ98=',
        ];
    }
}
