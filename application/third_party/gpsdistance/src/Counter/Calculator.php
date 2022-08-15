<?php
/*
 *
 * Created by Waldemar Graban 2020
 *
 */

class Calculator
{
    protected $startingPlace;
    protected $destination;
    protected $kilometers;

    public function __construct($startingPlace, $destination, $kilometers)
    {
        $this->startingPlace = $startingPlace;
        $this->destination   = $destination;
        $this->kilometers   = $kilometers;
    }

    public function getDistance()
    {
        $distance = sin(deg2rad($this->startingPlace->lat)) * sin(deg2rad($this->destination->lat)) +
                    cos(deg2rad($this->startingPlace->lat)) * cos(deg2rad($this->destination->lat)) *
                    cos(deg2rad($this->startingPlace->lon - $this->destination->lon));

        $distance   = acos($distance);
        $distance   = rad2deg($distance);
        $miles      = $distance * 60 * 1.1515;
        $kilometers = $miles * 1.609344;

        $distanceUnit = ($this->kilometers)
            ? $kilometers
            : $miles;

        return $distanceUnit;
    }
}
