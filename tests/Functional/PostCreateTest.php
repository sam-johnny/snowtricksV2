<?php

/**
 * Created by PhpStorm.
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


final class PostCreateTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    /**
     * @test
     */
    public function shouldCreatePost(): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->client->getContainer()->get(UserRepository::class);

        /** @var User $user */
        $user = $userRepository->find(1);

        $this->client->loginUser($user);

        $this->client->request(Request::METHOD_GET, '/admin/tricks/new');

        $this->client->submitForm('Créer', self::createFormData());

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        self::assertRouteSame('tricks.show', ['id' => $this->getLastIdPost() + 16, 'slug' => 'title-test']);

        self::assertSelectorExists('div.alert-success', 'Bien créé avec succès');

        /** @var PostRepository $postRepository */
        $postRepository = $this->client->getContainer()->get(PostRepository::class);

        /** @var Post $post */
        $post = $postRepository->find($this->getLastIdPost() + 16);

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
        $this->client->request(Request::METHOD_GET, '/admin/tricks/new');

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        self::assertRouteSame('login');

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

        $this->client->request(Request::METHOD_GET, '/admin/tricks/new');

        $this->client->submitForm('Créer', $formData);

        self::assertRouteSame('app_admin_post_new');

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

    private function getLastIdPost(): int
    {
        /** @var PostRepository $postRepository */
        $postRepository = $this->client->getContainer()->get(PostRepository::class);

        return count($postRepository->findAll());
    }
}