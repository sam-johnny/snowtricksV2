<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 10/10/2022
 * Time: 21:25
 */

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class PostShowTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    /**
     * @test
     */
    public function shouldShowPost(): void
    {
        /**
         * each time the database is reset change $slug
         */
        $slug = 'fuga-et-veniam';

        $this->client->request(Request::METHOD_GET, '/tricks/details/' . $slug . '/9');

        self::assertResponseIsSuccessful();

    }

    /**
     * @test
     */
    public function shouldListPaginatedCommentsLoadMore(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/loadmoreComments/9?page=1');

        self::assertCount(10,$crawler->filter('p'));
    }
}