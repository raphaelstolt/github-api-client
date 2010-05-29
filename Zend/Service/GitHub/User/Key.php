<?php
/**
 * @see Zend_Service_GitHub
 */
class Zend_Service_GitHub_User_Key extends Zend_Service_GitHub
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
     * Adds a public key to the current GitHub account. 
     *
     * @param string $title The title (name) of the key 
     * @param string $key
     * @throws Zend_Service_GitHub_Exception if client authorization is
     * not initialized or API interaction failed.
     */
    protected function _add($title, $key)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = '/user/key/add';
        $response = $this->_post($path, 
            array('title' => $title, 'key' => $key)
        );
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Removes a public key from the current GitHub account. 
     *
     * @param string $id The id of the key 
     * @throws Zend_Service_GitHub_Exception if client authorization is
     * not initialized, invalid key id is provided or API interaction failed.
     */
    protected function _remove($id)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = '/user/key/remove';
        $response = $this->_post($path, 
            array('id' => $this->_validInteger($id))
        );
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
}