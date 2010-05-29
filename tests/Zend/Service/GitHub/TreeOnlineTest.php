<?php
/**
 * @group online
 */
class Zend_Service_GitHub_TreeOnlineTest extends Zend_Service_GitHubOnlineTestcase
{
    public function testShowGetsBlobTreeForGivenSha()
    {
        $response =  $this->_gitHubClient->tree->show(
            'defunkt', 
            'facebox', 
            'a47803c9ba26213ff194f042ab686a7749b17476'
        );
        $this->assertTrue(isset($response['tree']));
        $this->assertSame('README.txt', $response['tree'][1]['name']);
    }
}