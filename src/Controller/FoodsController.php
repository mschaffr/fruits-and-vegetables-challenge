<?php

namespace App\Controller;

use App\Dto\Request\CreateFood;
use App\Dto\Request\QueryFood;
use App\Service\FoodService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/foods')]
class FoodsController extends AbstractController
{
    public function __construct(
        private readonly FoodService $foodService
    )
    {
    }

    #[Route('', name: 'get_foods', methods: ['GET'])]
    public function getFoods(
        #[MapQueryString] QueryFood $query
    ): JsonResponse
    {
        $items = $this->foodService->getByQuery($query);
        return $this->json([
            'data' => $this->foodService->getByQuery($query),
            // dummy pagination placeholder
            'meta' => [
                'total' => $total = count($items->list()),
                'limit' => $total,
                'offset' => 0,
            ]
        ]);
    }

    #[Route('', name: 'create_food', methods: ['POST'])]
    public function createFood(
        #[MapRequestPayload(validationFailedStatusCode: JsonResponse::HTTP_BAD_REQUEST)] CreateFood $input
    ): JsonResponse
    {
        $food = $this->foodService->createRecord($input);

        // If you follow the REST principles, the content body shouldn't be returned on creation, instead a Location header should be set
        // with the URL of the newly created resource. However, for simplicity, we return the created food item here.
        // In a real-world application, you might want to return a 201 Created status code with a Location header pointing to the new resource.
        // return $this->json([], 201, ['Location' => '/foods/' . $food->id]);
        // For now, we return the created food item directly, as the resource wouldn't have a defined endpoint yet.
        // This is not a best practice, but it simplifies the example.
        return $this->json($food, 201);
    }
}
