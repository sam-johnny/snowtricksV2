<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 12/10/2022
 * Time: 10:48
 */

namespace App\Tests\Functional;

use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


final class CategoryCreateTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    /**
     * @test
     */
    public function shouldCreateCategory(): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->client->getContainer()->get(UserRepository::class);

        /** @var User $user */
        $user = $userRepository->find(1);

        $this->client->loginUser($user);

        $this->client->request(Request::METHOD_GET, '/admin/category/new');

        $this->client->submitForm('Créer', self::createFormData());

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        self::assertRouteSame('app.home');

        self::assertSelectorExists('div.alert-success', 'Bien créé avec succès');

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->client->getContainer()->get(CategoryRepository::class);

        /** @var Category $category */
        $category = $categoryRepository->find($this->getLastIdCategory() + 10);

        self::assertNotNull($category);

        self::assertEquals('test', $category->getName());
    }

    /**
     * @test
     */
    public function shouldRedirectToLogin(): void
    {
        $this->client->request(Request::METHOD_GET, '/admin/category/new');

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

        $this->client->request(Request::METHOD_GET, '/admin/category/new');

        $this->client->submitForm('Créer', $formData);

        self::assertRouteSame('category_new');

        self::assertSelectorExists('div.invalid-feedback');
    }

    private static function createFormData(array $overrideData = []): array
    {
        return $overrideData + [
                'category[name]' => 'test',
            ];
    }

    public function provideBadData(): Generator
    {
        yield 'empty name' => [self::createFormData(['category[name]' => ''])];
    }

    private function getLastIdCategory(): int
    {
        $categoryRepository = $this->client->getContainer()->get(CategoryRepository::class);

        return count($categoryRepository->findAll());
    }
}