<?php
require_once 'Zend/Service/GitHub/Repos.php';
require_once 'Zend/Service/GitHubOfflineTestcase.php';

class Zend_Service_GitHub_Repos_CollaboratorsOfflineTest extends Zend_Service_GitHubOfflineTestcase
{
    public function testUseOfReposApiPartReturnsAGitHubInstance()
    {
        $assertionMessage = "Use of repos API part didn't return "
            . "expected Zend_Service_GitHub instance.";
        $this->assertTrue($this->_gitHubClient->repos->collaborators instanceof Zend_Service_GitHub, 
            $assertionMessage
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAListRepositoryCollaboratorsResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->repos->collaborators(TESTS_ZEND_SERVICE_GITHUB_USER, 'fooo');
    }
    public function testThatRepositoryCollaboratorsAreListed()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-collaborators')
        );
        $response = $this->_gitHubClient->repos->collaborators(TESTS_ZEND_SERVICE_GITHUB_USER, 'fooo');
        $expectedCollaborators = 'zfghclient;raphaelstolt';
        $this->assertSame($expectedCollaborators, implode(';', $response['collaborators']));
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnUnauthorizedCollaboratorAddRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->repos->collaborators->add('foo', 'foo');
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatACollaboratorAddResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->repos->collaborators->add('foo', 'foo');
    }
    public function testCollaboratorIsAddedToRepos()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-collaborators')
        );
        $response = $this->_gitHubClient->repos->collaborators->add('raphaelstolt', 'test');
        $expectedCollaborators = 'zfghclient;raphaelstolt';
        $this->assertSame($expectedCollaborators, implode(';', $response['collaborators']));
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnUnauthorizedCollaboratorRemoveRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->repos->collaborators->remove('foo', 'foo');
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatACollaboratorRemoveResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->repos->collaborators->remove('foo', 'foo');
    }
    public function testCollaboratorIsRemovedFromRepos()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-collaborators')
        );
        $response = $this->_gitHubClient->repos->collaborators->add('raphaelstolt', 'foo');
        
        $expectedCollaborators = 'zfghclient;raphaelstolt';
        $this->assertSame($expectedCollaborators, implode(';', $response['collaborators']));
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-remove-collaborators')
        );
        $response = $this->_gitHubClient->repos->collaborators->remove('raphaelstolt', 'foo');
        $expectedCollaborators = 'zfghclient';
        $this->assertSame($expectedCollaborators, implode(';', $response['collaborators']));
    }
}