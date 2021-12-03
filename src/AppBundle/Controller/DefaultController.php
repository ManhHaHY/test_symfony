<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route(
     *   methods={"GET"},
     *   path="/"
     * )
     * @return Response
     */
    public function indexAction(): Response
    {
        return new Response();
    }
}
