<?php
function getCurrentWeekInfo() {
    $date = new DateTime();
    return [
        'week' => $date->format("W"),
        'year' => $date->format("Y")
    ];
}
?>