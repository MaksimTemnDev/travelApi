<?php

namespace App\Service;
date_default_timezone_set('Europe/Moscow');

use DateTimeImmutable;
use DateTime;

class TravelCostCalculator
{

    public function calculate(array $data): int {
        $cost = $data['cost'] ?? 27000;
        $cost = $this->applyChildDiscount($data, $cost);
        $cost = $this->applyEarlyBookingDiscount($data, $cost);

        return $cost;
    }

    private function applyChildDiscount(array $data, int $cost): int {
        $age = $data['age'];
        if ($age >= 3 && $age < 6) {
            $cost *= 0.2;
        } elseif ($age >= 6 && $age < 12) {
            if ($cost * 0.7 > 4500) {
                $cost = $cost - 4500;
            } else {
                $cost *= 0.7;
            }
        } elseif ($age >= 12) {
            $cost *= 0.9;
        }

        return $cost;
    }

    private function applyEarlyBookingDiscount($data, $cost)
    {
        $bookingDate = new DateTimeImmutable($data['booking_date']);
        $startDate = new DateTimeImmutable($data['start_date']);
        $discount = 0;

        $currentYear = (new DateTime())->format('Y');

        if ($startDate >= new DateTimeImmutable("April 1 $currentYear") && $startDate <= new DateTimeImmutable("30th September next year")) {
            if ($bookingDate <= new DateTimeImmutable("last day of November $currentYear")) {
                $discount = 0.07;
            } elseif ($bookingDate <= new DateTimeImmutable("last day of December $currentYear")) {
                $discount = 0.05;
            } elseif ($bookingDate <= new DateTimeImmutable("last day of January " . ($currentYear + 1))) {
                $discount = 0.03;
            }
        } else if ($startDate >= new DateTimeImmutable("October 1 $currentYear") && $startDate <= new DateTimeImmutable("14th January " . ($currentYear + 1))) {
            if ($bookingDate <= new DateTimeImmutable("last day of March $currentYear")) {
                $discount = 0.07;
            } elseif ($bookingDate <= new DateTimeImmutable("last day of April $currentYear")) {
                $discount = 0.05;
            } elseif ($bookingDate <= new DateTimeImmutable("last day of May $currentYear")) {
                $discount = 0.03;
            }
        } else if ($startDate >= new DateTimeImmutable("January 15 " . ($currentYear + 1))) {
            if ($bookingDate <= new DateTimeImmutable("last day of August $currentYear")) {
                $discount = 0.07;
            } elseif ($bookingDate <= new DateTimeImmutable("last day of September $currentYear")) {
                $discount = 0.05;
            } elseif ($bookingDate <= new DateTimeImmutable("last day of October $currentYear")) {
                $discount = 0.03;
            }
        }

        // Расчет скидки
        $discountAmount = $cost * $discount;
        $discountAmount = min($discountAmount, 1500); // не более 1500 руб

        return $cost - $discountAmount;
    }
}