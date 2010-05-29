<?php
/**
 * @group online
 */
class Zend_Service_GitHub_BlobOnlineTest extends Zend_Service_GitHubOnlineTestcase
{
    public function testShowGetsBlobForGivenShaAndFilepath()
    {
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
        $responseString =  $this->_gitHubClient->blob->show(
            'defunkt', 
            'facebox', 
            '365b84e0fd92c47ecdada91da47f2d67500b8e31'
        );
        $this->assertContains('tree', $responseString);
        $this->assertContains('committer', $responseString);
        $this->assertContains('Merge branch', $responseString);
    }
    public function testAllGetsBlobsForGivenBranch()
    {
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