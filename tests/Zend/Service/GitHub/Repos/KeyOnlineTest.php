<?php
require_once 'Zend/Service/GitHubOnlineTestcase.php';
require_once 'Zend/Service/GitHub.php';

class Zend_Service_GitHub_Repos_KeyOnlineTest extends Zend_Service_GitHubOnlineTestcase
{
    const TEST_REPOS_CREATE_NAME = 'repos_key_test';
    const TEST_KEY_CREATE_NAME = 'repos_deploy_key';
    const TEST_DEPLOY_KEY = "ssh-rsa TESTPLACEHOLDER";
    
    public function testThatKeyIsAddedToRepository()
    {
        $repositoryName = $this->_getRandomRepositoryName('zfghrkey');
        $this->_createRepository($repositoryName);
        $response = $this->_gitHubClient->repos->key->add(
            $repositoryName, array('title' => self::TEST_KEY_CREATE_NAME, 'key' => self::TEST_DEPLOY_KEY));
        $assertionMessage = 'Key was not added as excepted';
        $this->assertTrue(count($response['public_keys']) > 0, $assertionMessage);
        $this->assertSame($response['public_keys'][0]['title'], self::TEST_KEY_CREATE_NAME);
        $this->_deleteRepository($repositoryName);
    }
    public function testThatKeyIsRemovedFromRepository()
    {
        $repositoryName = $this->_getRandomRepositoryName('zfghrkey');
        $this->_createRepository($repositoryName);
        $response = $this->_gitHubClient->repos->key->add($repositoryName, array(
            'title' => self::TEST_KEY_CREATE_NAME, 
            'key' => self::TEST_DEPLOY_KEY)
        );
        $assertionMessage = 'Key to remove was not added as excepted';
        $this->assertTrue(count($response['public_keys']) > 0, $assertionMessage);
        $keyId = $response['public_keys'][0]['id'];
        $response = $this->_gitHubClient->repos->key->remove($repositoryName, $keyId);
        $assertionMessage = 'Key was not removed as excepted';
        $this->assertTrue(count($response['public_keys']) === 0, $assertionMessage);
        $this->_deleteRepository($repositoryName);
    }
    public function testThatRepositoryKeysAreListed()
    {
        $repositoryName = $this->_getRandomRepositoryName('zfghrkey');
        $this->_createRepository($repositoryName);
        $response = $this->_gitHubClient->repos->key->add($repositoryName, array(
            'title' => 'key_one', 
            'key' => self::TEST_DEPLOY_KEY)
        );
        $response = $this->_gitHubClient->repos->key->add($repositoryName, array(
            'title' => 'key_two', 
            'key' => self::TEST_DEPLOY_KEY)
        );
        $response = $this->_gitHubClient->repos->keys($repositoryName);
        $publicKeys = $response['public_keys'];
        foreach ($publicKeys as $index => $key) {
            if ($index === 0) {
                $this->assertSame($key['title'], 'key_one');
            } elseif ($index === 1) {
                $this->assertSame($key['title'], 'key_two');
            }
        }
        $this->_deleteRepository($repositoryName);
    }
}
