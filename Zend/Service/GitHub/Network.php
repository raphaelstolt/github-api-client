<?php

class Zend_Service_GitHub_Network extends Zend_Service_GitHub
{
    /**
     * Supported GitHub API parts
     * @var array
     */
    protected $_supportedMethods = array(
        'meta',
        'data'
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
     * Gets the meta data for a given repository.
     *
     * @param string $user        The owner of the repository
     * @param string $repository  The repository to get the meta data from
     * @return array
     */
    protected function _meta($user, $repository)
    {
        $this->_init();
        $path = sprintf('/%s/%s/network_meta', 
            $user, 
            $repository
        );
        $response = $this->_get($path, null, false);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Gets the network data for a given repository.
     *
     * @param string $user       The owner of the repository
     * @param string $repository The repository to get the meta data from
     * @param string $nethash    The nethash returned by meta data call
     * @return array
     * @see Zend_Service_GitHub_Network::_meta
     */
    protected function _data($user, $repository, $nethash)
    {
        $this->_init();
        $path = sprintf('/%s/%s/network_data_chunk', 
            $user, 
            $repository
        );
        $response = $this->_get($path, array('nethash' => $nethash), false);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
}