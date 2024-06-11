<?php

namespace App\Controller;
date_default_timezone_set('Europe/Moscow');

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use DateTime;

class TravelCostController
{

    #[Route("/api/calculate", methods: "POST")]
    public function calculate(Request $request): Response
    {
        // Получение данных из запроса
        $data = json_decode($request->getContent(), true);

        // Расчет стоимости путешествия
        $cost = $data['cost'] ?? 27000;

        // Вычесление детской скидки
        $cost = $this->applyChildDiscount($data, $cost);

        // Вычесление скидки за раннее бронирование
        $cost = $this->applyEarlyBookingDiscount($data, $cost);

        // Ответ сервера
        return new Response(json_encode(['cost' => $cost]), 200, ['Content-Type' => 'application/json']);
    }

    private function calculateTravelCost($data)
    {
        return $data['distance'] * $data['rate_per_km'];
    }

    private function applyChildDiscount($data, $cost)
    {
        $age = $data['age'];
        if ($age >= 3 && $age < 6) {
            $cost *= 0.2;
        } elseif ($age >= 6 && $age < 12) {
            if($cost*0.7> 4500){
                $cost = $cost - 4500;
            }else {
                $cost *= 0.7;
            }
            //return min($cost*$discount, 4500); // не более 4500 Р
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

    #[Route('/lucky/number')]
    public function number(): Response
    {
        $currentYear = (new DateTime())->format('Y');
        $date = new DateTimeImmutable("last day of November $currentYear");
        $res = "noo(";
        $dateBook = new DateTimeImmutable("04.11.2024");
        //if()
        return new Response(
            '<html><body>Lucky year: '.$date->format('d/m/Y').'</body></html>'
        );
    }
}