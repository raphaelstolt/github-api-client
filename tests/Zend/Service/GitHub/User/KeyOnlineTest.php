<?php
require_once 'Zend/Service/GitHubOnlineTestcase.php';
require_once 'Zend/Service/GitHub/User/Key.php';

class Zend_Service_GitHub_User_KeyOnlineTest extends Zend_Service_GitHubOnlineTestcase
{
    const TEST_KEY_NAME = 'test_case_key';
    const TEST_KEY_VALUE = "ssh-rsa ??? ==";
    const PRIMARY_KEY_NAME = 'home';
    
    public function testThatAPublicKeyIsAddedAndRemoved()
    {
        $response = $this->_gitHubClient->user->key->add(
            self::TEST_KEY_NAME, 
            self::TEST_KEY_VALUE
        );
        $lastAddedKey = array_pop($response['public_keys']);
        $this->assertEquals($lastAddedKey['title'], self::TEST_KEY_NAME);
        $response = $this->_gitHubClient->user->key->remove($lastAddedKey['id']);
        $lastAddedKey = array_pop($response['public_keys']);
        $this->assertFalse($lastAddedKey['title'] === self::TEST_KEY_NAME);
    }    
    public function testThatKeysAreListed()
    {
        $response = $this->_gitHubClient->user->key->add(
            self::TEST_KEY_NAME, 
            self::TEST_KEY_VALUE
        );
        $lastAddedKey = array_pop($response['public_keys']);
        $response = $this->_gitHubClient->user->keys();
        $this->assertTrue(count($response['public_keys']) > 0);
        $this->_gitHubClient->user->key->remove($lastAddedKey['id']);
    }
}
