<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

#[Route('/api/products', name: 'api_products_')]
final class ProductController extends AbstractController
{
    #[Route(name: 'index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();
        $data = array_map(function (Product $product) {
            return [
                'id' => $product->getId(),
                'img' => $product->getImg(),
                'name' => $product->getName(),
                'stars' => $product->getStars(),
                'reviews' => $product->getReviews(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription(),
                'link' => $product->getLink(),
            ];
        }, $products);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Product $product): JsonResponse
    {
        $data = [
            'id' => $product->getId(),
            'img' => $product->getImg(),
            'name' => $product->getName(),
            'stars' => $product->getStars(),
            'reviews' => $product->getReviews(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription(),
            'link' => $product->getLink(),
        ];

        return $this->json($data);
    }

    #[Route(name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $product = new Product();
        $product->setImg($data['img']);
        $product->setName($data['name']);
        $product->setStars($data['stars']);
        $product->setReviews($data['reviews']);
        $product->setPrice($data['price']);
        $product->setDescription($data['description']);
        $product->setLink($data['link']);

        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json(['id' => $product->getId()], HttpResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, Product $product, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product->setImg($data['img'] ?? $product->getImg());
        $product->setName($data['name'] ?? $product->getName());
        $product->setStars($data['stars'] ?? $product->getStars());
        $product->setReviews($data['reviews'] ?? $product->getReviews());
        $product->setPrice($data['price'] ?? $product->getPrice());
        $product->setDescription($data['description'] ?? $product->getDescription());
        $product->setLink($data['link'] ?? $product->getLink());

        $entityManager->flush();

        return $this->json(['status' => 'Product updated'], HttpResponse::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Product $product, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->json(['status' => 'Product deleted'], HttpResponse::HTTP_NO_CONTENT);
    }
}
