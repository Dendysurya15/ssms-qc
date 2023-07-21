<?php

namespace Database\Factories;

use App\Models\Perumahan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PerumahanFactory extends Factory
{
    protected $model = Perumahan::class;

    public function definition()
    {
        $count = $this->faker->numberBetween(1, 5); // Random number of items in the arrays

        $foto_temuan = $this->generateRandomItems($count, 'jpg');
        $komentar_temuan = $this->generateRandomItems($count);
        $nilai = $this->generateRandomItems($count, null, 'integer'); // Use 'integer' format for nilai
        $komentar = $this->generateRandomItems($count);
        $datetime = $this->faker->dateTimeBetween('2023-07-01', '2023-07-31')->format('Y-m-d H:i:s');

        return [
            'datetime' => $datetime,
            'est' => $this->faker->randomElement(['KNE', 'NBE', 'PLE', 'BDE']),
            'afd' => $this->faker->randomElement(['OA', 'OB', 'OC', 'OD']),
            'petugas' => $this->faker->name(),
            'pendamping' => $this->faker->name(),
            'penghuni' => $this->faker->name(),
            'tipe_rumah' => $this->faker->randomElement(['A', 'B', 'C']),
            'foto_temuan' => implode('$', $foto_temuan),
            'komentar_temuan' => implode('$', $komentar_temuan),
            'nilai' => implode('$', $nilai),
            'komentar' => implode('$', $komentar),
        ];
    }

    protected function generateRandomItems($count, $extension = null, $format = null)
    {
        $items = [];

        for ($i = 0; $i < $count; $i++) {
            $item = $this->faker->word;
            if ($extension) {
                $item .= '.' . $extension;
            }
            if ($format === 'integer') {
                $item = $this->faker->randomNumber(2); // Generate a random integer between 0 and 99
            } elseif ($format) {
                $item = $this->faker->numerify($format);
            }
            $items[] = $item;
        }

        return $items;
    }

    public function configure()
    {
        return $this->afterMaking(function (Perumahan $perumahan) {
            $perumahan->setConnection('mysql2');
        })->afterCreating(function (Perumahan $perumahan) {
            $perumahan->setConnection('mysql2');
        });
    }
}
