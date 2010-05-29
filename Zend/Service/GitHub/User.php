<?php
/**
 * @see Zend_Service_GitHub
 */
class Zend_Service_GitHub_User extends Zend_Service_GitHub
{
    /**
     * Supported GitHub API parts
     * @var array
     */
    protected $_supportedMethods = array(
        'search', 
        'show', 
        'keys',
        'emails',
        'followers',
        'following'
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
     * @param string $name
     * @throws Zend_Service_GitHub_Exception if provided name is invalid
     */
    protected function _search($name)
    {
        $this->_init();
        $path = sprintf('/user/search/%s', $this->_validName($name));
        $response = $this->_get($path);
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Shows or updates information on a GitHub user. If params are set
     * a update is issued with the given values.
     *
     * @param string $name
     * @param array $params
     * @throws Zend_Service_GitHub_Exception if provided name is invalid
     * or values of the params array are invalid 
     */
    protected function _show($name, array $params = array())
    {
        $this->_init();
        $path = sprintf('/user/show/%s', $this->_validName($name));
        
        if (count($params) === 0) {
            $response = $this->_get($path);
        } else {
            if (!$this->_isAuthorizationInitialized()) {
                $this->_throwNonAuthorizationInitializationException();
            }
            $_params = array();
            foreach ($params as $key => $value) {
                switch (strtolower($key)) {
                    case 'name':
                        $_params['values']['name'] = $this->_validName($value);
                        break;
                    case 'email':
                        $_params['values']['email'] = $this->_validEmail($value);
                        break;
                    case 'blog':
                        $_params['values']['blog'] = $this->_validUri($value);
                        break;
                    case 'company':
                        $_params['values']['company'] = $value;
                        break;
                    case 'location':
                        $_params['values']['location'] = $value;
                        break;
                    default:
                        break;
                }
            }
            $response = $this->_post($path, $_params);
        }
        if ($response->isError()) {
            $this->_throwResponseErrorException($response);
        }
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Gets the followers of a given GitHub user.
     * 
     * @param  string $name The name of the GitHub user.
     * @return array
     * @throws Zend_Service_GitHub_Exception if provided name is invalid
     */
    protected function _followers($name)
    {
        $this->_init();
        $path = sprintf('/user/show/%s/followers', $this->_validName($name));
        $response = $this->_get($path);
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Gets the GitHub users a given GitHub user follows.
     * 
     * @param  string $name The name of the GitHub user.
     * @return array
     * @throws Zend_Service_GitHub_Exception if provided name is invalid
     */
    protected function _following($name)
    {
        $this->_init();
        $path = sprintf('/user/show/%s/following', $this->_validName($name));
        $response = $this->_get($path);
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Gets the public keys of a given GitHub user.
     * 
     * @return array
     */
    protected function _keys() 
    {
        $this->_init();
        $path = '/user/keys';
        $response = $this->_get($path);
        return Zend_Json::decode($response->getBody());
    }
    /**
     * Gets the email addresses of a given GitHub user.
     * 
     * @return array
     */
    protected function _emails() 
    {
        $this->_init();
        $path = '/user/emails';
        $response = $this->_get($path);
        return Zend_Json::decode($response->getBody());
    }
}