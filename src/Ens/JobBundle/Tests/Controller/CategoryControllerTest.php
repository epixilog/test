<?php

namespace Ens\JobBundle\Tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
 
class CategoryControllerTest extends WebTestCase
{

	/**
     * @var EntityManager
     */
    private $_em;

    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->_em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->_em->beginTransaction();
    }
  	
  	public function testShow()
  	{
	    $client = static::createClient();
	    $categories = $this->_em->getRepository('EnsJobBundle:Category')->getallCategories();
	 
	 	foreach($categories as $category){
	 		$crawler = $client->request('GET', '/category/'.$category->getSlug());
	   		$this->assertEquals('Ens\JobBundle\Controller\CategoryController::showAction', 
	    					$client->getRequest()->attributes->get('_controller')
	    				);
	 	}
	    $this->assertTrue(200 === $client->getResponse()->getStatusCode());
  	}

  	
}
