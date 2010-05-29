<?php
/**
 * @group offline
 */
class Zend_Service_GitHub_TreeOfflineTest extends Zend_Service_GitHubOfflineTestcase
{
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testTreeForGivenShaResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-not-found')
        );
        $this->_gitHubClient->tree->show(
            'defunkt', 
            'facebox', 
            'a47803c9ba26213ff194f042ab686a7749b17476'
        );
    }
    public function testShowGetsBlobTreeForGivenSha()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('tree-show')
        );
        $response =  $this->_gitHubClient->tree->show(
            'defunkt', 
            'facebox', 
            'a47803c9ba26213ff194f042ab686a7749b17476'
        );
        $this->assertTrue(isset($response['tree']));
        $this->assertSame('README.txt', $response['tree'][1]['name']);
    }
}