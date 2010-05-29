<?php
require_once 'Zend/Service/GitHubOnlineTestcase.php';
require_once 'Zend/Service/GitHub/Issues/Label.php';

class Zend_Service_GitHub_Issues_LabelOnlineTest extends Zend_Service_GitHubOnlineTestcase
{
    public function testLabelIsAdded()
    {
        $repositoryName = $this->_getRandomRepositoryName('zfghisla');
        $this->_createRepository($repositoryName);
        $response = $this->_createIssue(
            $repositoryName, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_TITLE, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_BODY
        );
        $issueNumber = $response['issue']['number'];
        
        $response = $this->_gitHubClient->issues->label->add(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $repositoryName, 
            Zend_Service_GitHub_Issues_LabelOfflineTest::TEST_ISSUE_LABEL, 
            $issueNumber
        );
        $this->_deleteRepository($repositoryName);
        $this->assertTrue(isset($response['labels']));
        $this->assertSame(
            $response['labels'][0], 
            Zend_Service_GitHub_Issues_LabelOfflineTest::TEST_ISSUE_LABEL
        );
    }
    public function testLabelIsRemoved()
    {
        $repositoryName = $this->_getRandomRepositoryName('zfghisla');
        $this->_createRepository($repositoryName);
        $response = $this->_createIssue(
            $repositoryName, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_TITLE, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_BODY
        );
        $issueNumber = $response['issue']['number'];
        $this->_addLabel(
            $repositoryName,
            Zend_Service_GitHub_Issues_LabelOfflineTest::TEST_ISSUE_LABEL,
            $issueNumber
        );
        $this->_addLabel(
            $repositoryName,
            Zend_Service_GitHub_Issues_LabelOfflineTest::TEST_ISSUE_LABEL . '_next',
            $issueNumber    
        );
        $response = $this->_gitHubClient->issues->label->remove(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $repositoryName, 
            Zend_Service_GitHub_Issues_LabelOfflineTest::TEST_ISSUE_LABEL, 
            $issueNumber
        );
        $this->_deleteRepository($repositoryName);
        $this->assertTrue(isset($response['labels']));
        $this->assertSame(
            $response['labels'][0], 
            Zend_Service_GitHub_Issues_LabelOfflineTest::TEST_ISSUE_LABEL . '_next'
        );
    }
}