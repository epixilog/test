<?php

namespace Ens\JobBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JobControllerTest extends WebTestCase
{

    /**
     * @var EntityManager
     */
    private $_em;

    /**
     * @var Kernel
     */
    private $_kernel;

    protected function setUp()
    {
        $this->_kernel = $kernel = static::createKernel();
        $kernel->boot();
        $this->_em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->_em->beginTransaction();
    }

    public function getMostRecentProgrammingJob()
    {
        $query = $this->_em->getRepository('EnsJobBundle:Job')
                            ->createQueryBuilder('j')
                            ->join('j.category', 'c')
                            ->where('c.slug = :slug')
                            ->setParameter('slug', 'programming')
                            ->andWhere('j.expires_at > :date')
                            ->setParameter('date', date('Y-m-d H:i:s'))
                            ->orderBy('j.created_at', 'DESC')
                            ->setMaxResults(1)
                            ->getQuery();

        return $query->getSingleResult();
    }

    public function getExpiredJob(){
        $query = $this->_em->getRepository('EnsJobBundle:Job')
                            ->createQueryBuilder('j')
                            ->where('j.expires_at < :date')
                            ->setParameter('date', date('Y-m-d H:i:s'))
                            ->setMaxResults(1)
                            ->getQuery();

        return $query->getSingleResult();
    }

    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $max_jobs_on_homepage = $this->_kernel->getContainer()->getParameter('max_jobs_on_homepage');
        $job = $this->getMostRecentProgrammingJob();

        $this->assertTrue(
                            $crawler->filter('.category_programming tr')
                                    ->first()
                                    ->filter( sprintf('a[href*="/%d/"]',$job->getId()))
                                    ->count() == 1
                        );

        $this->assertEquals(
                                'Ens\JobBundle\Controller\JobController::indexAction', 
                                $client->getRequest()->attributes->get('_controller')
                        );

        $this->assertTrue($crawler->filter('.jobs td.position:contains("Expired")')->count() == 0);
        $this->assertTrue($crawler->filter('.category_design .more_jobs')->count() == 0);
        $this->assertTrue($crawler->filter('.category_programming .more_jobs')->count() == 1);
        $this->assertTrue($crawler->filter('.category_programming tr')->count() != 0);

        $link = $crawler->selectLink('Web Developer')->first()->link();
        $crawler = $client->click($link);
        $this->assertEquals('Ens\JobBundle\Controller\JobController::showAction', $client->getRequest()->attributes->get('_controller'));
        $this->assertEquals($job->getCompanySlug(), $client->getRequest()->attributes->get('company'));
        $this->assertEquals($job->getLocationSlug(), $client->getRequest()->attributes->get('location'));
        $this->assertEquals($job->getPositionSlug(), $client->getRequest()->attributes->get('position'));
        $this->assertEquals($job->getId(), $client->getRequest()->attributes->get('id'));
    }

    /*
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/ens_job/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /ens_job/");
        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'ens_jobbundle_job[field_name]'  => 'Test',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'ens_jobbundle_job[field_name]'  => 'Foo',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
    }

    */
}
