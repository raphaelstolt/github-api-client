<?php
require_once 'Zend/Service/GitHub.php';
require_once 'Zend/Service/GitHubOfflineTestcase.php';

class Zend_Service_GitHub_Repos_SetOfflineTest extends Zend_Service_GitHubOfflineTestcase
{
    const TEST_REPOS_CREATE_NAME = 'vis_repos';
    
    public function testUseOfReposApiPartReturnsAGitHubInstance()
    {
        $assertionMessage = "Use of repos API part didn't return "
            . "expected Zend_Service_GitHub instance.";
        $this->assertTrue($this->_gitHubClient->repos->set instanceof Zend_Service_GitHub, 
            $assertionMessage
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnUnauthorizedVisibilityChangeToPrivateRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->repos->set->private(self::TEST_REPOS_CREATE_NAME);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAVisibilityChangeToPrivateResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->repos->set->private(self::TEST_REPOS_CREATE_NAME);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnUnauthorizedVisibilityChangeToPublicRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->repos->set->public(self::TEST_REPOS_CREATE_NAME);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAVisibilityChangeToPublicResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->repos->set->public(self::TEST_REPOS_CREATE_NAME);
    }
    public function testRepositoryVisibilityIsChangedToPrivate()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-set-private')
        );
        $response = $this->_gitHubClient->repos->set->private(self::TEST_REPOS_CREATE_NAME);
        $this->assertTrue($response['repository']['private']);
    }
    public function testRepositoryVisibilityIsChangedToPublic()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-set-public')
        );
        $response = $this->_gitHubClient->repos->set->public(self::TEST_REPOS_CREATE_NAME);
        $this->assertFalse($response['repository']['private']);
    }
}
