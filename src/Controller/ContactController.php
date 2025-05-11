<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $contact = new Message();
        $form = $this->createForm(MessageType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ajout de la date
            $contact->setSentAt(new \DateTimeImmutable());

            // Enregistrement en base
            $em->persist($contact);
            $em->flush();

            // Envoi d'email
            $email = (new Email())
                ->from($contact->getEmail())
                ->to('ton.email@domaine.com') // ← Remplace ici par ton adresse mail
                ->subject($contact->getSubject())
                ->text(
                    "Nom : " . $contact->getLastName() . "\n" .
                    "Prénom : " . $contact->getFirstName() . "\n" .
                    "Email : " . $contact->getEmail() . "\n\n" .
                    "Message :\n" . $contact->getMessage()
                );

            $mailer->send($email);

            $this->addFlash('success', 'Votre message a bien été envoyé !');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
