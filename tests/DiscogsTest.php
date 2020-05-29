<?php

namespace Kayintveen\Discogs\Test;

use GuzzleHttp\Client as GuzzleClient;
use Kayintveen\Discogs\DiscogsClient;
use Mockery;
use PHPUnit\Framework\TestCase;

class DiscogsTest extends TestCase
{
    const USER_NAME = 'microdesign';
    /**
     * @var \GuzzleHttp\Client|\Mockery\LegacyMockInterface|\Mockery\MockInterface
     */
    private $client;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = Mockery::mock(GuzzleClient::class);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testArtistRetrieve()
    {
        $name = "The Persuader";
        /** @var mixed $return */
        $return = json_encode(['name' => $name]);
        $this->client
            ->shouldReceive('get->getBody->getContents')
            ->once()
            ->andReturn($return);

        $discogsClient = new DiscogsClient($this->client);
        $output        = $discogsClient->getArtist('1')->name;
        $this->assertEquals($name, $output);
    }

    public function testGetFolders()
    {
        $discogsClient = new DiscogsClient($this->client, "123");

        /** @var mixed $return */
        $return = json_encode([
            "folders" =>
                [
                    "count" => 1,
                    "resource_url" => "https://api.discogs.com/users/microdesign/collection/folders/0",
                    "id" => 0,
                    "name" => "All"
                ]
        ]);

        $this->client
            ->shouldReceive('get->getBody->getContents')
            ->once()
            ->andReturn($return);

        $output        = $discogsClient->getFolders('microdesign')->folders->name;
        $this->assertEquals("All", $output);
    }

    public function testGetRecordsFromFolder()
    {
        $discogsClient = new DiscogsClient($this->client, "123");

        /** @var mixed $return */
        $return = json_encode([
            "count" => 1,
            "page" => 1,
            "per_page" => 50,
            "releases" => []
        ]);

        $this->client
            ->shouldReceive('get->getBody->getContents')
            ->once()
            ->andReturn($return);

        $output        = $discogsClient->getRecordsFromFolder('microdesign', '0', 50, 1);

        $this->assertObjectHasAttribute("releases", $output);
    }


}
