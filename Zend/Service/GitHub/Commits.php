<?php

class Zend_Service_GitHub_Commits extends Zend_Service_GitHub
{
    /**
     * Supported GitHub API parts
     * @var array
     */
    protected $_supportedMethods = array(
        'list',
        'show'
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
     * Lists the commits of a given branch or for a given path (file).
     *
     * @param string $user       The owner of the repository
     * @param string $repository The repository the branch is associated to
     * @param string $branch     The name of the branch
     * @param string $path       The path to a specific file in the repository branch
     * @return array
     * @throws Zend_Service_GitHub_Exception 
     */
    protected function _list($user, $repository, $branch, $path = null)
    {
        $this->_init();
        if ($path === null) {
            $_path = sprintf('/commits/list/%s/%s/%s', 
                $user, 
                $repository, 
                $branch
            );
        } else {
            $_path = sprintf('/commits/list/%s/%s/%s/%s', 
                $user, 
                $repository, 
                $branch,
                $path
            );
        }
        $response = $this->_get($_path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /** 
     * Gets the changes made on a specific commit.
     *
     * @param string $user       The owner of the repository
     * @param string $repository The repository the commit is associated to
     * @param string $branch     The complette or a part of the commit sha key
     * @return array
     * @throws Zend_Service_GitHub_Exception 
     */
    protected function _show($user, $repository, $sha)
    {
        $this->_init();
        $path = sprintf('/commits/show/%s/%s/%s', 
            $user, 
            $repository, 
            $sha
        );
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
}