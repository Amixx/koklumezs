<?php

namespace app\fitness\models;

class RpeCalculator
{
    private static $rpeArray = [
        100, 97.8, 95.5, 93.9, 92.2, 90.7, 89.2, 87.8,
        86.3, 85.0, 83.7, 82.4, 81.1, 79.9, 78.6, 77.4,
        76.2, 75.1, 73.9, 72.3, 70.7, 69.4, 68.0, 66.7,
        65.3, 64.0, 62.6, 61.3, 59.9, 58.6
    ];
    private static $rpes = [10, 9.5, 9, 8.5, 8, 7.5, 7, 6.5];

    private static function getRpeTable(){
        $rpeTable = [];

        for ($rep = 1; $rep <= 12; $rep++) {
            $rpeArrayOffset = ($rep - 1) * 2;

            foreach (self::$rpes as $i => $rpe) {
                if (!isset($rpeTable[$rep])) $rpeTable[$rep] = [];
                $rpeTable[$rep][$rpe] = self::$rpeArray[$i + $rpeArrayOffset];
            }
        }

        return $rpeTable;
    }

    public static function calculateRpe($reps, $weightPercentageOf1rm)
    {
        $rpes = self::getRpeTable()[$reps];
        $smallestDiff = null;
        $toReturn = null;
        foreach ($rpes as $key => $rpe) {
            $newDiff = abs($weightPercentageOf1rm - $rpe);
            if (!$smallestDiff || $newDiff < $smallestDiff) {
                $smallestDiff = $newDiff;
                $toReturn = $key;
            }
        }

        return $toReturn;
    }
}