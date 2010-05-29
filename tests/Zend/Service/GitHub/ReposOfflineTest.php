<?php
/**
 * @group offline
 */
class Zend_Service_GitHub_ReposOfflineTest extends Zend_Service_GitHubOfflineTestcase
{
    const TEST_REPOS_CREATE_NAME = 'gh_repos';
    const TEST_REPOS_NAME = 'pastebin';
    const TEST_REPOS_OWNER = 'weierophinney';
    
    public function testUseOfReposApiPartReturnsAGitHubInstance()
    {
        $assertionMessage = "Use of repos API part didn't return "
            . "expected Zend_Service_GitHub instance.";
        $this->assertTrue($this->_gitHubClient->repos instanceof Zend_Service_GitHub, 
            $assertionMessage
        );
    }
    public function testThatShowAllReposOfASpecificUserReturnsSomething()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-show-all')
        );
        $response = $this->_gitHubClient->repos->show(self::TEST_REPOS_NAME);
        $this->assertTrue(count($response['repositories']) > 0);
    }
    public function testThatShowFetchesASpecificReposOfASpecificUser()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-show-pastebin')
        );
        $response = $this->_gitHubClient->repos->show(
            self::TEST_REPOS_OWNER, 
            self::TEST_REPOS_NAME
        );
        $this->assertSame($response['repository']['name'], self::TEST_REPOS_NAME);
    }
    public function testThatSearchFindsPhpUnitViaPhpLanguageAndUnitQueryString()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-search-phpunit')
        );
        $response = $this->_gitHubClient->repos->search(
            'php+unit'
        );
        $reposNames = array();
        foreach ($response['repositories'] as $reposDetails) {
            $reposNames[] = strtolower($reposDetails['name']);
        }
        $this->assertContains('phpunit', $reposNames);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatASearchResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->repos->search('php+unit');
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnUnauthorizedWatchRepositoryRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->repos->watch(
            self::TEST_REPOS_OWNER, 
            self::TEST_REPOS_NAME
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnWatchRepositoryResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->repos->watch(
            self::TEST_REPOS_OWNER, 
            self::TEST_REPOS_NAME
        );
    }
    public function testThatGivenRepositoryIsWatched()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-watch-pastebin')
        );
        $response = $this->_gitHubClient->repos->watch(
            self::TEST_REPOS_OWNER, 
            self::TEST_REPOS_NAME
        );
        $this->assertSame($response['repository']['name'], self::TEST_REPOS_NAME);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnUnauthorizedUnwatchRepositoryRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->repos->unwatch(
            self::TEST_REPOS_OWNER, 
            self::TEST_REPOS_NAME
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnUnwatchRepositoryResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->repos->unwatch(
            self::TEST_REPOS_OWNER, 
            self::TEST_REPOS_NAME
        );
    }
    public function testThatGivenRepositoryIsUnwatched()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-unwatch-pastebin')
        );
        $response = $this->_gitHubClient->repos->unwatch(
            self::TEST_REPOS_OWNER, 
            self::TEST_REPOS_NAME
        );
        $this->assertSame($response['repository']['name'], self::TEST_REPOS_NAME);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnUnauthorizedForkRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->repos->fork(
            self::TEST_REPOS_OWNER, 
            self::TEST_REPOS_NAME
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAForkResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->repos->fork(
            self::TEST_REPOS_OWNER, 
            self::TEST_REPOS_NAME
        );
    }
    public function testThatGivenRepositoryIsForked()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-fork-pastebin')
        );
        $response = $this->_gitHubClient->repos->fork(
            self::TEST_REPOS_OWNER, 
            self::TEST_REPOS_NAME
        );
        $this->assertSame($response['repository']['owner'], TESTS_ZEND_SERVICE_GITHUB_USER);
        $this->assertSame($response['repository']['name'], self::TEST_REPOS_NAME);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnUnauthorizedCreateRepositoryRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->repos->create(self::TEST_REPOS_CREATE_NAME);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatACreateRepositoryResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->repos->create(self::TEST_REPOS_CREATE_NAME);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnInvalidPublicValueRaisesExpectedException()
    {
        $createParams = array('public' => 20);
        $this->_gitHubClient->repos->create(self::TEST_REPOS_CREATE_NAME, $createParams);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnInvalidHomepageValueRaisesExpectedException()
    {
        $createParams = array('homepage' => 'ä#+ü#ä');
        $this->_gitHubClient->repos->create(self::TEST_REPOS_CREATE_NAME, $createParams);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatExpectedExceptionIsRisenWhenCreateRepositoryIsCalledOnAlreadyExistingOne()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-create-error')
        );
        $createParams = array(
            'public' => 0, 
            'description' => 'Create via an online ZF Testcase', 
            'homepage' => 'http://framework.zend.com'
        );
        $this->_gitHubClient->repos->create(self::TEST_REPOS_CREATE_NAME, $createParams);
    }
    
    public function testThatGivenRepositoryIsCreated()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-create')
        );
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
    public function testThatAnUnauthorizedDeleteRepositoryRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->repos->delete(self::TEST_REPOS_CREATE_NAME);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatADeleteResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->repos->delete(self::TEST_REPOS_CREATE_NAME);
    }
    public function testThatGivenRepositoryIsDeleted()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-delete')
        );
        $response = $this->_gitHubClient->repos->delete(self::TEST_REPOS_CREATE_NAME);
        $this->assertSame(strtolower('deleted'), strtolower($response['status']));
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatADeleteRepositoryResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->repos->delete(self::TEST_REPOS_CREATE_NAME);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnUnauthorizedGetRepositoryKeysRaisesExpectedException()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->repos->keys(self::TEST_REPOS_CREATE_NAME);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAGetRepositoryKeysResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->repos->keys(self::TEST_REPOS_CREATE_NAME);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatNetworkResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-network-not-found')
        );
        $this->_gitHubClient->repos->network('shacon', 'ruby-git');
    }
    public function testThatNetworkOfGivenRepositoryIsListed()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-network')
        );
        $response = $this->_gitHubClient->repos->network('schacon', 'ruby-git');
        $this->assertSame($response['network'][0]['owner'],'schacon');
        $this->assertSame($response['network'][0]['name'],'ruby-git');
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatLanguagesResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-network-not-found')
        );
        $this->_gitHubClient->repos->languages('mojombo', 'agrit');
    }
    public function testThatLanguagesOfGivenRepositoryIsListed()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-languages')
        );
        $response = $this->_gitHubClient->repos->languages('mojombo', 'grit');
        $this->assertTrue(isset($response['languages']));
        $this->assertContains('Ruby', array_keys($response['languages']));
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatTagsResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-network-not-found')
        );
        $this->_gitHubClient->repos->tags('sebastianbergmann', 'phploca');
    }
    public function testThatTagsOfGivenRepositoryIsListed()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-tags')
        );
        $response = $this->_gitHubClient->repos->tags('sebastianbergmann', 'phploc');
        $this->assertTrue(isset($response['tags']));
        $this->assertContains('1.0.0', array_keys($response['tags']));
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatBranchesResponseErrorRaisesExpectedExceptiond()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-network-not-found')
        );
        $this->_gitHubClient->repos->branches('sebastianbergmann', 'phpunita');
    }
    public function testThatBranchesOfGivenRepositoryIsListed()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-branches')
        );
        $response = $this->_gitHubClient->repos->branches('sebastianbergmann', 'phpunit');
        $this->assertTrue(isset($response['branches']));
        $this->assertContains('1.3', array_keys($response['branches']));
    }
}