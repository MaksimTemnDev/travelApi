<?php

namespace App\Controller;
date_default_timezone_set('Europe/Moscow');

use App\Service\TravelCostCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class TravelCostController extends AbstractController
{

    #[Route("/api/calculate", methods: "POST")]
    public function calculate(Request $request, TravelCostCalculator $calculator): Response {
        $data = json_decode($request->getContent(), true);
        $cost = $calculator->calculate($data);

        return new Response(json_encode(['cost' => $cost]), 200, ['Content-Type' => 'application/json']);
    }
}