<?php
/**
 * @group offline
 */
class Zend_Service_GitHubOfflineTest extends Zend_Service_GitHubOfflineTestcase
{
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testUseOf40CharacterExceedingLoginRaisesExpectedException()
    {
        $this->_gitHubClient = new Zend_Service_GitHub(
            'fourtyfourtyfourtyfourtyfourtyfourtyfourty', 
            'fooooo'
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testUseOfInvalidLoginRaisesExpectedException()
    {
        $this->_gitHubClient = new Zend_Service_GitHub(
            '', 
            'fooooo'
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testUseOfUnsupportedApiPartRaisesExpectedException()
    {
        $this->_gitHubClient->monster;
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testUseOfNonExistingApiPartMethodRaisesExpectedException()
    {
        $this->_gitHubClient->user->findThatDude('chacon');
    }
}