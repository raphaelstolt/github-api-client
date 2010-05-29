<?php
/**
 * @group offline
 */
class Zend_Service_GitHub_UserOfflineTest extends Zend_Service_GitHubOfflineTestcase
{
    public function testUseOfUserApiPartReturnsAGitHubInstance()
    {
        $assertionMessage = "Use of user API part didn't return "
            . "expected Zend_Service_GitHub instance.";
        $this->assertTrue($this->_gitHubClient->user instanceof Zend_Service_GitHub, 
            $assertionMessage
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnUnauthorizedUpdateUserRaisesExpectedException()
    {
        $_gitHubClient = new Zend_Service_GitHub();
        $userDetails = array(
            'company' => 'Zend', 
            'name' => 'ZF GitHub Client',
            'email' => 'zfgh@framework.zend.com',
            'location' => 'Somewhere',
            'blog' => 'http://framework.zend.com'
        );
        $_gitHubClient->user->show(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $userDetails
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnIvalidEmailInDetailsRaisesExpectedException()
    {
        $userDetails = array(
            'company' => 'Zend', 
            'name' => 'ZF GitHub Client',
            'email' => '4512ä+#',
            'location' => 'Somewhere',
            'blog' => 'http://framework.zend.com'
        );
        $this->_gitHubClient->user->show(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $userDetails
        );
    }
    public function testUserShowWithParamsUpdatesUserDetails()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('user-update')
        );
        
        $userDetails = array(
            'company' => 'Zend', 
            'name' => 'ZF GitHub Client',
            'email' => 'zfgh@framework.zend.com',
            'location' => 'Somewhere',
            'blog' => 'http://framework.zend.com'
        );
        $response = $this->_gitHubClient->user->show(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $userDetails
        );
        foreach (array_keys($userDetails) as $key) {
            $this->assertSame($response['user'][$key], $userDetails[$key]);
        }
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testUpdateUserResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $userDetails = array(
            'company' => 'Zend', 
            'name' => 'ZF GitHub Client',
            'email' => 'zfgh@framework.zend.com',
            'location' => 'Somewhere'
        );
        $this->_gitHubClient->user->show(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $userDetails
        );
    }
    public function testUserShowDeliversExpectedUserDetails()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('user-show')
        );
        
        $expectedDetails = array(
            'company' => 'Zend', 
            'blog' => 'http://framework.zend.com',
            'location' => 'Germany'
        );
        $response = $this->_gitHubClient->user->show(
            TESTS_ZEND_SERVICE_GITHUB_USER
        );
        foreach (array_keys($expectedDetails) as $key) {
            $this->assertSame($response['user'][$key], $expectedDetails[$key]);
        }
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testUseOfInvalidBlogUriInUserUpdateRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('user-update')
        );
        
        $userDetails = array(
            'company' => 'Zend', 
            'name' => 'ZF GitHub Client',
            'email' => 'zfgh@framework.zend.com',
            'blog' => '#ä+ü#ä?'
        );
        $response = $this->_gitHubClient->user->show(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $userDetails
        );
    }
    public function testThatSearchFindsGivenUsername()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('user-search')
        );
        $response = $this->_gitHubClient->user->search(TESTS_ZEND_SERVICE_GITHUB_USER);
        $this->assertTrue(count($response['users']) === 1);
    }
    
    public function testFollowerCountIsGreaterOne()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('user-followers')
        );
        $followers = $this->_gitHubClient->user->followers('raphaelstolt');
        $this->assertTrue(count($followers['users']) > 0);
    }
    public function testFollowingCountIsGreaterOne()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('user-following')
        );
        $following = $this->_gitHubClient->user->following('raphaelstolt');
        $this->assertTrue(count($following['users']) > 0);
    }
    public function testFollowingAsSpecificGitHubUser()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('user-following')
        );
        $following = $this->_gitHubClient->user->following('raphaelstolt');
        $assertionMessage = "GitHub user 'mnaberez' is not being followed as expected.";
        $this->assertTrue(in_array('mnaberez', $following['users']), $assertionMessage);
    }
}