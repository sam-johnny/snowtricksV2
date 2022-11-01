<?php

/**
 * Created by PhpStorm.
 * User: SAM Johnny
 * Date: 10/10/2022
 * Time: 14:25
 */

namespace App\Tests\Functional;

use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profile;

final class LoginTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->client->request(Request::METHOD_GET, '/login');
    }

    /**
     * @test
     */
    public function shouldAuthenticate(): void
    {

        $this->client->submitForm('Se connecter', self::createFormData());

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->enableProfiler();

        if (($profile = $this->client->getProfile()) instanceof Profile) {
           /** @var SecurityDataCollector $securityCollector*/
            $securityCollector = $profile->getCollector('security');

            self::assertTrue($securityCollector->isAuthenticated());
        }

        $this->client->enableProfiler();

        $this->client->followRedirect();

        self::assertRouteSame('app.home');
    }

    /**
     * @test
     *
     * @dataProvider provideBadData
     */
    public function shouldShowErrors(array $formData): void
    {

        $this->client->submitForm('Se connecter', $formData);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->enableProfiler();

        if (($profile = $this->client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector*/
            $securityCollector = $profile->getCollector('security');

            self::assertFalse($securityCollector->isAuthenticated());
        }

        $this->client->followRedirect();

        self::assertRouteSame('login');
    }

    /**
     * @test
     *
     * @dataProvider provideNotVerifiedData
     */
    public function shouldShowErrorNotVerified(array $formData): void
    {
        $this->client->submitForm('Se connecter', $formData);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->enableProfiler();

        if (($profile = $this->client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector*/
            $securityCollector = $profile->getCollector('security');

            self::assertFalse($securityCollector->isAuthenticated());
        }

        $this->client->followRedirect();

        self::assertSelectorExists('.alert.alert-danger');
        self::assertRouteSame('login');
    }

    private static function createFormData(array $overrideData = []): array
    {
        return $overrideData + [
            'username' => 'user1',
                'password' => 'password'
            ];
    }

    public function provideBadData(): Generator
    {
        yield 'bad username' => [self::createFormData(['username' => 'fail'])];
        yield 'bad password' => [self::createFormData(['password' => 'fail'])];
    }

    public function provideNotVerifiedData(): Generator
    {
        yield 'user not verified' => [self::createFormData([
            'username' => 'user6',
            'password' => 'password'
        ])];
    }
}