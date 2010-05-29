<?php
/**
 * @group offline
 */
class Zend_Service_GitHub_BlobOfflineTest extends Zend_Service_GitHubOfflineTestcase
{
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testShowForGivenShaAndFilepathResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-not-found')
        );
        $this->_gitHubClient->blob->show(
            'defunkt', 
            'facebox', 
            '365b84e0fd92c47ecdada91da47f2d67500b8e31',
            'README.txt'
        );
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testShowForGivenShaResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-not-found')
        );
        $this->_gitHubClient->blob->show(
            'defunkt', 
            'facebox', 
            '365b84e0fd92c47ecdada91da47f2d67500b8e31'
        );
    }
    public function testShowGetsBlobForGivenShaAndFilepath()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('blob-show-path')
        );
        $response =  $this->_gitHubClient->blob->show(
            'defunkt', 
            'facebox', 
            '365b84e0fd92c47ecdada91da47f2d67500b8e31',
            'README.txt'
        );
        $this->assertTrue(isset($response['blob']));
        $this->assertSame('README.txt', $response['blob']['name']);
    }
    public function testShowGetsBlobForGivenSha()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('blob-show')
        );
        $responseString =  $this->_gitHubClient->blob->show(
            'defunkt', 
            'facebox', 
            '365b84e0fd92c47ecdada91da47f2d67500b8e31'
        );
        $this->assertContains('tree', $responseString);
        $this->assertContains('committer', $responseString);
        $this->assertContains('Merge branch', $responseString);
    }
    /**
     * @expectedException Zend_Service_GitHub_Exception
     */
    public function testAllBlobResponseErrorRaisesExpectedException()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('repos-not-found')
        );
        $this->_gitHubClient->blob->all(
            'defunkt', 
            'facebox', 
            'master'
        );
    }
    public function testAllGetsBlobsForGivenBranch()
    {
        $this->_injectHttpClientAdapterTest();
        $this->_httpClientAdapterTest->setResponse(
            $this->_getStoredResponseContent('blob-all')
        );
        $response =  $this->_gitHubClient->blob->all(
            'sebastianbergmann', 
            'phpunit', 
            '3.3'
        );
        $this->assertTrue(isset($response['blobs']));
        $blobFiles = array_keys($response['blobs']);
        $this->assertTrue(count($blobFiles) > 0);
        $this->assertContains('PHPUnit/Util/Skeleton', $blobFiles[0]);
    }
}