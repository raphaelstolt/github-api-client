<?php
require_once 'Zend/Service/GitHub.php';
require_once 'Zend/Service/GitHubOfflineTestcase.php';

class Zend_Service_GitHub_Repos_KeyOfflineTest extends Zend_Service_GitHubOfflineTestcase
{
    protected $gitHubClient;
    
    public function testUseOfReposKeyApiPartReturnsAGitHubInstance()
    {
        $assertionMessage = "Use of repos API part didn't return "
            . "expected Zend_Service_GitHub instance.";
        $this->assertTrue($this->_gitHubClient->repos->key instanceof Zend_Service_GitHub, 
            $assertionMessage
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testIncompleteParamsRaisesExcpectedException()
    {
        $this->_gitHubClient->repos->key->add('foo', array('foo' => 'bar', 'key' => 'ssh-rsa'));
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testNonsetKeyNameInParamsRaisesExcpectedException()
    {
        $this->_gitHubClient->repos->key->add('foo', array('title' => '', 'key' => 'ssh-rsa'));
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testNonsetKeyInParamsRaisesExcpectedException()
    {
        $this->_gitHubClient->repos->key->add('foo', array('title' => 'abc', 'key' => ''));
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testInvalidKeyInParamsRaisesExcpectedException()
    {
        $this->_gitHubClient->repos->key->add('foo', array('title' => 'abc', 'key' => 'ssh-zit'));
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnUnauthorizedKeyAddRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->repos->key->add('foo', array('title' => 'abc', 'key' => 'ssh-rsa'));
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAKeyAddResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->repos->key->add('foo', array('title' => 'abc', 'key' => 'ssh-rsa'));
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnUnauthorizedKeyRemoveRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->repos->key->remove('foo', 100);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAKeyRemoveResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->repos->key->remove('foo', 1000);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnNonNumericKeyIdRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->repos->key->remove('foo', 'abnbsnbs');
    }
    public function testThatKeyIsAddedToRepository()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-create-key')
        );
        $response = $this->_gitHubClient->repos->key->add(
            Zend_Service_GitHub_Repos_KeyOnlineTest::TEST_REPOS_CREATE_NAME, array(
                'title' => Zend_Service_GitHub_Repos_KeyOnlineTest::TEST_KEY_CREATE_NAME, 
                'key' => Zend_Service_GitHub_Repos_KeyOnlineTest::TEST_DEPLOY_KEY)
            );
        $this->assertSame($response['public_keys'][0]['title'], 
            Zend_Service_GitHub_Repos_KeyOnlineTest::TEST_KEY_CREATE_NAME
        );
    }
    public function testThatKeyIsRemovedFromRepository()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-remove-key')
        );
        $response = $this->_gitHubClient->repos->key->remove(
            Zend_Service_GitHub_Repos_KeyOnlineTest::TEST_REPOS_CREATE_NAME, 100
        );
        $this->assertTrue(count($response['public_keys']) === 0);
    }
    public function testThatRepositoryKeysAreListed()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-keys')
        );
        $response = $this->_gitHubClient->repos->keys('foo');
        $publicKeys = $response['public_keys'];
        foreach ($publicKeys as $index => $key) {
            if ($index === 0) {
                $this->assertSame($key['title'], 'key_one');
            } elseif ($index === 1) {
                $this->assertSame($key['title'], 'key_two');
            }
        }
    }
}
