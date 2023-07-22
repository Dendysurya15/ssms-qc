<?php

namespace Database\Factories;

use App\Models\Landscape;
use Illuminate\Database\Eloquent\Factories\Factory;

class LandscapeFactory extends Factory
{
    protected $model = Landscape::class;
    private $usedDatetimes = [];

    public function definition()
    {
        $count = 5;

        $foto_temuan = $this->generateRandomItems($count, 'jpg');
        $komentar_temuan = $this->generateRandomItems($count);
        $nilai = $this->generateRandomItems($count, null, 'integer', 1, 5); // Use 'integer' format for nilai
        $komentar = $this->generateRandomItems($count);
        $datetime = $this->generateUniqueDatetime();
        return [
            'datetime' => $datetime,
            'est' => $this->faker->randomElement(['KNE', 'PLE', 'RDE', 'SLE', 'BKE', 'KDE', 'RGE', 'SGE', 'NBE', 'SYE', 'UPE', 'SGE', 'NKE', 'PDE', 'SPE', 'BTE', 'MLE', 'BDE', 'KTE', 'PKE', 'BSE', 'LME1', 'KTE4']),
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

    protected function generateUniqueDatetime()
    {
        $datetime = $this->faker->dateTimeBetween('2023-01-01', '2023-01-31')->format('Y-m-d H:i:s');

        // Check if the datetime is already used, if so, regenerate until unique
        while (in_array($datetime, $this->usedDatetimes)) {
            $datetime = $this->faker->dateTimeBetween('2023-07-01', '2023-07-30')->format('Y-m-d H:i:s');
        }

        // Store the generated datetime in the usedDatetimes array to avoid duplicates
        $this->usedDatetimes[] = $datetime;

        return $datetime;
    }


    protected function generateRandomItems($count, $extension = null, $format = null, $min = null, $max = null)
    {
        $items = [];

        for ($i = 0; $i < $count; $i++) {
            $item = $this->faker->word;
            if ($extension) {
                $item .= '.' . $extension;
            }
            if ($format === 'integer') {
                $item = $this->faker->numberBetween($min, $max); // Generate a random integer within the specified range
            } elseif ($format) {
                $item = $this->faker->numerify($format);
            }
            $items[] = $item;
        }

        return $items;
    }

    public function configure()
    {
        return $this->afterMaking(function (Landscape $landscape) {
            $landscape->setConnection('mysql2');
        })->afterCreating(function (Landscape $landscape) {
            $landscape->setConnection('mysql2');
        });
    }
}
