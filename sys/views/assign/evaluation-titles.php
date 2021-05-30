<?php

$empty = '<code>Not set</code>';

$getEvaluations = function ($etid) use ($empty, $evaluations, $evaluationsValues, $id) {
    if (isset($evaluations[$id][$etid])) {
        if (isset($evaluationsValues[$etid]) && isset($evaluationsValues[$etid][(int) $evaluations[$id][$etid]])) {
            return $evaluationsValues[$etid][(int) $evaluations[$id][$etid]];
        } else if (isset($evaluations[$id][$etid])) {
            return $evaluations[$id][$etid];
        }
    }

    return $empty;
};
