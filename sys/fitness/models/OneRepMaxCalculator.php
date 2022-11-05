<?php

namespace app\fitness\models;

class OneRepMaxCalculator
{
//    private $weight;
//    private $reps;
//
//    public function __construct($weight, $reps)
//    {
//        $this->$weight = $weight;
//        $this->$reps = $reps;
//    }

    // formula source:
    // https://www.calculators.tech/1rm-calculator#1rm-formula
    public static function oneRepMax($weight, $reps)
    {
        return $weight / (1.0278 - (0.0278 * $reps));
    }

    public static function oneRepMaxRounded($weight, $reps, $precision = 1)
    {
        return round(self::oneRepMax($weight, $reps), $precision);
    }

    public static function oneRepMaxRange($weight, $minReps, $maxReps)
    {
        $minOneRepMax = !is_null($minReps) ? self::oneRepMaxRounded($weight, $minReps) : null;
        $maxOneRepMax = !is_null($maxReps) ? self::oneRepMaxRounded($weight, $maxReps) : null;

        return [
            'min' => $minOneRepMax,
            'max' => $maxOneRepMax,
        ];
    }
}