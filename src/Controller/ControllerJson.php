<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ControllerJson
{
    #[Route("/api/quote")]
    public function jsonQuote(): Response
    {
        $number = random_int(1, 3);
        $quote = "";
        $date = date('l jS \of F Y h:i:s A');


        switch ($number) {
            case 1:
                $quote = "Two things are infinite: the universe and human stupidity; and I am not sure about the universe.";
                break;
            case 2:
                $quote = "Imagination is more important than knowledge.";
                break;
            case 3:
                $quote = "I have no special talent. I am only passionately curious.";
                break;
        }

        $data = [
            'Quote of the day' => $quote,
            'Date and timestamp' => $date,
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
}
