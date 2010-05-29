<?php

class Zend_Service_GitHub_Repos extends Zend_Service_GitHub
{
    /**
     * Supported GitHub API parts
     * @var array
     */
    protected $_supportedMethods = array(
        'show', 
        'search',
        'watch',
        'unwatch',
        'fork',
        'create',
        'delete',
        'keys',
        'collaborators',
        'network',
        'languages',
        'tags',
        'branches'
    );
    const PUBLIC_REPOS_VALUE = 1;
    const PRIVATE_REPOS_VALUE = 0;
    /**
     * @param string $user The GitHub user (owner) of the repository.
     * @param string $user The name of a specific repository.
     */
    protected function _show($user, $repository = null)
    {
        $this->_init();
        if ($repository == null) {
            $path = sprintf('/repos/show/%s', $user);
        } else {
            $path = sprintf('/repos/show/%s/%s', $user, $repository);
        }
        $response = $this->_get($path);
        return Zend_Json::decode($response->getBody());
    }
    /**
     * @param string The query to search against all repositories.
     */
    protected function _search($query)
    {
        $path = sprintf('/repos/search/%s', $query);
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * @param string $user The GitHub user (owner) of the repository.
     * @param string $user The name of the repository to watch.
     */
    protected function _watch($user, $repository)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = sprintf('/repos/watch/%s/%s', $user, $repository);
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * @param string $user The GitHub user (owner) of the repository.
     * @param string $user The name of the repository to unwatch.
     */
    protected function _unwatch($user, $repository)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = sprintf('/repos/unwatch/%s/%s', $user, $repository);
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * @param string $user The GitHub user (owner) of the repository.
     * @param string $user The name of the repository to fork.
     */
    protected function _fork($user, $repository)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = sprintf('/repos/fork/%s/%s', $user, $repository);
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Creates a repository.
     *
     * @param string $repository The name of the repository to create.
     * @param array $params Params to configure the repository. 
     * @throws Zend_Service_GitHub_Exception if provided Uri or public value is invalid or
     * when the repository already exists.
     */
    protected function _create($repository, array $params = array())
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        
        $path = sprintf('/repos/create');
        $_params = array();
        $_params['name'] = $repository;
        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'description':
                    $_params['values']['description'] = $value;
                    break;
                case 'homepage':
                    $_params['values']['homepage'] = $this->_validUri($value);
                    break;
                case 'public':
                    $_params['values']['public'] = $this->_validPublicValue($value);
                    break;
                default:
                    break;
            }
        }
        if (!isset($_params['public'])) {
            $_params['public'] = self::PUBLIC_REPOS_VALUE;
        }
        $response = $this->_post($path, $_params);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Deletes a repository.
     *
     * @param string $repository The name of the repository to delete.
     * @return array 
     * @throws Zend_Service_GitHub_Exception
     */
    protected function _delete($repository)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = sprintf('/repos/delete/%s', $repository);
        $response = $this->_post($path);
        
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        $deleteToken = Zend_Json::decode($response->getBody());
        $response = $this->_post($path, $deleteToken);
        
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Gets the (deploy) keys of a given repository
     *
     * @param string $repository
     * @return array
     * @throws Zend_Service_GitHub_Exception
     */
    public function _keys($repository)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = sprintf('/repos/keys/%s', $repository);
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Gets the collaborators of a given repository.
     *
     * @param string $user
     * @param string $repository
     * @return array
     * @throws Zend_Service_GitHub_Exception
     */
    public function _collaborators($user, $repository)
    {
        $this->_init();
        $path = sprintf('/repos/show/%s/%s/collaborators', $user, $repository);
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Gets the network of a given repository.
     *
     * @param string $user
     * @param string $repository
     * @return array
     * @throws Zend_Service_GitHub_Exception
     */
    public function _network($user, $repository)
    {
        $this->_init();
        $path = sprintf('/repos/show/%s/%s/network', $user, $repository);
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Gets the (programming) languages of a given repository.
     *
     * @param string $user
     * @param string $repository
     * @return array
     * @throws Zend_Service_GitHub_Exception
     */
    public function _languages($user, $repository)
    {
        $this->_init();
        $path = sprintf('/repos/show/%s/%s/languages', $user, $repository);
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Gets the tags of a given repository.
     *
     * @param string $user
     * @param string $repository
     * @return array
     * @throws Zend_Service_GitHub_Exception
     */
    public function _tags($user, $repository)
    {
        $this->_init();
        $path = sprintf('/repos/show/%s/%s/tags', $user, $repository);
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Gets the tags of a given repository.
     *
     * @param string $user
     * @param string $repository
     * @return array
     * @throws Zend_Service_GitHub_Exception
     */
    public function _branches($user, $repository)
    {
        $this->_init();
        $path = sprintf('/repos/show/%s/%s/branches', $user, $repository);
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Returns the public (creation) value of a repository.
     *
     * @param  integer $value
     * @return integer $value
     * @throws Zend_Service_GitHub_Exception if the public 
     * repository value is not supported. Possible values are:
     * 1 => public and 0 => private.     
     */
    private function _validPublicValue($value) 
    {
        $acceptedValues = array(
            self::PUBLIC_REPOS_VALUE, 
            self::PRIVATE_REPOS_VALUE
        );
        if (in_array($value, $acceptedValues)) {
            return $value;
        }
        require_once 'Zend/Service/GitHub/Exception.php';
        $exceptionMessage = "Invalid public value '%d' "
            . "provided";
        $exceptionMessage = sprintf($exceptionMessage, $value);    
        throw new Zend_Service_GitHub_Exception($exceptionMessage);
    }
}