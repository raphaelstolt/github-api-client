<?php
/**
 * @group online
 */
class Zend_Service_GitHub_ReposOnlineTest extends Zend_Service_GitHubOnlineTestcase
{
    const TEST_REPOS_CREATE_NAME = 'gh_repos';
    const TEST_REPOS_NAME = 'pastebin';
    const TEST_REPOS_OWNER = 'weierophinney';
    
    public function testThatShowAllReposOfASpecificUserReturnsSomething()
    {
        $response = $this->_gitHubClient->repos->show(self::TEST_REPOS_OWNER);
        $this->assertTrue(count($response['repositories']) > 0);
    }
    public function testThatShowFetchesASpecificReposOfASpecificUser()
    {
        $response = $this->_gitHubClient->repos->show(
            self::TEST_REPOS_OWNER, 
            self::TEST_REPOS_NAME
        );
        $this->assertSame($response['repository']['name'], self::TEST_REPOS_NAME);
    }
    public function testThatSearchFindsPhpUnitViaPhpLanguageAndUnitQueryString()
    {
        try {
            $response = $this->_gitHubClient->repos->search(
                'php+unit'
            );
        } catch (Zend_Service_GitHub_Exception $e) {
            $this->markTestSkipped($e->getMessage());
        }
        $reposNames = array();
        foreach ($response['repositories'] as $reposDetails) {
            $reposNames[] = strtolower($reposDetails['name']);
        }
        $this->assertContains('phpunit', $reposNames);
    }
    public function testThatGivenRepositoryIsWatched()
    {
        $response = $this->_gitHubClient->repos->watch(
            self::TEST_REPOS_OWNER, 
            self::TEST_REPOS_NAME
        );
        $this->assertSame($response['repository']['name'], self::TEST_REPOS_NAME);
    }
    public function testThatGivenRepositoryIsUnwatched()
    {
        $response = $this->_gitHubClient->repos->unwatch(
            self::TEST_REPOS_OWNER, 
            self::TEST_REPOS_NAME
        );
        $this->assertSame($response['repository']['name'], self::TEST_REPOS_NAME);
    }
    public function testThatGivenRepositoryIsForked()
    {
        $response = $this->_gitHubClient->repos->fork(
            self::TEST_REPOS_OWNER, 
            self::TEST_REPOS_NAME
        );
        $this->assertSame($response['repository']['owner'], TESTS_ZEND_SERVICE_GITHUB_USER);
        $this->assertSame($response['repository']['name'], self::TEST_REPOS_NAME);
        $this->_deleteRepository(self::TEST_REPOS_NAME);
        // unwatch as a fork also creates a watch on forked repository
        $this->_gitHubClient->repos->unwatch(
            self::TEST_REPOS_OWNER, 
            self::TEST_REPOS_NAME
        );
    }
    public function testThatGivenRepositoryIsCreated()
    {
        $createParams = array(
            'public' => 0, 
            'description' => 'Create via an online ZF Testcase', 
            'homepage' => 'http://framework.zend.com'
        );
        $response = $this->_gitHubClient->repos->create(self::TEST_REPOS_CREATE_NAME, $createParams);
        $this->assertSame($response['repository']['owner'], TESTS_ZEND_SERVICE_GITHUB_USER);
        $this->assertSame($response['repository']['name'], self::TEST_REPOS_CREATE_NAME);
        $expectedGitHubUri = sprintf("http://github.com/%s/%s", 
            TESTS_ZEND_SERVICE_GITHUB_USER,
            self::TEST_REPOS_CREATE_NAME
        ); 
        $this->assertSame($response['repository']['url'], $expectedGitHubUri);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatExpectedExceptionIsRisenWhenCreateRepositoryIsCalledOnAlreadyExistingOne()
    {
        $createParams = array(
            'public' => 0, 
            'description' => 'Create via an online ZF Testcase', 
            'homepage' => 'http://framework.zend.com'
        );
        $this->_gitHubClient->repos->create(self::TEST_REPOS_CREATE_NAME, $createParams);
    }
    public function testThatGivenRepositoryIsDeleted()
    {
        $response = $this->_gitHubClient->repos->delete(self::TEST_REPOS_CREATE_NAME);
        $this->assertSame(strtolower('deleted'), strtolower($response['status']));
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatADeleteOnNonExistentRepositoryRaisesExpectedException()
    {
        $this->_gitHubClient->repos->delete('wohoooooo');
    }
    public function testThatNetworkOfGivenRepositoryIsListed()
    {
        $response = $this->_gitHubClient->repos->network('schacon', 'ruby-git');
        $this->assertSame($response['network'][0]['owner'],'schacon');
        $this->assertSame($response['network'][0]['name'],'ruby-git');
    }
    public function testThatLanguagesOfGivenRepositoryIsListed()
    {
        $response = $this->_gitHubClient->repos->languages('mojombo', 'grit');
        $this->assertTrue(isset($response['languages']));
        $this->assertContains('Ruby', array_keys($response['languages']));
    }
    public function testThatTagsOfGivenRepositoryIsListed()
    {
        $response = $this->_gitHubClient->repos->tags('sebastianbergmann', 'phploc');
        $this->assertTrue(isset($response['tags']));
        $this->assertContains('1.0.0', array_keys($response['tags']));
    }
    public function testThatBranchesOfGivenRepositoryIsListed()
    {
        $response = $this->_gitHubClient->repos->branches('sebastianbergmann', 'phpunit');
        $this->assertTrue(isset($response['branches']));
        $this->assertContains('1.3', array_keys($response['branches']));
    }
}