<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Providers\Interfaces\StreamsProviderInterface;
use App\Providers\StreamsProvider;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

class StreamsProviderTest extends TestCase
{
    /**
     * @var MockObject $response
     */
    private $response;

    /** @var StreamsProviderInterface $streamsProvider */
    private $streamsProvider;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->response = $this->getMockBuilder(ResponseInterface::class)->getMock();

        $httpClient = $this->getMockBuilder(ClientInterface::class)->getMock();
        $httpClient->expects($this->any())->method('send')->willReturn($this->response);

        $this->streamsProvider = new StreamsProvider($httpClient);
    }

    /**
     * @test
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function mustReturnTopMostPopularStreamers(): void
    {
        $response = $this->streamsProvider->getMostPopularStreams();
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @test
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function mustReturnStreamByUserName(): void
    {
        $response = $this->streamsProvider->getStreamByUserLogin('Tfue');
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
