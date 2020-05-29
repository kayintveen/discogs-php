<?php

namespace Kayintveen\Discogs;

use GuzzleHttp\Client;

class DiscogsClient
{
    const URL_LIVE = 'https://api.discogs.com';

    const USER_AGENT = 'DiscogsPhp/1.0';

    /**
     * @var string
     */
    private $token;

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * DiscogsClient constructor.
     *
     * @param \GuzzleHttp\Client $client
     * @param string             $token
     */
    public function __construct(Client $client, $token = '')
    {
        $this->token = $token;
        $this->client = $client;
    }

    /**
     * @param string $artist_id
     *
     * @return mixed
     */
    public function getArtist(string $artist_id)
    {
        $content = $this->client
            ->get(
                $this->getUrl('artists/%', $artist_id),
                $this->parameters()
            )->getBody()
            ->getContents();

        return json_decode($content);
    }

    /**
     * @param string $username
     *
     * @return array
     */
    public function getFolders(string $username)
    {
        $content = $this->client
            ->get(
                $this->getUrl("users/{$username}/collection/folders"),
                $this->parameters()
            )->getBody()
            ->getContents();

        return json_decode($content);
    }

    public function getRecordsFromFolder($username, $folder_id, $perPage = 50, $page = 1) {

        $content = $this->client
            ->get(
                $this->getUrl("users/{$username}/collection/folders/{$folder_id}/releases"),
                $this->parameters(['per_page' => $perPage, 'page' => $page])
            )->getBody()
            ->getContents();

        return json_decode($content);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function getUrl(string $path): string
    {
        return self::URL_LIVE . '/' . $path;
    }

    /**
     * @return array
     */
    private function parameters($query): array
    {
        $query["token"] = $this->token;

        return [
            'stream' => true,
            'headers' => ['User-Agent' => self::USER_AGENT],
            'query' => $query
        ];
    }
}
