<?php
/**
 * @group offline
 */
class Zend_Service_GitHub_CommitsOfflineTest extends Zend_Service_GitHubOfflineTestcase
{
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testListResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-not-found')
        );
        $this->_gitHubClient->commits->list(
            'mojombo', 
            'grit', 
            'master'
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testListOnGivenFileInBranchResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-not-found')
        );
        $this->_gitHubClient->commits->list(
            'mojombo', 
            'master',
            'grit',
            'Rakefile'
        );
    }
    public function testListShowsCommitsOfGivenBranch()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('commits-list')
        );
        $response =  $this->_gitHubClient->commits->list(
            'mojombo', 
            'grit', 
            'master'
        );
        $this->assertTrue(isset($response['commits']));
        $firstCommit = array_pop($response['commits']);
        $this->assertSame($firstCommit['committer']['login'], 'mojombo');
    }
    public function testListShowsCommitsOfGivenFilepathInBranch()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('commits-list-path')
        );
        $response =  $this->_gitHubClient->commits->list(
            'mojombo', 
            'grit', 
            'master',
            'Rakefile'
        );
        $this->assertTrue(isset($response['commits']));
        $firstCommit = array_pop($response['commits']);
        $this->assertContains('grit setup', $firstCommit['message']);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testShowResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-not-found')
        );
        $this->_gitHubClient->commits->show(
            'mojombo', 
            'grit',
            '2c6af5a45ddf8b539e8481d7bf9dff4bc71dde78'
        );
    }
    public function testShowGetsTheChangesOfGivenCommit()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('commits-show')
        );
        $response =  $this->_gitHubClient->commits->show(
            'mojombo', 
            'grit',
            '2c6af5a45ddf8b539e8481d7bf9dff4bc71dde78'
        );
        $this->assertTrue(isset($response['commit']));
        $this->assertSame(
            'test/fixtures/for_each_ref', 
            $response['commit']['added'][0]['filename']
        );
    }
}