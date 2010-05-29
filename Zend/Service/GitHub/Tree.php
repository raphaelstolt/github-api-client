<?php

class Zend_Service_GitHub_Tree extends Zend_Service_GitHub
{
    /**
     * Supported GitHub API parts
     * @var array
     */
    protected $_supportedMethods = array(
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
     * Gets the repository tree for a given tree SHA.
     *
     * @param  string $user       The owner of the repository
     * @param  string $repository The repository the branch is associated to
     * @param  string $treeSha    The SHA key of the tree
     * @return array
     * @throws Zend_Service_GitHub_Exception
     */
    protected function _show($user, $repository, $treeSha)
    {
        $this->_init();
        $path = sprintf('/tree/show/%s/%s/%s', 
            $user, 
            $repository, 
            $treeSha
        );
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
}