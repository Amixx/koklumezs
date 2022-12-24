<?php

namespace app\fitness\models;

class DifficultyEvaluation
{
    private $repsOrTime;
    private $forReps;
    private $isEmpty;

    private $evaluationValueToMinExtraPercent = [
        2 => 0.50,
        4 => 0.20,
        6 => 0.05,
        8 => 0,
        10 => -10,
    ];
    private $evaluationValueToGenericText = [
        2 => 'Garlaicgi',
        4 => 'Viegli',
        6 => 'Nedaudz grūti',
        8 => 'Ļoti grūti',
        10 => 'Nevarēju pabeigt piegājienu',
    ];

    function __construct($repsOrTime, $forReps, $isEmpty = false)
    {
        $this->repsOrTime = $repsOrTime;
        $this->forReps = $forReps;
        $this->isEmpty = $isEmpty;
    }

    public static function createForReps($reps)
    {
        return new self($reps, true);
    }

    public static function createForTime($timeSeconds)
    {
        return new self($timeSeconds, false);
    }

    public static function createEmpty()
    {
        return new self(null, false, true);
    }

    private function formatEvaluationText($min, $max)
    {
        $minMaxEqual = $min === $max;
        $repsFormatted = is_null($max)
            ? "$min+"
            : ($minMaxEqual ? "$min" : "$min-$max");
        $suffix = $minMaxEqual && $min == 1
            ? ($this->forReps ? "reizi" : "sekundi")
            : ($this->forReps ? "reizes" : "sekundes");

        return join(" ", ["Varēju vēl", $repsFormatted, $suffix]);
    }

    private function createMinMaxRepsOrTimeSeconds($evaluationValue)
    {
        $minRepsOrTime = round($this->repsOrTime * $this->evaluationValueToMinExtraPercent[$evaluationValue]);
        if ($evaluationValue !== 8) $minRepsOrTime += 1;
        $maxRepsOrTime = $evaluationValue === 2
            ? null
            : round($this->repsOrTime * $this->evaluationValueToMinExtraPercent[$evaluationValue - 2]);
        return [
            'min' => $minRepsOrTime,
            'max' => $maxRepsOrTime,
        ];
    }

    public function createMinMaxRepsOrTimeSecondsForNotFinished(){
        return ['min' => $this->repsOrTime, 'max' => $this->repsOrTime];
    }

    public function createMinMaxTotalRepsOrTimeSeconds($evaluationValue){
        if ($evaluationValue === 10) return $this->createMinMaxRepsOrTimeSecondsForNotFinished();
        $minMaxExtraRepsOrTimeSeconds = $this->createMinMaxRepsOrTimeSeconds($evaluationValue);
        return [
            'min' => $this->repsOrTime + $minMaxExtraRepsOrTimeSeconds['min'],
            'max' => $this->repsOrTime + $minMaxExtraRepsOrTimeSeconds['max'],
        ];
    }

    public function createEvaluationText($evaluationValue)
    {
        if ($evaluationValue === 10) return "Nevarēju pabeigt piegājienu";
        if ($this->isEmpty) return $this->evaluationValueToGenericText[$evaluationValue];
        else {
            $minMax = $this->createMinMaxRepsOrTimeSeconds($evaluationValue);
            return $this->formatEvaluationText($minMax['min'], $minMax['max']);
        }
    }

    public function createEvaluations()
    {
        return array_map(function ($evaluationValue) {
            return [
                'value' => $evaluationValue,
                'text' => self::createEvaluationText($evaluationValue),
                'is_could_not_finish' => $evaluationValue === 10,
            ];
        }, [2, 4, 6, 8, 10]);
    }
}