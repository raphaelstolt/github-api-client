<?php

class Zend_Service_GitHub_Repos_Set extends Zend_Service_GitHub
{
    /**
     * Supported GitHub API parts
     * @var array
     */
    protected $_supportedMethods = array(
        'public', 
        'private'
    );
    /**
     * @param string $login 
     * @param string $token
     */
    public function __construct($login = null, $token = null)
    {
        parent::__construct($login, $token);
    }
    protected function _public($repository)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = sprintf('/repos/set/public/%s', $repository);
        $response = $this->_post($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    
    protected function _private($repository)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = sprintf('/repos/set/private/%s', $repository);
        $response = $this->_post($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
}