<?php

class FoxFit
{
    private int $protein = 4;
    private int $carbon = 4;
    private int $fat = 9;

    private float $averageProtein = 2.6;

    private int $caloNeedToUpgrade = 7700;

    private float $proteinNeeded = 0;
    private float $carbonNeeded = 0;
    private float $fatNeeded = 0;

    // https://www.thehinh.com/tool/TDEE/tinh-tdee.html
    private $TDEE;
    private $weight;

    const NORMAL_CALCULATOR = 0;
    const SPECIAL_CALCULATOR = 1;

    private $list = [30, 35, 35];
    private int $type;
    private array $foodList;

    private $foods = [
        'chicken' => [25.2, 0, 1], // 100gr
        'beef' => [26.1, 0, 11.8], // 100gr
        'whey' => [24, 3, 1],
        'apple' => [0.5, 13.8, 0.1], // max: 2
        'avocado' => [2, 1.9, 19], // 100gr
        'hamburger' => [13.3, 30.3, 10.1],
        'rice' => [2.7, 28.2, 0.3],
        'cheese' => [1.7, 0, 3.5],
        'banana' => [1, 23, 0.3], // max: 2
        'corn' => [0, 19, 1.4],
        'pho' => [17, 31, 4],
        'yaourt' => [3.7, 12, 2.5],
        'bread' => [13.2, 30, 2.5],
        'milk_no_sugar' => [8.14, 12, 8],
        'watermelon' => [1.45, 17.97, 0.36],
    ];

    public function __construct(float $weight, float $TDEE, int $type = self::NORMAL_CALCULATOR)
    {
        $this->weight = $weight;
        $this->TDEE = $TDEE;
        $this->type = $type;
    }

    public function setList(array $list): void
    {
        $this->list = $list;
    }

    public function setFoodList(array $foodList): void {
        $this->foodList = $foodList;
    }

    private function specialCalculator(): void
    {
        $this->proteinNeeded = $this->averageProtein * $this->weight;
        $this->proteinNeeded = round($this->proteinNeeded);

        $left = $this->TDEE - $this->proteinToCalo();

        $this->fatNeeded = $left / 15;
        $this->fatNeeded = round($this->fatNeeded);

        $this->carbonNeeded = $this->fatNeeded * 1.5;
        $this->carbonNeeded = round($this->carbonNeeded);
    }

    private function normalCalculator(): void
    {
        $this->proteinNeeded = (($this->TDEE / 100) * $this->list[0]) / $this->protein;
        $this->proteinNeeded = round($this->proteinNeeded);

        $this->carbonNeeded = (($this->TDEE / 100) * $this->list[1]) / $this->carbon;
        $this->carbonNeeded = round($this->carbonNeeded);

        $this->fatNeeded = (($this->TDEE / 100) * $this->list[2]) / $this->fat;
        $this->fatNeeded = round($this->fatNeeded);
    }

    public function calculator(): void
    {
        if ($this->type == self::SPECIAL_CALCULATOR) {
            $this->specialCalculator();
            return;
        }
        $this->normalCalculator();
    }

    public function getTotalCalories(): float
    {
        return $this->proteinToCalo() + $this->carbonToCalo() + $this->fatToCalo();
    }

    public function showResult(): void
    {
        $this->calculator();

        $lines = [
            'Weight: ' . $this->weight,
            'Protein: ' . $this->proteinNeeded,
            'Carbon: ' . $this->carbonNeeded,
            'Fat: ' . $this->fatNeeded,
            'Total: ' . $this->getTotalCalories() . ' >< TDEE: ' . $this->TDEE,
        ];

        $total = $this->calculateFood($this->foodList);


        foreach ($lines as $k => $line) {
            if ($k === 0) {
                $this->printLine();
            }
            $this->printLine($line);
        }

        $this->printLine(implode('-', $total));
    }

    private function calculateFood(array $foodList): array {
        $totalProtein = $totalCarbon = $totalFat = 0;
        foreach ($foodList as $foodKey => $num) {
            $food = $this->foods[$foodKey];
            $protein = $food[0];
            $carbon = $food[1];
            $fat = $food[2];

            if ($protein > 0) {
                $totalProtein += $protein * $num;
            }

            if ($fat > 0) {
                $totalFat += $fat * $num;
            }

            if ($protein > 0) {
                $totalCarbon += $carbon * $num;
            }
        }

        return [$totalProtein, $totalCarbon, $totalFat];
    }

    private function printLine(?string $line = null): void
    {
        $lineBreak = '<br/>';

        if ($line === null) {
            echo $lineBreak;
            return;
        }

        echo $line . '<br/>';
    }

    public function proteinToCalo(): float
    {
        return $this->proteinNeeded * $this->protein;
    }

    public function carbonToCalo(): float
    {
        return $this->carbonNeeded * $this->carbon;
    }

    public function fatToCalo(): float
    {
        return $this->fatNeeded * $this->fat;
    }
}


$TDEE = 1952;
$weight = 52;

//$fit = new FoxFit($weight, $TDEE, Freedom::SPECIAL_CALCULATOR);
//$fit->showResult();
//
//echo '---------------------';

$fit = new FoxFit($weight, $TDEE);
$fit->setList([30, 50, 20]);
$fit->setFoodList([
    'chicken' => 2,
    'rice' => 4,
    'hamburger' => 1,
    'banana' => 2,
    'whey' => 2,
    'yaourt' => 2,
    'cheese' => 2,
    'milk_no_sugar' => 2,
    'watermelon' => 1,
]);
$fit->showResult();