<?php
/**
 * @group offline
 */
class Zend_Service_GitHub_IssuesOfflineTest extends Zend_Service_GitHubOfflineTestcase
{
    const TEST_ISSUE_REPOS   = 'isfoo';
    const TEST_ISSUE_TITLE   = 'zfghissue';
    const TEST_ISSUE_BODY    = 'This was opened in a test scenario.';
    const TEST_ISSUE_NUMBER  = 1;
    const TEST_ISSUE_COMMENT = 'A test comment.';
    
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testUseOfAnInvalidStateRaisesExpectedException()
    {
        $this->_gitHubClient->issues->search(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            'missmatch',
            'foo'
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testUseOfNoSearchTermRaisesExpectedException()
    {
        $this->_gitHubClient->issues->search(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            'open',
            ''
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testSearchIssueResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-not-found')
        );
        $this->_gitHubClient->issues->search(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            'open',
            'foo'
        );
    }
    public function testSearchFindsSpecificTermInAnOpenIssue()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('issues-search')
        );
        $response = $this->_gitHubClient->issues->search(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_REPOS, 
            'open', 
            'scenario'
        );
        $this->assertTrue(isset($response['issues']));
        $this->assertSame($response['issues'][0]['title'], self::TEST_ISSUE_TITLE);
        $this->assertContains('was opened', $response['issues'][0]['body']);
        $this->assertContains('scenario', $response['issues'][0]['body']);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testListIssueResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->issues->list(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            'boo', 
            'open'
        );
    }
    public function testListHasSomeOpenIssues()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('issues-list')
        );
        $response = $this->_gitHubClient->issues->list(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            'open'
        );
        $this->assertTrue(isset($response['issues']));
        $this->assertSame($response['issues'][0]['title'], self::TEST_ISSUE_TITLE);
        $this->assertContains('extension', $response['issues'][1]['body']);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testShowIssueResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->issues->show(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS,
            self::TEST_ISSUE_NUMBER
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testShowWithInvalidIssueNumberRaisesExpectedException()
    {
        $this->_gitHubClient->issues->show(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS,
            'boo'
        );
    }
    public function testShowGetsASpecificIssue()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('issues-show')
        );
        $response = $this->_gitHubClient->issues->show(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_NUMBER
        );
        $this->assertTrue(isset($response['issue']));
        $this->assertSame($response['issue']['title'], self::TEST_ISSUE_TITLE);
        $this->assertSame($response['issue']['number'], self::TEST_ISSUE_NUMBER);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testUseOfInvalidIssueParamsRaisesExpectedException()
    {
        $issueParams = array(
            'title' => self::TEST_ISSUE_TITLE, 
            'buddy' => self::TEST_ISSUE_BODY
        );
        $this->_gitHubClient->issues->open(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            $issueParams
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testUnauthorizedOpenRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $issueParams = array(
            'title' => self::TEST_ISSUE_TITLE, 
            'body' => self::TEST_ISSUE_BODY
        );
        $gitHubClient->issues->open(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            $issueParams
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testOpenResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-not-found')
        );
        $issueParams = array(
            'title' => self::TEST_ISSUE_TITLE, 
            'body' => self::TEST_ISSUE_BODY
        );
        $this->_gitHubClient->issues->open(
            TESTS_ZEND_SERVICE_GITHUB_USER,
            'non_existent',
            $issueParams
        );
    }
    public function testAnIssueIsOpened()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('issues-open')
        );
        $issueParams = array(
            'title' => self::TEST_ISSUE_TITLE, 
            'body' => self::TEST_ISSUE_BODY
        );
        $response = $this->_gitHubClient->issues->open(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            $issueParams
        );
        $this->assertTrue(isset($response['issue']));
        $this->assertSame($response['issue']['state'], 'open');
        $this->assertSame($response['issue']['title'], self::TEST_ISSUE_TITLE);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testUnauthorizedCloseRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->issues->close(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_NUMBER
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testInvalidIssueNumberRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->issues->close(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            'bar'
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testCloseResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-not-found')
        );
        $this->_gitHubClient->issues->close(
            TESTS_ZEND_SERVICE_GITHUB_USER,
            'non_existent',
            self::TEST_ISSUE_NUMBER
        );
    }
    public function testAnIssueIsClosed()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('issues-close')
        );
        $response = $this->_gitHubClient->issues->close(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_NUMBER
        );
        $this->assertTrue(isset($response['issue']));
        $this->assertSame($response['issue']['state'], 'closed');
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testUnauthorizedReopenRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->issues->reopen(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_NUMBER
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testReopenResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-not-found')
        );
        $this->_gitHubClient->issues->reopen(
            TESTS_ZEND_SERVICE_GITHUB_USER,
            'non_existent',
            self::TEST_ISSUE_NUMBER
        );
    }
    public function testAnIssueIsReopened()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('issues-reopen')
        );
        $response = $this->_gitHubClient->issues->reopen(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_NUMBER
        );
        $this->assertTrue(isset($response['issue']));
        $this->assertSame($response['issue']['state'], 'open');
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testUnauthorizedCommentRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->issues->comment(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_NUMBER,
            self::TEST_ISSUE_COMMENT
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testInvalidCommentRaisesExpectedException()
    {
        $this->_gitHubClient->issues->comment(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_NUMBER,
            ''
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testCommentResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-not-found')
        );
        $this->_gitHubClient->issues->comment(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_NUMBER,
            self::TEST_ISSUE_COMMENT
        );
    }
    public function testAnIssueIsCommented()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('issues-comment')
        );
        $response = $this->_gitHubClient->issues->comment(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_NUMBER,
            self::TEST_ISSUE_COMMENT
        );
        $this->assertTrue(isset($response['comment']));
        $this->assertSame($response['comment']['body'], self::TEST_ISSUE_COMMENT);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testCommentsResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-not-found')
        );
        $this->_gitHubClient->issues->comments(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_NUMBER
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testCommentsWithInvalidIssueNumberRaisesExpectedException()
    {
        $this->_gitHubClient->issues->comments(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS,
            'boo'
        );
    }
    public function testIssueCommentsAreListed()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('issues-comments')
        );
        $response = $this->_gitHubClient->issues->comments(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_NUMBER
        );
        $this->assertTrue(isset($response['comments']));
        $this->assertTrue(count($response['comments']) == 2);
        $this->assertSame(
            $response['comments'][0]['body'], 
            self::TEST_ISSUE_COMMENT . ' One.'
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testUnauthorizedLabelsRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->issues->labels(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testLabelsResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-not-found')
        );
        $this->_gitHubClient->issues->labels(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testUnauthorizedEditRaisesExpectedException()
    {
        $issueParams = array(
            'title' => self::TEST_ISSUE_TITLE, 
            'body' => self::TEST_ISSUE_BODY
        );
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->issues->edit(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_NUMBER,
            $issueParams
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testInvalidIssueNumberInEditRaisesExpectedException()
    {
        $issueParams = array(
            'title' => self::TEST_ISSUE_TITLE, 
            'body' => self::TEST_ISSUE_BODY
        );
        $this->_gitHubClient->issues->edit(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            'invalid0',
            $issueParams
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testInvalidEditIssueParamsRaisesExpectedException()
    {
        $issueParams = array(
            'title' => self::TEST_ISSUE_TITLE, 
            'buddy' => self::TEST_ISSUE_BODY
        );
        $this->_gitHubClient->issues->edit(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_NUMBER,
            $issueParams
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testEditIssueResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-not-found')
        );
        $issueParams = array(
            'title' => self::TEST_ISSUE_TITLE, 
            'body' => self::TEST_ISSUE_BODY
        );
        $this->_gitHubClient->issues->edit(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            self::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_NUMBER,
            $issueParams
        );
    }
    public function testIssueIsEdited()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('issues-edit')
        );
        $issueParams = array(
            'title' => Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_TITLE . '_edit',
            'body' => Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_BODY . ' Added text.'
        );
        $response = $this->_gitHubClient->issues->edit(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_NUMBER,
            $issueParams
        );
        $this->assertTrue(isset($response['issue']));
        $this->assertSame(
            $response['issue']['body'], 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_BODY . ' Added text.'
        );
        $this->assertSame(
            $response['issue']['title'], 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_TITLE . '_edit'
        );
    }
}