<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\ParserService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController {
    #[Route(path: '/', name: 'app_main')]
    public function index(ParserService $parserService, LoggerInterface $logger): Response {
        $topSales = $parserService->parseUri("https://carturesti.ro/");
        $topSales = array_filter($topSales, function ($item) { return str_contains($item['url'], "/carte/"); });

        return $this->render('main/main.html.twig',
            array('topSales' => $topSales),
        );
    }
}
