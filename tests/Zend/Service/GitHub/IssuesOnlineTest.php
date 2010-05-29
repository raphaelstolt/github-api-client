<?php
/**
 * @group online
 */
class Zend_Service_GitHub_IssuesOnlineTest extends Zend_Service_GitHubOnlineTestcase
{
    public function testSearchFindsSpecificTermInAnOpenIssue()
    {
        $this->markTestSkipped('GitHub issue search seems to not work at the moment.');
        $this->_createRepository(Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_REPOS);
        $this->_createIssue(
             Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_REPOS,
             Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_TITLE,
             Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_BODY
        );
        
        $response = $this->_gitHubClient->issues->search(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_REPOS, 
            'open',
            'scenario'
        );
        $this->_deleteRepository(Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_REPOS);
        $this->assertTrue(isset($response['issues']));
        $this->assertTrue(count($response['issues']) > 0, 'There is no issue response body');
        $this->assertSame($response['issues'][0]['title'], Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_TITLE);
        $this->assertContains('scenario', $response['issues'][0]['body']);
    }
    
    public function testListHasSomeOpenIssues()
    {
        $repositoryName = $this->_getRandomRepositoryName('zfghis');
        $this->_createRepository($repositoryName);
        $this->_createIssue(
             $repositoryName,
             Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_TITLE,
             Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_BODY
        );
        
        $this->_createIssue(
             $repositoryName,
             Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_TITLE . '_second',
             Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_BODY . '. Body extension.'
        );
        
        $response = $this->_gitHubClient->issues->list(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $repositoryName, 
            'open'
        );
        $this->_deleteRepository($repositoryName);
        $this->assertTrue(isset($response['issues']));
        $this->assertSame($response['issues'][0]['title'], Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_TITLE);
        $this->assertContains('extension', $response['issues'][1]['body']);
    }
    
    public function testShowGetsASpecificIssue()
    {
        $repositoryName = $this->_getRandomRepositoryName('zfghis');
        $this->_createRepository($repositoryName);
        $response = $this->_createIssue(
             $repositoryName,
             Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_TITLE,
             Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_BODY
        );
        $issueNumber = $response['issue']['number'];
        $response = $this->_gitHubClient->issues->show(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $repositoryName, 
            $issueNumber
        );
        $this->_deleteRepository($repositoryName);
        $this->assertTrue(isset($response['issue']));
        $this->assertSame($response['issue']['title'], Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_TITLE);
        $this->assertSame($response['issue']['number'], Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_NUMBER);
    }
    
    public function testAnIssueIsOpened()
    {
        $repositoryName = $this->_getRandomRepositoryName('zfghis');
        $this->_createRepository($repositoryName);
        $issueParams = array(
            'title' => Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_TITLE, 
            'body' => Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_BODY
        );
        $response = $this->_gitHubClient->issues->open(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $repositoryName, 
            $issueParams
        );
        $this->assertTrue(isset($response['issue']));
        $this->assertSame($response['issue']['state'], 'open');
        $this->assertSame($response['issue']['title'], Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_TITLE);
        $this->_deleteRepository($repositoryName);
    }
    
    public function testAnIssueIsClosed()
    {
        $repositoryName = $this->_getRandomRepositoryName('zfghis');
        $this->_createRepository($repositoryName);
        $response = $this->_createIssue(
             $repositoryName,
             Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_TITLE,
             Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_BODY
        );
        $issueNumber = $response['issue']['number'];
        $response = $this->_gitHubClient->issues->close(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $repositoryName, 
            $issueNumber
        );
        $this->assertTrue(isset($response['issue']));
        $this->assertSame($response['issue']['state'], 'closed');
        $this->_deleteRepository($repositoryName);
    }
    
    public function testAnIssueIsReopened()
    {
        $repositoryName = $this->_getRandomRepositoryName('zfghis');
        $this->_createRepository($repositoryName);
        $response = $this->_createIssue(
             $repositoryName,
             Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_TITLE,
             Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_BODY
        );
        $issueNumber = $response['issue']['number'];
        
        $this->_closeIssue(
            $repositoryName,
            $issueNumber
        );
        
        $response = $this->_gitHubClient->issues->reopen(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $repositoryName, 
            $issueNumber
        );
        $this->assertTrue(isset($response['issue']));
        $this->assertSame($response['issue']['state'], 'open');
        $this->_deleteRepository($repositoryName);
    }
    
    public function testAnIssueIsCommented()
    {
        $repositoryName = $this->_getRandomRepositoryName('zfghis');
        $this->_createRepository($repositoryName);
        $response = $this->_createIssue(
             $repositoryName,
             Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_TITLE,
             Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_BODY
        );
        $issueNumber = $response['issue']['number'];
        
        $response = $this->_gitHubClient->issues->comment(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $repositoryName, 
            $issueNumber,
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_COMMENT
        );

        $this->assertTrue(isset($response['comment']));
        $this->assertSame($response['comment']['body'], 
          Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_COMMENT);
        $this->_deleteRepository($repositoryName);
    }
    public function testIssueCommentsAreListed()
    {
        $repositoryName = $this->_getRandomRepositoryName('zfghis');
        $this->_createRepository($repositoryName);
        $response = $this->_createIssue(
             $repositoryName,
             Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_TITLE,
             Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_BODY
        );
        $issueNumber = $response['issue']['number'];
        $this->_createIssueComment(
            $repositoryName, 
            $issueNumber, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_COMMENT . ' One.'
        );
        $this->_createIssueComment(
            $repositoryName, 
            $issueNumber, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_COMMENT . ' Two.'
        );
        $response = $this->_gitHubClient->issues->comments(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $repositoryName, 
            $issueNumber
        );
        $this->assertTrue(isset($response['comments']));
        $this->assertTrue(count($response['comments']) == 2);
        $this->assertSame(
            $response['comments'][0]['body'], 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_COMMENT . ' One.'
        );
        $this->_deleteRepository($repositoryName);
    }
    public function testIssueIsEdited()
    {
        $repositoryName = $this->_getRandomRepositoryName('zfghis');
        $this->_createRepository($repositoryName);
        $response = $this->_createIssue(
             $repositoryName,
             Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_TITLE,
             Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_BODY
        );
        $issueNumber = $response['issue']['number'];
        $issueParams = array(
            'title' => Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_TITLE . '_edit',
            'body' => Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_BODY . ' Added text.'
        );
        $response = $this->_gitHubClient->issues->edit(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $repositoryName, 
            $issueNumber,
            $issueParams
        );
        $this->_deleteRepository($repositoryName);
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