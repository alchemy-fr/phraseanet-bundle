<?php

/*
 * This file is part of Phraseanet SDK.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phraseanet\PhraseanetSDK\Http;

use Guzzle\Common\Exception\GuzzleException;
use Guzzle\Http\Client as Guzzle;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Exception\BadResponseException as GuzzleBadResponse;
use Guzzle\Http\Exception\CurlException;
use Guzzle\Http\Message\EntityEnclosingRequestInterface;
use Guzzle\Http\Message\RequestInterface;
use Alchemy\Phraseanet\PhraseanetSDK\ApplicationInterface;
use Alchemy\Phraseanet\PhraseanetSDK\Exception\BadResponseException;
use Alchemy\Phraseanet\PhraseanetSDK\Exception\InvalidArgumentException;
use Alchemy\Phraseanet\PhraseanetSDK\Exception\RuntimeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Parade\Component\Locale\LocaleService;

class GuzzleAdapter implements GuzzleAdapterInterface
{
    /** @var ClientInterface */
    private $guzzle;
    private $locale;
    private $extended = false;
    private $sslVerification = false;

    public function __construct(ClientInterface $guzzle, string $locale)
    {
        $this->guzzle = $guzzle;
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     *
     * @return ClientInterface
     */
    public function getGuzzle()
    {
        return $this->guzzle;
    }

    /**
     * Returns the client base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->guzzle->getBaseUrl();
    }

    /**
     * Sets the user agent
     *
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->guzzle->setUserAgent($userAgent);
    }

    /**
     * Sets extended mode
     *
     * Extended mode fetch more data (status, meta, subdefs) in one request
     * for a record
     *
     * @param boolean $extended
     */
    public function setExtended($extended)
    {
        $this->extended = (boolean)$extended;
    }

    /**
     * @return boolean
     */
    public function isExtended()
    {
        return $this->extended;
    }

    /**
     * Sets setSslVerification mode
     *
     * @param boolean $sslVerification
     */
    public function setSslVerification($sslVerification)
    {
        $this->sslVerification = (boolean)$sslVerification;
    }

    /**
     * Performs an HTTP request, returns the body response
     *
     * @param string $method The method
     * @param string $path The path to query
     * @param array $query An array of query parameters
     * @param array $postFields An array of post fields
     * @param array $files An array of post files
     * @param array $headers An array of request headers
     * @param string|null $body
     *
     * @return string The response body
     *
     * @throws BadResponseException
     * @throws RuntimeException
     */
    public function call(
        $method,
        $path,
        array $query = array(),
        array $postFields = array(),
        array $files = array(),
        array $headers = array(),
        $body = null
    ) {

//        file_put_contents("/var/parade/log.txt",
//            sprintf("method=%s\npath=%s\nquery=%s\npostFields=%s\nheaders=%s\n",
//                $method,
//                $path,
//                var_export($query, true),
//                var_export($postFields, true),
//                var_export($headers, true)
//        ), FILE_APPEND);
        try {
            $acceptHeader = array(
                'Accept' => 'application/json',
                'Accept-Language' => $_COOKIE['parade-standard-ml-lng'] ?? 'en',
            );

            if(!$this->sslVerification) {
                $this->guzzle->setSslVerification(false,false,0);
            }
            $request = $this->guzzle->createRequest($method, $path, array_merge($acceptHeader, $headers), $body);

            $this->addRequestParameters($request, $query, $postFields, $files);

            $response = $request->send();

        } catch (CurlException $e) {
            throw new RuntimeException($e->getMessage(), $e->getErrorNo(), $e);
        } catch (GuzzleBadResponse $e) {
            throw BadResponseException::fromGuzzleResponse($e);
        } catch (GuzzleException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        } catch (\Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        $ret =             json_encode([
                'meta'     => [
                    'http_code'     => $response->getStatusCode(),
                 //   'error_message' => $response->getMessage(),
                    'error_details' => $response->getReasonPhrase(),
                    'response_time' => $response->getDate(),
                ],
          //      'response' => json_decode($response->getBody(true), true),
                'response' => $response->getBody(true),
            ]);

        return $ret;
    }

    /**
     * Creates a new instance of GuzzleAdapter
     *
     * @param string $endpoint
     * @param EventSubscriberInterface[] $plugins
     * @param int $endpointVersion
     * @return static
     */
    public static function create(
        $endpoint,
        $locale,
        array $plugins = array()
    ) {
        if (!is_string($endpoint)) {
            throw new InvalidArgumentException('API url endpoint must be a valid url');
        }

        $guzzle = new Guzzle($endpoint);
        $guzzle->setUserAgent(sprintf(
            '%s version %s',
            ApplicationInterface::USER_AGENT,
            ApplicationInterface::VERSION
        ));

        $guzzle->setDefaultOption('headers/Accept-Language', $locale);

        foreach ($plugins as $plugin) {
            $guzzle->addSubscriber($plugin);
        }

        return new static($guzzle, $locale);
    }

    private function addRequestParameters(RequestInterface $request, $query, $postFields, $files)
    {
        foreach ($query as $name => $value) {
            $request->getQuery()->add($name, $value);
        }

        if ($request instanceof EntityEnclosingRequestInterface) {
            if ($request->getHeader('Content-Type') == 'application/json') {
                $request->setBody(json_encode($postFields));

                return;
            }

            foreach ($postFields as $name => $value) {
                $request->getPostFields()->add($name, $value);
            }
            foreach ($files as $name => $filename) {
                $request->addPostFile($name, $filename);
            }
        } elseif (0 < count($postFields)) {
            throw new InvalidArgumentException('Can not add post fields to GET request');
        }
    }
}
