<?php
require_once 'Zend/Service/GitHub/User/Key.php';
require_once 'Zend/Service/GitHubOfflineTestcase.php';

class Zend_Service_GitHub_User_KeyOfflineTest extends Zend_Service_GitHubOfflineTestcase
{
    const TEST_KEY_NAME = 'test_case_key';
    const TEST_KEY_VALUE = "ssh-rsa ??? ==";
    
    public function testUseOfKeyApiPartReturnsAGitHubInstance()
    {
        $assertionMessage = "Use of key API part didn't return "
            . "expected Zend_Service_GitHub instance.";
        $this->assertTrue($this->_gitHubClient->user->key instanceof Zend_Service_GitHub, 
            $assertionMessage
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnUnauthorizedAddKeyRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->user->key->add(self::TEST_KEY_NAME, self::TEST_KEY_VALUE);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnUseOfAnUnauthorizedRemoveKeyRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->user->key->remove(0);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */    
    public function testThatInvalidKeyIdRaisesExpectedException()
    {
        $this->_gitHubClient->user->key->remove('abcd');
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnAddKeyResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->user->key->add(self::TEST_KEY_NAME, self::TEST_KEY_VALUE);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatARemoveKeyResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->user->key->remove(2010);
    }
    public function testThatAPublicKeyIsAddedAndRemoved()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('key-add')
        );
        $response = $this->_gitHubClient->user->key->add(
            self::TEST_KEY_NAME, 
            self::TEST_KEY_VALUE
        );
        $lastAddedKey = array_pop($response['public_keys']);
        $this->assertEquals($lastAddedKey['title'], self::TEST_KEY_NAME);
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('key-remove')
        );
        $response = $this->_gitHubClient->user->key->remove($lastAddedKey['id']);
        $lastAddedKey = array_pop($response['public_keys']);
        $this->assertFalse($lastAddedKey['title'] === self::TEST_KEY_NAME);
    }
    public function testThatKeysAreListed()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('key-add')
        );
        
        $response = $this->_gitHubClient->user->key->add(
            self::TEST_KEY_NAME, 
            self::TEST_KEY_VALUE
        );
        $lastAddedKey = array_pop($response['public_keys']);
        
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('key-list')
        );
        
        $response = $this->_gitHubClient->user->keys();
        $this->assertTrue(count($response['public_keys']) > 0);
        $this->_gitHubClient->user->key->remove($lastAddedKey['id']);
    }
}