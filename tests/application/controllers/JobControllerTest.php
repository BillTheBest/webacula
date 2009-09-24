<?php

class JobControllerTest extends ControllerTestCase
{
   /**
    * @access protected
    */
	protected function tearDown()
	{
		$this->resetRequest();
        $this->resetResponse();
        parent::tearDown();
	}

 
	public function testJobTerminated()
	{
		print "\n".__CLASS__."\t".__FUNCTION__.' ';	
        $this->dispatch('job/terminated');
		$this->assertModule('default');
        $this->assertController('job');
        $this->assertAction('terminated');
		//echo $this->response->outputBody(); // for debug !!!
		$this->assertResponseCode(200);
		$this->assertNotQueryContentContains('div', 'No Jobs found');
		$this->assertQueryCount('tr', 11);  // 11 строк таблицы
	}
	
	public function testJobRunning()
	{
		print "\n".__CLASS__."\t".__FUNCTION__.' ';	
        $this->dispatch('job/running');
		$this->assertModule('default');
        $this->assertController('job');
        $this->assertAction('running');
		//echo $this->response->outputBody(); // for debug !!!
		$this->assertResponseCode(200);
		$this->assertQueryContentContains('div', 'Information from Director : No Running Jobs found');
		$this->assertNotQueryContentContains('div', 'Information from DB Catalog : No Running Jobs found');
		$this->assertQueryContentContains('td', 'job.name.test.4');
		$this->assertQueryCount('tr', 2);
	}
	
	
	public function testJobNext()
	{	
		print "\n".__CLASS__."\t".__FUNCTION__.' ';
        $this->dispatch('job/next');
		$this->assertModule('default');
        $this->assertController('job');
        $this->assertAction('next');
		//echo $this->response->outputBody(); // for debug !!!
		$this->assertResponseCode(200);
		$this->assertNotQueryContentContains('div', 'No Scheduled Jobs found');
		$this->assertQueryContentContains('td', 'job.name.test.1');
		$this->assertQueryContentContains('td', 'job name test 2');
		$this->assertQueryContentContains('td', 'job-name-test-3');
		$this->assertQueryCount('tr', 4);
	}
	
	public function testJobProblem()
	{	
		print "\n".__CLASS__."\t".__FUNCTION__.' ';
        $this->dispatch('job/problem');
		$this->assertModule('default');
        $this->assertController('job');
        $this->assertAction('problem');
		//echo $this->response->outputBody(); // for debug !!!
		$this->assertResponseCode(200);
		$this->assertNotQueryContentContains('div', 'No Jobs found');
		$this->assertQueryContentContains('td', 'job name test 2');
		$this->assertQueryContentContains('td', 'job.name.test.4');
		$this->assertQueryCount('tr', 3);
	}
	
	public function testRunJobWrong()
	{
		print "\n".__CLASS__."\t".__FUNCTION__.' ';
		$this->getRequest()
        	 ->setParams(array("jobname" => "wrong job name"))
        	 ->setMethod('POST');	
        $this->dispatch('job/run-job');
		$this->assertModule('default');
        $this->assertController('job');
        $this->assertAction('run-job');
		//echo $this->response->outputBody(); // for debug !!!
		$this->assertResponseCode(200);
		$this->assertQueryContentContains('div', '1000 OK: main.dir');
		$this->assertNotQueryContentRegex('div', '/Error/i');
		$this->assertQueryContentContains('div', 'Job "wrong job name" not found');	
	}

	
	public function testRunJob1()
	{
		print "\n".__CLASS__."\t".__FUNCTION__.' ';
		$this->getRequest()
        	 ->setParams(array("jobname" => "job.name.test.1"))
        	 ->setMethod('POST');
        $this->dispatch('job/run-job');
		$this->assertModule('default');
        $this->assertController('job');
        $this->assertAction('run-job');
        sleep(5); // подождать пока выполнится
		//echo $this->response->outputBody();// for debug !!!
		$this->assertResponseCode(200);
		$this->assertQueryContentContains('div', '1000 OK: main.dir');
		$this->assertNotQueryContentRegex('div', '/Error/i');
		// http://by.php.net/manual/en/function.preg-match.php
		$pattern = '/Increme.*job.name.test.1.*is running|Increme.*job.name.test.1.*has terminated|12  Incr.*0.*0.*OK.*job.name.test.1/';
		$this->assertQueryContentRegex('div', $pattern);		
	}
	
	public function testRunJob2()
	{
		print "\n".__CLASS__."\t".__FUNCTION__.' ';
		$this->getRequest()
        	 ->setParams(array("jobname" => "job name test 2"))
        	 ->setMethod('POST');
        $this->dispatch('job/run-job');
		$this->assertModule('default');
        $this->assertController('job');
        $this->assertAction('run-job');
        sleep(5); // подождать пока выполнится
		//echo $this->response->outputBody();// for debug !!!
		$this->assertResponseCode(200);
		$this->assertQueryContentContains('div', '1000 OK: main.dir');
		$this->assertNotQueryContentRegex('div', '/Error/i');
		// http://by.php.net/manual/en/function.preg-match.php
		$pattern = '/Differe.*job_name_test_2.*is running|Differe.*job_name_test_2.*has terminated|13  Diff.*3.*4.115 K.*OK.*job_name_test_2/';
		$this->assertQueryContentRegex('div', $pattern);		
	}

		
	public function testJobFindByIdWrong()
	{
		print "\n".__CLASS__."\t".__FUNCTION__.' ';
		$this->getRequest()
        	 ->setParams(array("jobid" => "1111"))
        	 ->setMethod('POST');	
        $this->dispatch('job/find-job-id');
		$this->assertModule('default');
        $this->assertController('job');
        $this->assertAction('find-job-id');
		$this->assertResponseCode(200);
		$this->assertQueryContentContains('div', 'No Jobs found');		
	}

	public function testJobFindById()
	{
		print "\n".__CLASS__."\t".__FUNCTION__.' ';
		$this->getRequest()
        	 ->setParams(array("jobid" => "4"))
        	 ->setMethod('POST');	
        $this->dispatch('job/find-job-id');
		$this->assertModule('default');
        $this->assertController('job');
        $this->assertAction('find-job-id');
		//echo $this->response->outputBody(); // for debug !!!
		$this->assertResponseCode(200);
		$this->assertNotQueryContentContains('div', 'No Jobs found');
		$this->assertQueryContentContains('td', 'job.name.test.1');
		$this->assertQueryCount('tr', 2);		
	}
	
	public function testJobFindByFilters()
	{
		print "\n".__CLASS__."\t".__FUNCTION__.' ';
		$this->request->setPost(array(
				"jlevel" => "D", 
				"date_begin" => date('Y-m-d', time()-86400),
				"time_begin" => date('H:i:s', time()-86400),
				"date_end"   => date('Y-m-d', time()),
				"time_end"   => date('H:i:s', time())
		));
		$this->request->setMethod('POST');	
        $this->dispatch('job/find-filters');
		$this->assertModule('default');
        $this->assertController('job');
        $this->assertAction('find-filters');
		//echo $this->response->outputBody(); exit; // for debug !!!
		$this->assertResponseCode(200);
		$this->assertNotQueryContentContains('div', 'No Jobs found');
		$this->assertQueryContentContains('td', 'job name test 2');
		$this->assertQueryContentContains('td', 'job.name.test.1');
		$this->assertQueryCount('tr', 4);		
	}
	
	
}
