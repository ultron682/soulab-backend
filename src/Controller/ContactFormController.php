<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactFormController extends AbstractController
{
    #[Route('/api/contact', methods: ['POST'])]
    public function submitContactForm(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response {
        $contact = $serializer->deserialize($request->getContent(), Contact::class, 'json');

        $errors = $validator->validate($contact);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new Response(json_encode($errorMessages), Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json']);
        }

        $entityManager->persist($contact);
        $entityManager->flush();

        return new Response($serializer->serialize($contact, 'json'), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
