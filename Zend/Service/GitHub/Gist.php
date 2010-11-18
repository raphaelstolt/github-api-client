<?php

class Zend_Service_GitHub_Gist extends Zend_Service_GitHub
{
    /**
     * Supported GitHub API parts
     * @var array
     */
    protected $_supportedMethods = array(
        'content',
        'list',
        'meta'
    );
    /**
     * @param string $login 
     * @param string $token
     */
    public function __construct($login = null, $token = null, $baseUri)
    {
        parent::__construct($login, $token, $baseUri);
    }
    /**
     * Gets the metadata of Gist.
     *
     * @param  integer $gistId  The id of the Gist
     * @return array
     * @throws Zend_Service_GitHub_Exception
     */
    protected function _meta($gistId)
    {
        $this->_init();
        $path = sprintf('%s/%s', Zend_Service_GitHub::API_ENTRY_PATH_GIST, $gistId);

        $response = $this->_get($path, null, false);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
}