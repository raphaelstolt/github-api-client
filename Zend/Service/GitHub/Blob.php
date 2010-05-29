<?php

class Zend_Service_GitHub_Blob extends Zend_Service_GitHub
{
    /**
     * Supported GitHub API parts
     * @var array
     */
    protected $_supportedMethods = array(
        'all',
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
     * Shows the blob data via given tree SHA and given path (file). 
     * When the path is ommited the blob data is shown for the
     * given tree SHA and the response is returned as a string since
     * this format is delivered back by the GitHub API.
     *
     * @param string $user       The owner of the repository
     * @param string $repository The repository the branch is associated to
     * @param string $treeSha    The SHA key of the tree
     * @param string $path       The path to a specific file in the repository
     * @return mixed
     * @throws Zend_Service_GitHub_Exception
     */
    protected function _show($user, $repository, $treeSha, $path = null)
    {
        $this->_init();
        if ($path === null) {
            $_path = sprintf('/blob/show/%s/%s/%s', 
                $user, 
                $repository, 
                $treeSha
            );
        } else {
            $_path = sprintf('/blob/show/%s/%s/%s/%s', 
                $user, 
                $repository, 
                $treeSha,
                $path
            );
        }
        $response = $this->_get($_path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        if ($path !== null) {
            return Zend_Json::decode($response->getBody());
        }
        return $response->getBody();
    }
    /**
     * Gets all blobs for a given branch in a given repository.
     *
     * @param string $user       The owner of the repository
     * @param string $repository The repository the branch is associated to
     * @param string $branch     The branch of the repository
     * @return array
     * @throws Zend_Service_GitHub_Exception
     */
    protected function _all($user, $repository, $branch)
    {
        $this->_init();
        $path = sprintf('/blob/all/%s/%s/%s', 
            $user, 
            $repository, 
            $branch
        );
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
}