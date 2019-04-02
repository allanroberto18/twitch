<?php

namespace Tests\Unit;

use App\Providers\Interfaces\TwitchAuthProviderInterface;
use App\Providers\TwitchAuthAuthProvider;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

class TwitchAuthProviderTest extends TestCase
{
    /**
     * @var TwitchAuthProviderInterface $twitchAuthProvider
     */
    private $twitchAuthProvider;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();

        $httpClient = $this->getMockBuilder(ClientInterface::class)->getMock();
        $httpClient->expects($this->any())->method('send')->willReturn($response);

        $this->twitchAuthProvider = new TwitchAuthAuthProvider($httpClient);
    }

    /**
     * @test
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function mustReturnResponseFromAccessTokensFunctions(): void
    {
        $accessToken = $this->twitchAuthProvider->getAccessToken();
        $userAccessToken = $this->twitchAuthProvider->getUserAccessToken('code');

        $this->assertInstanceOf(ResponseInterface::class, $accessToken);
        $this->assertInstanceOf(ResponseInterface::class, $userAccessToken);
    }

    /**
     * @test
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function mustReturnExceptionWithEmptyCodeOnUserAccessToken(): void
    {
        $this->expectException(\Exception::class);

        $this->twitchAuthProvider->getUserAccessToken('');
    }

    /**
     * @test
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function mustReturnExceptionWithNullCodeOnUserAccessToken(): void
    {
        $this->expectException(\Exception::class);

        $this->twitchAuthProvider->getUserAccessToken('');
    }

    /**
     * @test
     * @return void
     */
    public function mustReturnCorrectAuthUrl(): void
    {
        $base = $this->twitchAuthProvider->getBaseUrl();
        $params = [
            'client_id' => $this->twitchAuthProvider->getClientId(),
            'redirect_uri' => $this->twitchAuthProvider->getRedirectUrl(),
            'response_type' => 'code',
            'scope' => $this->twitchAuthProvider->getScopes(),
            'force_verify' => true
        ];
        $query = http_build_query($params);
        $urlExpected = sprintf('%s/authorize?%s', $base, $query);

        $urlRequested = $this->twitchAuthProvider->getAuthUrl();

        $this->assertEquals($urlExpected, $urlRequested);
    }

    /**
     * @test
     * @return void
     */
    public function mustReturnRefreshToken(): void
    {
        $refreshToken = $this->twitchAuthProvider->getRefreshToken('token');
        $this->assertInstanceOf(ResponseInterface::class, $refreshToken);
    }
}
