<?php
namespace Marvin\Security\Presentation\Web\Controller;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'marvin.home')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('security/home.html.twig',);
    }
}
