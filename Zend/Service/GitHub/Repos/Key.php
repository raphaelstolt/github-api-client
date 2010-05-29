<?php

class Zend_Service_GitHub_Repos_Key extends Zend_Service_GitHub
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
    protected function _add($repository, array $params)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = sprintf('/repos/key/%s/add', $repository);
        $response = $this->_post($path, $this->_validKeyParams($params));
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    protected function _remove($repository, $id)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = sprintf('/repos/key/%s/remove', $repository);
        $_params['id'] = $this->_validInteger($id);
        $response = $this->_post($path, $_params);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Returns the valid key params.
     *
     * @param array $params
     * @return array
     * @throws Zend_Service_Github_Exception if required params are not set.
     */
    private function _validKeyParams(array $params) {
        $supportedKeyParams = array('title', 'key');
        $keyParamsKeys = array_keys($params);
        if (count(array_intersect($supportedKeyParams, $keyParamsKeys)) === count($supportedKeyParams)) {
            $_params = array(
                'title' => $this->_validKeyTitle($params['title']), 
                'key' => $this->_validKey($params['key'])
            ); 
            return $_params;
        }
        require_once 'Zend/Service/GitHub/Exception.php';
        $exceptionMessage = "Invalid repos key param(s) '%s' "
            . "provided";
        $exceptionMessage = sprintf($exceptionMessage, implode(', ', $keyParamsKeys));    
        throw new Zend_Service_GitHub_Exception($exceptionMessage);
    }
    /**
     * Returns a valid key title.
     *
     * @param  string $title The title of the key.
     * @return string
     * @throws Zend_Service_Github_Exception if the key is empty.
     */
    private function _validKeyTitle($title)
    {
        $len = iconv_strlen($title, 'UTF-8');
        if (0 == $len) {
            $exceptionMessage = 'Repos key title must contain at least '
                . 'one character';
            throw new Zend_Service_Github_Exception($exceptionMessage);
        } 
        return $title;
    }
    /**
     * Returns a valid key.
     *
     * @param  string $key The actual key.
     * @return string
     * @throws Zend_Service_Github_Exception if the key is empty or does 
     * not begin with ssh-rsa or ssh-dss.
     */
    private function _validKey($key)
    {
        $len = iconv_strlen($key, 'UTF-8');
        if (0 == $len) {
            $exceptionMessage = 'Repos key must contain at least one '
                . 'character';
            throw new Zend_Service_Github_Exception($exceptionMessage);
        } 
        if (strpos($key, 'ssh-rsa') === false && strpos($key, 'ssh-dss') === false) {
            $exceptionMessage = "Repos key must begin with '%s' or '%s'";
            $exceptionMessage = sprintf($exceptionMessage, 'ssh-rsa', 'ssh-dss');
            throw new Zend_Service_Github_Exception($exceptionMessage);
        }
        return $key;
    }
}