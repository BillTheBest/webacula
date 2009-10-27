<?php
class StorageControllerTest extends ControllerTestCase
{
    const ZF_pattern = '/Exception:|Warning:|Notice:|Call Stack/'; // Zend Framework

    /**
     * @access protected
     */
    protected function tearDown ()
    {
        $this->resetRequest();
        $this->resetResponse();
        parent::tearDown();
    }


    public function testListAllStorage ()
    {
        print "\n" . __METHOD__ . ' ';
        $this->dispatch('storage/storage');
        $this->assertController('storage');
        $this->assertAction('storage');
        //echo $this->response->outputBody(); // for debug !!!
        $this->assertNotQueryContentRegex('table', self::ZF_pattern); // Zend Framework
        $this->assertResponseCode(200);
        $this->assertQueryContentContains('td', 'storage.file.1');
        $this->assertQueryContentContains('td', 'storage.file.2');
    }


    /**
     * @group use-bconsole
     */
    public function testStorageStatus ()
    {
        print "\n" . __METHOD__ . ' ';
        $this->dispatch('storage/status-id/id/1/name/storage.file.1');
        $this->assertModule('default');
        $this->assertController('storage');
        $this->assertAction('status-id');
        //echo $this->response->outputBody(); // for debug !!!
        $this->assertNotQueryContentRegex('table', self::ZF_pattern); // Zend Framework
        $this->assertResponseCode(200);
        $this->assertQueryContentContains('div', '1000 OK: main.dir');
        $this->assertNotQueryContentRegex('div', '/Error/i');
        // http://by.php.net/manual/en/function.preg-match.php
        $this->assertQueryContentRegex('div', "/Storage1 Version:.*linux/");
    }
}
