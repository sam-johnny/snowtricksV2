<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 10/10/2022
 * Time: 16:43
 */

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class HomeTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    /**
     * @test
     */
    public function shouldListPaginatedPosts(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/');

        self::assertCount(12,$crawler->filter('article'));
    }

    /**
     * @test
     */
    public function shouldListPaginatedPostsLoadMore(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/loadmorePosts?page=1');

        self::assertCount(10,$crawler->filter('article'));
    }
}