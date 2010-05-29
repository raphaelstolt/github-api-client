<?php
/**
 * @see Zend_Service_GitHub
 */
class Zend_Service_GitHub_User_Email extends Zend_Service_GitHub
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
     * Adds an email address to the current GitHub account. 
     *
     * @param string $email
     * @throws Zend_Service_GitHub_Exception if provided email is invalid
     * or API interaction failed.
     */
    protected function _add($email)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = '/user/email/add';
        $response = $this->_post($path, 
            array('email' => $this->_validEmail($email))
        );
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Removes an email address from the current GitHub account. 
     *
     * @param string $email
     * @throws Zend_Service_GitHub_Exception if provided email is invalid
     * or API interaction failed.
     */
    protected function _remove($email)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = '/user/email/remove';
        $response = $this->_post($path, 
            array('email' => $this->_validEmail($email))
        );
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
}