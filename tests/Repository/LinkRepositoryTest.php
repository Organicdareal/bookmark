<?php
use App\Entity\Link;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Created by PhpStorm.
 * User: ggicquel
 * Date: 16/08/2018
 * Time: 02:11
 */
class LinkRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->entityManager->getConnection()->beginTransaction(); // suspend auto-commit
        $this->entityManager->getConnection()->setAutoCommit(false);
    }

    public function testfindAllPaginated()
    {
        $this->markAsRisky("depends on DB");
        $bookmarks = $this->entityManager
            ->getRepository(Link::class)
            ->findAllPaginated(1)
        ;

        $this->assertCount(5, $bookmarks->getCurrentPageResults());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}
