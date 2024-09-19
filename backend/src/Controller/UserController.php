<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

#[Route('/api/users', name: 'api_users_')]
final class UserController extends AbstractController
{
    private $jwtManager;
    private $passwordHasher;
    private $entityManager;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ) {
        $this->jwtManager = $jwtManager;
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setEmail($data['email']);
        $user->setName($data['name']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
        $user->setFechaRegistro(new \DateTime());
        $user->setSubscripcionActiva($data['subscripcionActiva'] ?? false);
        $user->setTipoSubscripcion($data['tipoSubscripcion'] ?? null);
        $user->setDiasRestantes($data['diasRestantes'] ?? 0);
        $user->setRol($data['rol'] ?? 'ROLE_USER');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Generate JWT token for the user
        $token = $this->jwtManager->create($user);

        return $this->json([
            'token' => $token
        ], HttpResponse::HTTP_CREATED);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request, UserRepository $userRepository): JsonResponse
    {
        // The actual authentication is handled by the JWT token generation
        $data = json_decode($request->getContent(), true);

        $user = $userRepository->findOneBy(['email' => $data['email']]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            return $this->json(['error' => 'Invalid credentials'], HttpResponse::HTTP_UNAUTHORIZED);
        }

        $token = $this->jwtManager->create($user);

        return $this->json([
            'token' => $token
        ]);
    }

    #[Route(name: 'index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        $data = array_map(function (User $user) {
            return [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'subscripcionActiva' => $user->getSubscripcionActiva(),
                'tipoSubscripcion' => $user->getTipoSubscripcion(),
                'diasRestantes' => $user->getDiasRestantes(),
                'rol' => $user->getRol(),
                'fechaRegistro' => $user->getFechaRegistro()->format('Y-m-d H:i:s'),
            ];
        }, $users);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(User $user): JsonResponse
    {
        $data = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'subscripcionActiva' => $user->getSubscripcionActiva(),
            'tipoSubscripcion' => $user->getTipoSubscripcion(),
            'diasRestantes' => $user->getDiasRestantes(),
            'rol' => $user->getRol(),
            'fechaRegistro' => $user->getFechaRegistro()->format('Y-m-d H:i:s'),
        ];

        return $this->json($data);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Request $request, User $user): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user->setEmail($data['email'] ?? $user->getEmail());
        $user->setName($data['name'] ?? $user->getName());
        if (isset($data['password'])) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
        }
        $user->setSubscripcionActiva($data['subscripcionActiva'] ?? $user->getSubscripcionActiva());
        $user->setTipoSubscripcion($data['tipoSubscripcion'] ?? $user->getTipoSubscripcion());
        $user->setDiasRestantes($data['diasRestantes'] ?? $user->getDiasRestantes());
        $user->setRol($data['rol'] ?? $user->getRol());

        $this->entityManager->flush();

        return $this->json(['status' => 'User updated'], HttpResponse::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(User $user): JsonResponse
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json(['status' => 'User deleted'], HttpResponse::HTTP_NO_CONTENT);
    }
}
