<?php

class Zend_Service_GitHub_Issues_Label extends Zend_Service_GitHub
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
     * Adds a label to a given repository issue.
     *
     * @param string $user
     * @param string $repository
     * @param string $label
     * @param integer $issueNumber
     * @throws Zend_Service_GitHub_Exception 
     * @return array
     */
    protected function _add($user, $repository, $label, $issueNumber)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = sprintf('issues/label/add/%s/%s/%s/%d', 
            $user,
            $repository,
            $this->_validIssueLabel($label),
            $this->_validInteger($issueNumber)
        );
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Removes a label from a given repository issue.
     *
     * @param string $user
     * @param string $repository
     * @param string $label
     * @param integer $issueNumber
     * @throws Zend_Service_GitHub_Exception 
     * @return array
     */
    protected function _remove($user, $repository, $label, $issueNumber)
    {
        $this->_init();
        if (!$this->_isAuthorizationInitialized()) {
            $this->_throwNonAuthorizationInitializationException();
        }
        $path = sprintf('issues/label/remove/%s/%s/%s/%d', 
            $user,
            $repository,
            $this->_validIssueLabel($label),
            $this->_validInteger($issueNumber)
        );
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Returns a valid issue label.
     *
     * @param  string $label A label for the issue.
     * @return string
     * @throws Zend_Service_Github_Exception if the label is empty.
     */
    private function _validIssueLabel($label)
    {
        $len = iconv_strlen($label, 'UTF-8');
        if (0 == $len) {
            $exceptionMessage = 'Issue label must contain at least one '
                . 'character';
            throw new Zend_Service_Github_Exception($exceptionMessage);
        } 
        return str_replace(' ', '_', trim($label));
    }
}
