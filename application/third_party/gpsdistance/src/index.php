<?php
/*
 *
 * Created by Waldemar Graban 2020
 *
 */

namespace Waldekgraban\Distance;

require_once "../vendor/autoload.php";

use Waldekgraban\Distance\Location\Point;
use Waldekgraban\Distance\Counter\Calculator;

$startingPlace = new Point(54.377464, 18.608775);
$destination   = new Point(54.357664, 18.586158);

$calculator = new Calculator($startingPlace, $destination, $kilometers = true);
echo $calculator->getDistance();
