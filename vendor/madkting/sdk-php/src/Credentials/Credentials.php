<?php

namespace Madkting\Credentials;

/**
 * Basic implementation of the Madkting Credentials interface that allows callers to
 * pass in the Madkting Token in the constructor.
 */
class Credentials
{
    private $token;

    /**
     * Constructs a new MadktingCredentials object, with the specified Madkting token
     *
     * @param string $token   Security token to use
     */
    public function __construct($token = null)
    {
        $this->token = $token;
    }

    public static function __set_state(array $state)
    {
        return new self(
            $state['token']
        );
    }

    public function getSecurityToken()
    {
        return 'Token ' . $this->token;
    }

    public function toArray()
    {
        return array(
            'token' => $this->token
        );
    }

    public function __serialize()
    {
        return json_encode($this->toArray());
    }

    public function __unserialize($serialized)
    {
        $data = json_decode($serialized, true);

        $this->token = $data['token'];
    }
}