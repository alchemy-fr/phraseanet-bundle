<?php

/*
 * This file is part of Phraseanet SDK.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phraseanet\PhraseanetSDK\Entity;

use Alchemy\Phraseanet\PhraseanetSDK\Entity\User;

class QuarantineSession
{

    public static function fromList(array $values)
    {
        $sessions = array();

        foreach ($values as $value) {
            $sessions[$value->id] = self::fromValue($value);
        }

        return $sessions;
    }

    public static function fromValue(\stdClass $value)
    {
        return new self($value);
    }

    /**
     * @var \stdClass
     */
    protected $source;

    /**
     * @var User
     */
    protected $user;

    /**
     * @param \stdClass $source
     */
    public function __construct(\stdClass $source)
    {
        $this->source = $source;
    }

    /**
     * @return \stdClass
     */
    public function getRawData()
    {
        return $this->source;
    }

    /**
     * The session id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->source->id;
    }

    /**
     * The user id
     *
     * @return integer
     */
    public function getUser()
    {
        return $this->user ?: $this->user = User::fromValue($this->source->user);
    }
}
