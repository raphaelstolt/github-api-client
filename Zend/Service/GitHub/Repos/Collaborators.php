<?php

class Zend_Service_GitHub_Repos_Collaborators extends Zend_Service_GitHub
{
    /**
     * Supported GitHub API parts
     * @var array
     */
    protected $_supportedMethods = array(
        'add',
        'remove'
    );
    /**
     * @param string $login 
     * @param string $token
     */
    public function __construct($login = null, $token = null)
    {
        parent::__construct($login, $token);
    }
    /**
     * Adds a collaborator to the repository.
     *     
     * @param string $collaborator 
     * @param string $repository
     * @return array
     * @throws Zend_Service_GitHub_Exception
     */
    protected function _add($collaborator, $repository)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = sprintf('repos/collaborators/%s/add/%s', $repository, $collaborator);
        $response = $this->_post($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Removes a collaborator from the repository.
     *
     * @param string $collaborator 
     * @param string $repository
     * @return array
     * @throws Zend_Service_GitHub_Exception
     */
    protected function _remove($collaborator, $repository)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = sprintf('repos/collaborators/%s/remove/%s', $repository, $collaborator);
        $response = $this->_post($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
}