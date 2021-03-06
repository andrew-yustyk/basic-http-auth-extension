<?php

namespace Behat\BasicHttpAuthExtension\Config\Definition\Builder;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;

/**
 * Class BasicHttpAuthConfigBuilderUtil.
 */
class AuthConfigBuilder
{

    /**
     * Appends ArrayNodeDefinition for HTTP Auth to the root node.
     *
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $root
     *  The root node for HTTP Auth config.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function appendAuthNode(ArrayNodeDefinition $root)
    {
        $root->append($this->authNode());
    }

    /**
     * Returns node definition for array with HTTP auth parameters.
     *
     * @return ArrayNodeDefinition
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function authNode()
    {
        $auth = new ArrayNodeDefinition('auth');
        $auth->cannotBeEmpty()
            ->addDefaultsIfNotSet()
            ->disallowNewKeysInSubsequentConfigs()
            ->append($this->userNode())
            ->append($this->passwordNode())
        ;

        return $auth;
    }

    /**
     * Returns definition for HTTP Auth user parameter.
     *
     * @return \Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function userNode()
    {
        $user = new ScalarNodeDefinition('user');

        $user->cannotBeEmpty()
            ->defaultFalse()
            ->treatNullLike(false)
            ->validate()
                ->ifTrue($this->invalidUserParameter())
                ->thenInvalid('HTTP Auth user should be non empty string')
            ->end();

        return $user;
    }

    /**
     * Returns closure for HTTP Auth user validation.
     *
     * @return \Closure
     */
    protected function invalidUserParameter()
    {
        return function ($user) {
            // Valid user value is false or not empty string.
            return !(false === $user || (is_string($user) && '' !== $user));
        };
    }

    /**
     * Returns definition for HTTP Auth password parameter.
     *
     * @return \Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function passwordNode()
    {
        $password = new ScalarNodeDefinition('password');

        $password->defaultValue('')
            ->treatFalseLike('')
            ->treatNullLike('')
            ->validate()
                ->ifTrue($this->invalidPasswordParameter())
                ->thenInvalid('HTTP Auth password should be a string')
            ->end();

        return $password;
    }

    /**
     * Returns closure for HTTP Auth password validation.
     *
     * @return \Closure
     */
    protected function invalidPasswordParameter()
    {
        return function ($pass) {
            // Valid password value is null or false or any string.
            return !(null === $pass || false === $pass || is_string($pass));
        };
    }
}
