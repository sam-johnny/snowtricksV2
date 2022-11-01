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


final class PostUpdateTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::CreateClient();
    }

    /**
     * @test
     */
    public function shouldUpdatePost(): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->client->getContainer()->get(UserRepository::class);

        /** @var User $user */
        $user = $userRepository->find(1);

        $this->client->loginUser($user);

        $this->client->request(Request::METHOD_GET, '/admin/tricks/1/edit');

        $this->client->submitForm('Modifier', self::createFormData());

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        self::assertRouteSame('tricks.show', ['id' => 1, 'slug' => 'title-test']);

        self::assertSelectorExists('div.alert-success', 'L\'article a bien été modifié avec succès');

        /** @var PostRepository $postRepository */
        $postRepository = $this->client->getContainer()->get(PostRepository::class);

        /** @var Post $post */
        $post = $postRepository->find(1);

        self::assertNotNull($post);
        self::assertEquals('Title test', $post->getTitle());
        self::assertEquals('Content', $post->getContent());
        self::assertEquals(1, $post->getCategory()->getId());
    }

    /**
     * @test
     */
    public function shouldRedirectToLogin(): void
    {
        $this->client->request(Request::METHOD_GET, '/admin/tricks/1/edit');

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

        $this->client->request(Request::METHOD_GET, '/admin/tricks/20/edit');

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
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

        $this->client->request(Request::METHOD_GET, '/admin/tricks/1/edit');

        $this->client->submitForm('Modifier', $formData);

        self::assertRouteSame('app_admin_post_edit');

        self::assertSelectorExists('div.invalid-feedback');
    }

    private static function createFormData(array $overrideData = []): array
    {
         /*$originalFile = __DIR__ . '/../../public/images/image.png';

        $filename = sprintf('%s.png', Uuid::v4());

        $finalFile = sprintf('%s/../../public/images/%s', __DIR__, $filename);

        copy($originalFile, $finalFile);*/

        return $overrideData + [
                'post[title]' => 'Title test',
                'post[content]' => 'Content',
                'post[category]' => 1,
                /*'post[imageFiles]' => new UploadedFile($finalFile, $filename, 'image/png', null, true)*/
            ];
    }

    public function provideBadData(): Generator
    {
        yield 'empty title' => [self::createFormData(['post[title]' => ''])];
        yield 'empty content' => [self::createFormData(['post[content]' => ''])];
    }
}