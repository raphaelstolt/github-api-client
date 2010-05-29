<?php
/**
 * @group online
 */
class Zend_Service_GitHub_CommitsOnlineTest extends Zend_Service_GitHubOnlineTestcase
{
    public function testListShowsCommitsOfGivenBranch()
    {
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
    public function testShowGetsTheChangesOfGivenCommit()
    {
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