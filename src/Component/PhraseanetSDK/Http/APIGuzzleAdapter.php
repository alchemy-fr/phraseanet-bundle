<?php

namespace Alchemy\Phraseanet\PhraseanetSDK\Http;

use Alchemy\Phraseanet\PhraseanetSDK\Exception\RuntimeException;

class APIGuzzleAdapter implements GuzzleAdapterInterface
{
    /** @var GuzzleAdapterInterface */
    private $adapter;

    public function __construct(GuzzleAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function getGuzzle()
    {
        return $this->adapter->getGuzzle();
    }

    public function call(
        $method,
        $path,
        array $query = array(),
        array $postFields = array(),
        array $files = array(),
        array $headers = array()
    ) {
        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n", __FILE__, __LINE__, __FUNCTION__), FILE_APPEND);
        $json = @json_decode($this->adapter->call($method, $path, $query, $postFields, $files, $headers));

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new RuntimeException(
                'Json response cannot be decoded or the encoded data is deeper than the recursion limit'
            );
        }
//        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, var_export($json, true)), FILE_APPEND);

        return new APIResponse($json);
    }
}
