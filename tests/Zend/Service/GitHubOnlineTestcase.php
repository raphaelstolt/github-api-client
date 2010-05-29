<?php
require_once 'Zend/Http/Client/Adapter/Test.php';

class Zend_Service_GitHubOnlineTestcase extends PHPUnit_Framework_TestCase
{
    protected $_gitHubClient;
    
    public function setUp()
    {
        if (!defined('TESTS_ZEND_SERVICE_GITHUB_ONLINE_ENABLED') || 
            TESTS_ZEND_SERVICE_GITHUB_ONLINE_ENABLED === false) {
            return $this->markTestSkipped('GitHub online tests are not enabled');
        }
        // Needed due to GitHub API call limitation (60/min)
        sleep(4);
        $this->_gitHubClient = new Zend_Service_GitHub(TESTS_ZEND_SERVICE_GITHUB_USER, 
            TESTS_ZEND_SERVICE_GITHUB_API_TOKEN);
    }
    /**
     * Helper method for getting a random repository name.
     *
     * @param  string $prefix The prefix of the repository name
     * @return string
     */
    protected function _getRandomRepositoryName($prefix) 
    {
        $length = 5 + strlen($prefix);
        $chars = 'abcdefghijklmnopqrstuvwxyz';
        $charsLength = (strlen($chars) - 1);
        $string = $chars{rand(0, $charsLength)};
       
        for ($i = 1; $i < $length; $i = strlen($string)) {
            $r = $chars{rand(0, $charsLength)};
            // Make sure the same two characters don't appear next to each other
            if ($r != $string{$i - 1}) {
                $string .=  $r;
            }
        }
        return $prefix . $string;
    }
    /**
     * Helper method for creating a repository.
     *
     * @param string $repository The name of the repository to create
     * @param string $visibility The visibility of the repository to create
     */
    protected function _createRepository($repository, $visibility = 'public')
    {
        if ($visibility == 'public') {
            $visibility = 1;
        } else {
            $visibility = 0;
        }
        $createParams = array(
            'public' => $visibility, 
            'description' => 'Create via an online ZF Testcase', 
            'homepage' => 'http://framework.zend.com'
        );
        $this->_gitHubClient->repos->create($repository, $createParams);
    }
    /**
     * Helper method for deleting a repository.
     *
     * @param string $repository The name of the repository to delete
     */
    protected function _deleteRepository($repository)
    {
        $this->_gitHubClient->repos->delete($repository);
    }
    /**
     * Helper method for creating an issue comment.
     *
     * @param string  $repository
     * @param integer $issueNumber
     * @param string  $comment
     * @return array
     */
    protected function _createIssueComment($repository, $issueNumber, $comment)
    {
        return $this->_gitHubClient->issues->comment(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $repository, 
            $issueNumber,
            $comment
        );
    }
    /**
     * Helper method for creating issues.
     *
     * @param string $repository
     * @param string $title
     * @param string $body
     * @return array
     */
    protected function _createIssue($repository, $title, $body)
    {
        $issueParams = array(
            'title' => $title,
            'body' => $body
        );
        return $this->_gitHubClient->issues->open(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $repository, 
            $issueParams
        );
    }
    /**
     * Helper method for closing issues.
     *     
     * @param string  $repository
     * @param integer $issueNumber
     * @return array     
     */
    protected function _closeIssue($repository, $issueNumber)
    {
        return $this->_gitHubClient->issues->close(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $repository, 
            $issueNumber
        );
    }
    /**
     * Helper method for creating an issue label.
     *
     * @param string $repository
     * @param string $label
     * @param string $issueNumber
     * @return array
     */
    protected function _addLabel($repository, $label, $issueNumber)
    {
        return $this->_gitHubClient->issues->label->add(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $repository, 
            $label, 
            $issueNumber
        );
    }
}