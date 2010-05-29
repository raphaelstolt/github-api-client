<?php
require_once 'Zend/Service/GitHubOfflineTestcase.php';
require_once 'Zend/Service/GitHub/Issues/Label.php';

class Zend_Service_GitHub_Issues_LabelOfflineTest extends Zend_Service_GitHubOfflineTestcase
{
    const TEST_ISSUE_LABEL = 'test_label';
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testUseOfNoLabelRaisesExpectedException()
    {
        $this->_gitHubClient->issues->label->add(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_REPOS, 
            '',
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_NUMBER
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testUseOfInvalidIssueNumberRaisesExpectedException()
    {
        $this->_gitHubClient->issues->label->add(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_LABEL,
            'abc'
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testUnauthorizedAddRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->issues->label->add(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_LABEL,
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_NUMBER
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testAddResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-not-found')
        );
        $this->_gitHubClient->issues->label->add(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_LABEL,
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_NUMBER
        );
    }
    public function testLabelIsAdded()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('issues-label-add')
        );
        $response = $this->_gitHubClient->issues->label->add(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_REPOS, 
            Zend_Service_GitHub_Issues_LabelOfflineTest::TEST_ISSUE_LABEL, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_NUMBER
        );
        $this->assertTrue(isset($response['labels']));
        $this->assertSame(
            $response['labels'][0], 
            Zend_Service_GitHub_Issues_LabelOfflineTest::TEST_ISSUE_LABEL
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testUnauthorizedRemoveRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->issues->label->remove(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_LABEL,
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_NUMBER
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testRemoveResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-not-found')
        );
        $this->_gitHubClient->issues->label->remove(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_REPOS, 
            self::TEST_ISSUE_LABEL,
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_NUMBER
        );
    }
    public function testLabelIsRemoved()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('issues-label-remove')
        );
        $response = $this->_gitHubClient->issues->label->remove(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_REPOS, 
            Zend_Service_GitHub_Issues_LabelOfflineTest::TEST_ISSUE_LABEL, 
            Zend_Service_GitHub_IssuesOfflineTest::TEST_ISSUE_NUMBER
        );
        $this->assertTrue(isset($response['labels']));
        $this->assertSame(
            $response['labels'][0], 
            Zend_Service_GitHub_Issues_LabelOfflineTest::TEST_ISSUE_LABEL . '_next'
        );
    }
}