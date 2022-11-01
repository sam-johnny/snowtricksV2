<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 13/10/2022
 * Time: 17:51
 */

namespace App\Tests\Functional;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CommentPostTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    /**
     * @test
     */
    public function shouldPostComment()
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->client->getContainer()->get(UserRepository::class);

        /** @var User $user */
        $user = $userRepository->find(1);

        $this->client->loginUser($user);

        $this->client->request(Request::METHOD_GET, '/tricks/details/' . $this->getSlug() . '/9');

        self::assertResponseIsSuccessful();

        $this->client->submitForm('Commenter', self::createFormData());

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        self::assertRouteSame('tricks.show', ['slug' => $this->getSlug(), 'id' => 9]);

        self::assertSelectorExists('div.alert-success', 'Votre commentaire est bien envoyé');

        /** @var CommentRepository $commentRepository */
        $commentRepository = $this->client->getContainer()->get(CommentRepository::class);

        /** @var Comment $comment */
        $comment = $commentRepository->find($this->getLastIdComment() + 6);

        self::assertNotNull($comment);
        self::assertEquals('commentaire test', $comment->getContent());

    }

    /**
     * @test
     *
     * @dataProvider provideBadData
     */
    public function shouldShowErrors(array $formData): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->client->getContainer()->get(UserRepository::class);

        /** @var User $user */
        $user = $userRepository->find(1);

        $this->client->loginUser($user);

        $this->client->request(Request::METHOD_GET, '/tricks/details/' . $this->getSlug() . '/9');

        $this->client->submitForm('Commenter', $formData);

        self::assertRouteSame('tricks.show', ['slug' => $this->getSlug(), 'id' => 9]);

        self::assertSelectorExists('div.invalid-feedback');
    }

    private static function createFormData(array $override = []): array
    {
        return $override + [
                'comment[content]' => 'commentaire test'
            ];
    }

    public function provideBadData(): Generator
    {
        yield 'empty content' => [self::createFormData(['comment[content]' => ''])];
    }

    private function getSlug(): string
    {
        /** @var PostRepository $postRepository */
        $postRepository = $this->client->getContainer()->get(PostRepository::class);

        return $postRepository->find(9)->getSlug();
    }

    private function getLastIdComment(): int
    {
        /** @var CommentRepository $commentRepository */
        $commentRepository = $this->client->getContainer()->get(CommentRepository::class);

        return count($commentRepository->findAll());
    }

}