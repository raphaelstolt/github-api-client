<?php
require_once 'Zend/Http/Client/Adapter/Test.php';

class Zend_Service_GitHubOfflineTestcase extends PHPUnit_Framework_TestCase
{
    protected $_gitHubClient;
    protected $_filesPath;
    protected $_httpClientAdapterTest;
    
    public function setUp()
    {
        parent::setUp();
        $this->_gitHubClient = new Zend_Service_GitHub(TESTS_ZEND_SERVICE_GITHUB_USER, 
            TESTS_ZEND_SERVICE_GITHUB_API_TOKEN);
        $this->_filesPath =  dirname(__FILE__) . '/_files';
        $this->_httpClientAdapterTest = new Zend_Http_Client_Adapter_Test();      
    }
    /**
     * Injects a Http Client test adapter for mocking responses.
     * @return void
     */
    protected function _injectHttpClientAdapterTest()
    {
        $client = new Zend_Http_Client();
        $client->setAdapter($this->_httpClientAdapterTest);
        Zend_Service_GitHub::setHttpClient($client);    
    }
    /**
     * Returns a stored response file content. 
     *
     * @param string $name Name of the reponse file
     * @return string
     */
    protected function _getStoredResponseContent($name)
    {
        $responseFile = $this->_filesPath . '/' . $name . '.response';
        if (!file_exists($responseFile)) {
            $exceptionMessage = "Response file %s doesn't exist.";
            $exceptionMessage = sprintf($exceptionMessage, $responseFile);
            throw new PHPUnit_Framework_Exception($exceptionMessage);
        }
        return trim(file_get_contents($responseFile));
    }
}