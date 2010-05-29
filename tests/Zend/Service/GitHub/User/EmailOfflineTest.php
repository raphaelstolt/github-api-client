<?php
require_once 'Zend/Service/GitHub/User/Email.php';
require_once 'Zend/Service/GitHubOfflineTestcase.php';

class Zend_Service_GitHub_User_EmailOfflineTest extends Zend_Service_GitHubOfflineTestcase
{
    const TEST_EMAIL_ADDRESS = 'some@email.org';
    const PRIMARY_EMAIL_ADDRESS = 'raphael.stolt@googlemail.com';
    const INVALID_EMAIL_ADDRESS = '#ä+ü';
    
    public function testUseOfKeyApiPartReturnsAGitHubInstance()
    {
        $assertionMessage = "Use of email API part didn't return "
            . "expected Zend_Service_GitHub instance.";
        $this->assertTrue($this->_gitHubClient->user->email instanceof Zend_Service_GitHub, 
            $assertionMessage
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnUnauthorizedAddEmailRaisesExpectedExceptions()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->user->email->add(self::TEST_EMAIL_ADDRESS);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnAddEmailResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->user->email->add(self::TEST_EMAIL_ADDRESS);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnUnauthorizedRemoveEmailRaisesExpectedExceptions()
    {
        $gitHubClient = new Zend_Service_GitHub();
        $gitHubClient->user->email->remove(self::TEST_EMAIL_ADDRESS);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnRemoveEmailResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('auth-error')
        );
        $this->_gitHubClient->user->email->remove(self::TEST_EMAIL_ADDRESS);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testThatAnInvalidEmailRaisesExpectedException()
    {
        $this->_gitHubClient->user->email->add(self::INVALID_EMAIL_ADDRESS);
    }
    public function testThatAnEmailAddressIsAdded()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('email-add')
        );
        $response = $this->_gitHubClient->user->email->add(self::TEST_EMAIL_ADDRESS);
        $this->assertTrue(in_array(self::TEST_EMAIL_ADDRESS, $response['emails']));
    }
    public function testThatAnEmailAddressGetsRemoved()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('email-remove')
        );
        $response = $this->_gitHubClient->user->email->remove(self::TEST_EMAIL_ADDRESS);
        $this->assertFalse(in_array(self::TEST_EMAIL_ADDRESS, $response['emails']));
    }
    public function testThatThePrimaryEmailAddressIsListed()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('email-list')
        );
        $response = $this->_gitHubClient->user->emails();
        $this->assertTrue(in_array(self::PRIMARY_EMAIL_ADDRESS, $response['emails']));
    }
}