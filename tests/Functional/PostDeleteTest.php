<?php

/**
 * Updated by PhpStorm.
 * User: SAM Johnny
 * Date: 12/10/2022
 * Time: 10:48
 */

namespace App\Tests\Functional;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;


final class PostDeleteTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::CreateClient();
    }

    /**
     * @test
     */
    public function shouldDeletePost(): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->client->getContainer()->get(UserRepository::class);

        /** @var User $user */
        $user = $userRepository->find(1);

        $this->client->loginUser($user);

        $this->client->request(Request::METHOD_GET, '/admin/tricks/1/edit');

        $this->client->submitForm('Supprimer');

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        self::assertRouteSame('app.home');

        self::assertSelectorExists('div.alert-success', 'L\'article a bien été supprimé avec succès');

        /** @var PostRepository $postRepository */
        $postRepository = $this->client->getContainer()->get(PostRepository::class);

        /** @var Post $post */
        $post = $postRepository->find(1);

        self::assertNull($post);
    }

    /**
     * @test
     */
    public function shouldRedirectToLogin(): void
    {
        $this->client->request(Request::METHOD_POST, '/admin/tricks/1');

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        self::assertRouteSame('login');
    }

    /**
     * @test
     */
    public function shouldRaise403(): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->client->getContainer()->get(UserRepository::class);

        /** @var User $user */
        $user = $userRepository->find(1);

        $this->client->loginUser($user);

        $this->client->request(Request::METHOD_POST, '/admin/tricks/20');

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}