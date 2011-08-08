<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage GitHub
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
/**
 * @see Zend_Rest_Client
 */
require_once 'Zend/Rest/Client.php';
/**
 * @see Zend_Json
 */
require_once 'Zend/Json.php';
/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage GitHub
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_GitHub extends Zend_Rest_Client
{
    const API_ENTRY_PATH = '/api/v2/json';
    const MAX_LOGIN_LENGTH = 40;
    const SERVICE_BASE_URI = 'http://github.com';
    const SERVICE_GIST_URI = 'http://gist.github.com';
    const API_ENTRY_PATH_GIST = '/api/v1/json';

    /**
     * Whether or not authorization has been initialized.
     * @var bool
     */
    protected $_authorizationInitialized = false;
    /**
     * @var array
     */
    protected $_authorizationCredentials;
    /**
     * @var Zend_Http_CookieJar
     */
    protected $_cookieJar;
    /**
     * Local HTTP Client cloned from statically set client
     * @var Zend_Http_Client
     */
    protected $_localHttpClient;
    /**
     * The GitHub API token.
     * @var string
     */
    protected $_token;
    /**
     * The GitHub login.
     * @var string
     */
    protected $_login;
    /**
     * Current GitHub API component to (for method proxying)
     * @var string
     */
    protected $_currentApiComponent;
    protected $_currentApiPart;
    /**
     * Supported GitHub API parts
     * @var array
     */
    protected $_supportedApiParts = array(
        'user', 
        'issues', 
        'network', 
        'repos',
        'set',
        'commits', 
        'email',
        'key',
        'collaborators',
        'search',
        'label',
        'blob',
        'tree',
        'gist'
    );
    protected $_schemaBreakingApiPartsAndMappings = array(
        'gist' => self::SERVICE_GIST_URI
    );
    protected $_supportedSubApiParts = array(
        'user/key',
        'user/email',
        'repos/key',
        'repos/set',
        'repos/collaborators',
        'issues/label'
    );
    /**
     * @param  string $login 
     * @param  string $token
     * @throws Zend_Service_GitHub_Exception if login is invalid 
     */
    public function __construct($login = null, $token = null, $setBaseUri = true)
    {
        $this->setLocalHttpClient(clone self::getHttpClient());
        if (!is_null($login)) {
            $this->setLogin($this->_validLogin($login));
        }
        if (!is_null($token)) {
            $this->setToken($token);
        }
        if ($setBaseUri === true) {
            $this->setUri(self::SERVICE_BASE_URI);
        } else {
            $this->setUri($setBaseUri);
        }
        $this->_localHttpClient->setHeaders('Accept-Charset', 'ISO-8859-1,utf-8');
    }
    /**
     * Set local HTTP client as distinct from the static HTTP client
     * as inherited from Zend_Rest_Client.
     *
     * @param Zend_Http_Client $client
     * @return self
     */
    public function setLocalHttpClient(Zend_Http_Client $client)
    {
        $this->_localHttpClient = $client;
        return $this;
    }
    /**
     * Sets the GitHub login
     *
     * @param string $login
     * @return Zend_Service_GitHub
     */
    public function setLogin($login)
    {
        $this->_login = $login;
        $this->_authorizationInitialized = false;
        return $this;
    }
    /**
     * Gets the GitHub login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->_login;
    }
    /**
     * Sets the GitHub token
     *
     * @param string $token
     * @return Zend_Service_GitHub
     */
    public function setToken($token)
    {
        $this->_token = $token;
        $this->_authorizationInitialized = false;
        return $this;
    }
    /**
     * Gets the GitHub token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }
    /**
     * Gets the client's authorization status
     *
     * @return boolean
     */
    protected function _isAuthorizationInitialized()
    {
        return $this->_authorizationInitialized;
    }
    /**
     * Gets the authorization credentials for the GitHub
     * client.
     *
     * @return array
     */
    private function _getAuthorizationCredentials()
    {
        return $this->_authorizationCredentials;
    }
    /**
     * Initialize HTTP authentication
     *
     * @return void
     */
    protected function _init()
    {
        $client = $this->_localHttpClient;
        $client->resetParameters();
        if (null == $this->_cookieJar) {
            $client->setCookieJar();
            $this->_cookieJar = $client->getCookieJar();
        } else {
            $client->setCookieJar($this->_cookieJar);
        }
        if (!$this->_authorizationInitialized && 
            $this->getLogin() !== null && $this->getToken() !== null) {
            $this->_authorizationInitialized = true;
            $this->_authorizationCredentials = array(
                'login' => $this->getLogin(), 
                'token' => $this->getToken()
            );
        }
    }
    /**
     * Proxy the GitHub API parts
     *
     * @param  string $part
     * @return Zend_Service_Github
     * @throws Zend_Service_GitHub_Exception if part is not in supported
     * @see Zend_Service_GitHub::_supportedApiParts
     */
    public function __get($part)
    {
        if (!in_array($part, $this->_supportedApiParts)) {
            require_once 'Zend/Service/GitHub/Exception.php';
            $exceptionMessage  = "Unsupported API part '%s' used";
            $exceptionMessage = sprintf($exceptionMessage, $part);
            throw new Zend_Service_GitHub_Exception($exceptionMessage);
        }
        if ($this->_currentApiComponent !== null) {
            $possibleSubApiPart = sprintf('%s/%s', 
                $this->_currentApiPart,
                $part
            );
            if (in_array($possibleSubApiPart, $this->_supportedSubApiParts)) {
                $proxiedApiComponent = sprintf('%s_%s', get_class($this->_currentApiComponent), ucfirst($part));
            } else {
                $proxiedApiComponent = sprintf('%s_%s', __CLASS__, ucfirst($part));
            }
        } else {
            $proxiedApiComponent = sprintf('%s_%s', __CLASS__, ucfirst($part));
        }
        
        require_once str_replace('_', '/', $proxiedApiComponent. '.php');
        
        if (!class_exists($proxiedApiComponent)) {
            require_once 'Zend/Service/GitHub/Exception.php';
            $exceptionMessage  = "Nonexisting API component '%s' used";
            $exceptionMessage = sprintf($exceptionMessage, $proxiedApiComponent);
            throw new Zend_Service_GitHub_Exception($exceptionMessage);
        }
        $this->_currentApiPart = $part;
        $setBaseUri = true;         
        if (in_array($part, array_keys($this->_schemaBreakingApiPartsAndMappings))) {
            $setBaseUri = $this->_schemaBreakingApiPartsAndMappings[$part];
        }
        $this->_currentApiComponent = new $proxiedApiComponent(
            $this->getLogin(),
            $this->getToken(),
            $setBaseUri
        );
        return $this;
    }
    /**
     * Method overloading
     *
     * @param  string $method
     * @param  array $params
     * @return mixed
     * @throws Zend_Service_GitHub_Exception if unable to find method
     */
    public function __call($method, $params)
    {
        if ($this->_currentApiComponent === null) {
            require_once 'Zend/Service/GitHub/Exception.php';
            throw new Zend_Service_GitHub_Exception('No GitHub API component set');
        }
        $methodOriginal = $method;
        $method = sprintf("_%s", strtolower($method));
        
        if (!method_exists($this->_currentApiComponent, $method)) {
            require_once 'Zend/Service/GitHub/Exception.php';
            $exceptionMessage  = "Nonexisting API method '%s' used";
            $exceptionMessage = sprintf($exceptionMessage, $method);
            throw new Zend_Service_GitHub_Exception($exceptionMessage);
        }
        
        if (!in_array($methodOriginal, $this->_currentApiComponent->_supportedMethods)) {
            require_once 'Zend/Service/GitHub/Exception.php';
            $exceptionMessage  = "Unsupported API method '%s' used";
            $exceptionMessage = sprintf($exceptionMessage, $methodOriginal);
            throw new Zend_Service_GitHub_Exception($exceptionMessage);
        }
        return call_user_func_array(array(
            $this->_currentApiComponent, 
            $method), $params);
    }
    /**
     * Prepare the REST web service URI
     *
     * @param  string  $path The path to append to the URI
     * @param  boolean $addApiEntryPath Defaults to true
     * @throws Zend_Rest_Client_Exception
     * @return void
     */
    protected function _prepare($path, $addApiEntryPath = true)
    {
        // Get the URI object and configure it
        if (!$this->_uri instanceof Zend_Uri_Http) {
            require_once 'Zend/Rest/Client/Exception.php';
            $exceptionMessage  = 'URI object must be set before '
                . 'performing call';
            throw new Zend_Rest_Client_Exception($exceptionMessage);
        }

        $uri = $this->_uri->getUri();

        if ($path[0] != '/' && $uri[strlen($uri) - 1] != '/') {
            $path = '/' . $path;
        }
        if ($addApiEntryPath) {
            $this->_uri->setPath(self::API_ENTRY_PATH . $path);
        } else {
            $this->_uri->setPath($path);
        }
        
        /**
         * Get the HTTP client and configure it for the endpoint URI. Do this 
         * each time because the Zend_Http_Client instance is shared among all
         * Zend_Service_Abstract subclasses.
         */
        $this->_localHttpClient->resetParameters()->setUri($this->_uri);
    }
    /**
     * Performs an HTTP GET request to the $path.
     *
     * @param string  $path
     * @param array   $query Array of GET parameters
     * @param boolean $addApiEntryPath Defaults to true. Is needed to make outlier Network API part work
     * @throws Zend_Http_Client_Exception
     * @return Zend_Http_Response
     * @see GitHub::_prepare
     */
    protected function _get($path, array $query = null, $addApiEntryPath = true)
    {
        $this->_prepare($path, $addApiEntryPath);
        $this->_localHttpClient->setParameterGet($query);
        
        if ( $this->_authorizationCredentials ) {
            $this->_localHttpClient->setAuth(
                $this->getLogin() . "/token", 
                $this->getToken()
            );
        }
        return $this->_localHttpClient->request('GET');
    }
    /**
     * Performs an HTTP POST request to $path.
     *
     * @param string $path
     * @param mixed $data Raw data to send
     * @throws Zend_Http_Client_Exception
     * @return Zend_Http_Response
     */
    protected function _post($path, $data = null)
    {
        $this->_prepare($path);
        if ( $this->_authorizationCredentials ) {
            $this->_localHttpClient->setAuth(
                $this->getLogin() . "/token", 
                $this->getToken()
            );
        }
        return $this->_performPost('POST', $data);
    }    
    /**
     * Perform a POST or PUT
     *
     * Performs a POST or PUT request. Any data provided is set in the HTTP
     * client. String data is pushed in as raw POST data; array or object data
     * is pushed in as POST parameters.
     *
     * @param mixed $method
     * @param mixed $data
     * @return Zend_Http_Response
     */
    protected function _performPost($method, $data = null)
    {
        $client = $this->_localHttpClient;
        if (is_string($data)) {
            $client->setRawData($data);
        } elseif (is_array($data) || is_object($data)) {
            $client->setParameterPost((array) $data);
        }
        return $client->request($method);
    }
    /**
     * Validates a login.
     *
     * @param  string The GitHub login 
     * @return string     
     * @throws Zend_Service_GitHub_Exception
     */
    protected function _validLogin($login)
    {
        $len = iconv_strlen($login, 'UTF-8');
        if (0 === $len) {
            require_once 'Zend/Service/GitHub/Exception.php';
            $exceptionMessage = 'Login must contain at least one character';
            throw new Zend_Service_GitHub_Exception($exceptionMessage);
        } elseif (self::MAX_LOGIN_LENGTH < $len) {
            require_once 'Zend/Service/GitHub/Exception.php';
            $exceptionMessage = 'Login exceeds max length of %d characters';
            $exceptionMessage = sprintf($exceptionMessage, self::MAX_LOGIN_LENGTH);
            throw new Zend_Service_GitHub_Exception($exceptionMessage);
        }
        return $login;
    }
    /**
     * Returns the email or throws an Exception when invalid.
     *
     * @param  string $email The users email.
     * @return string
     * @throws Zend_Service_GitHub_Exception
     */
    protected function _validEmail($email)
    {
        require_once 'Zend/Validate.php';
        if (Zend_Validate::is($email, 'EmailAddress')) {
            return $email;
        }
        require_once 'Zend/Service/GitHub/Exception.php';
        $exceptionMessage = "Invalid email address '%s' "
            . "provided";
        $exceptionMessage = sprintf($exceptionMessage, $email);    
        throw new Zend_Service_GitHub_Exception($exceptionMessage);
    }
    /**
     * Validates a GitHub user name. Alias method for _validLogin.
     *
     * @param  string The GitHub name 
     * @return string     
     * @throws Zend_Service_GitHub_Exception
     * @see Zend_Service_GitHub::_validLogin
     */
    protected function _validName($name) {
        return $this->_validLogin($name);
    }
    /**
     * Validates that a given value is an integer.
     *
     * @param $int
     * @return integer
     * @throws Zend_Service_GitHub_Exception
     */
    protected function _validInteger($int)
    {
        require_once 'Zend/Validate.php';
        if (Zend_Validate::is($int, 'Int')) {
            return $int;
        }
        require_once 'Zend/Service/GitHub/Exception.php';
        $exceptionMessage = "Invalid integer '%d' "
            . "provided";
        $exceptionMessage = sprintf($exceptionMessage, $int);    
        throw new Zend_Service_GitHub_Exception($exceptionMessage);
    }
    /**
     * Returns the uri or throws an Exception when invalid.
     *
     * @param  string $uri The uri of the users blog.
     * @return string
     * @throws Zend_Service_GitHub_Exception
     */
    protected function _validUri($uri)
    {
        require_once 'Zend/Uri.php';
        if (Zend_Uri::check($uri)) {
            return $uri;
        }
        require_once 'Zend/Service/GitHub/Exception.php';
        $exceptionMessage = "Invalid uri '%s' provided";
        $exceptionMessage = sprintf($exceptionMessage, $uri);    
        throw new Zend_Service_GitHub_Exception($exceptionMessage);
    }
    /**
     * @param Zend_Http_Response $reponse
     */
    protected function _throwResponseErrorException(Zend_Http_Response $response)
    {
        require_once 'Zend/Service/GitHub/Exception.php';
        $exceptionMessage = 'The GitHub API interaction failed. '
            . '%s: %s';
        $exceptionMessage = sprintf($exceptionMessage, 
            $response->extractCode($response->asString()),
            $response->extractMessage($response->asString())
        );    
        throw new Zend_Service_GitHub_Exception($exceptionMessage);
    }
    /**
     *
     */
    protected function _throwNonAuthorizationInitializationException()
    {
        require_once 'Zend/Service/GitHub/Exception.php';
        $exceptionMessage = 'The GitHub client authorization is not '
            . 'initialized';   
        throw new Zend_Service_GitHub_Exception($exceptionMessage);
    }
}