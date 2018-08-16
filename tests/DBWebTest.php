<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DBWebTest extends WebTestCase
{
    /**
     * helper to acccess EntityManager
     */
    protected $em;
    /**
     * Helper to access test Client
     */
    protected $client;

    /**
     * Before each test we start a new transaction
     * everything done in the test will be canceled ensuring isolation and speed
     */
    protected function setUp()
    {
        parent::setUp();
        $this->client = $this->createClient();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->em->getConnection()->beginTransaction(); // suspend auto-commit
        $this->em->getConnection()->setAutoCommit(false);
    }

    /**
     * After each test, a rollback reset the state of
     * the database
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->rollback();
        $this->em->close();
    }
}
