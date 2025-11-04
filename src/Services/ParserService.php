<?php

namespace App\Services;

use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ParserService {
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private CacheInterface $cache;

    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger, CacheInterface $cache) {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->cache = $cache;
    }

    private function getCacheKey(string $url): string {
        return 'scraper_cache_' . $url;
    }

    public function parseUri($url): array {
        try {
            string: $cacheKey = $this->getCacheKey($url);

            $cachedContent = $this->cache->get($cacheKey, function (ItemInterface $item) use ($url) {
                $this->logger->info("Fetching data from $url");

                $item->expiresAfter(60 * 60 * 24);

                $response = $this->httpClient->request('GET', $url, [
                    'timeout' => 10,
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (compatible; MyBot/1.0)',
                    ],
                ]);

                return $response->getContent();
            });

            return $this->parseHtml($cachedContent, $url);

        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            $this->logger->error("Error fetching data: " . $e->getMessage());

            throw new ("Error fetching data from $url: " . $e->getMessage());
        }
    }

    public function parseHtml(string $html, string $url): array {
        Crawler: $crawler = new Crawler($html, $url);

        return $crawler->filter('#topVanzari-109 a')->each(function (Crawler $link) {
            $imageHtml = '';

            if ($link->filter('img')->count() > 0) {
                $imageHtml = $link->filter('img')->outerHtml();
            }

            return [
                'url' => $link->link()->getUri(),
                'imageHtml' => $imageHtml,
            ];
        });
    }
}
