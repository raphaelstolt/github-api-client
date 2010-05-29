<?php
/**
 * @group online
 */
class Zend_Service_GitHub_UserOnlineTest extends Zend_Service_GitHubOnlineTestcase
{
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testCallOfUserUpdateWithInvalidBlogUriRaisesExpectedException()
    {
        $this->_gitHubClient->user->show('raphaelstolt', array('blog' => 'domdum'));
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testCallOfUserUpdateWithInvalidEmailRaisesExpectedException()
    {
        $this->_gitHubClient->user->show('raphaelstolt', array('email' => 'pub2178+#+'));
    }
    public function testUserShowWithParamsUpdatesUserDetails()
    {
        $userDetails = array(
            'company' => 'Zend', 
            'name' => 'ZF GitHub Client',
            'email' => 'zfgh@framework.zend.com',
            'location' => 'Somewhere'
        );
        $response = $this->_gitHubClient->user->show(
            TESTS_ZEND_SERVICE_GITHUB_USER, 
            $userDetails
        );
        foreach (array_keys($userDetails) as $key) {
            $this->assertSame($response['user'][$key], $userDetails[$key]);
        }
    }
    public function testThatSearchFindsGivenUsername()
    {
        try {
            $response = $this->_gitHubClient->user->search(TESTS_ZEND_SERVICE_GITHUB_USER);
        } catch (Zend_Service_GitHub_Exception $e) {
            $this->markTestSkipped($e->getMessage());
        }
        $this->assertTrue(count($response['users']) === 1);
    }
    
    public function testThatAuthorizedShowGivesMoreDetails()
    {
        $response = $this->_gitHubClient->user->show(TESTS_ZEND_SERVICE_GITHUB_USER);
        $this->assertTrue(in_array('plan', array_keys($response['user'])));
    }
    public function testFollowerCountIsGreaterOne()
    {
        $followers = $this->_gitHubClient->user->followers('raphaelstolt');
        $this->assertTrue(count($followers['users']) > 0);
    }
    public function testFollowingCountIsGreaterOne()
    {
        $following = $this->_gitHubClient->user->following('raphaelstolt');
        $this->assertTrue(count($following['users']) > 0);
    }
}