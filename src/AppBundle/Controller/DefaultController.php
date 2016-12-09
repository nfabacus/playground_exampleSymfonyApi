<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Entity\Chapter;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->get('doctrine')->getEntityManager();

        // Creating an entity
        $book = new Book();
        $book->setName('Harry Potter');

        // Storing it
        $entityManager->persist($book);
        $entityManager->flush();

        // Retrieving it
        $bookRepository = $entityManager->getRepository(Book::class);
        $book = $bookRepository->find(6);//the book id

        dump($book);

        return new JsonResponse('');
    }

    // This is a suggestion

    //private function persistsAndFlush($entity)
    //{
    //    /** @var EntityManager $entityManager */
    //    $entityManager = $this->get('doctrine')->getEntityManager();
    //
    //    $entityManager->persist($entity);
    //    $entityManager->flush();
    //}

    /**
     * @Route("/all")
     */
    public function findAllAction(Request $request)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->get('doctrine')->getEntityManager();

        $bookRepository = $entityManager->getRepository(Book::class);

        // Easiest case for getting all the entities of one class
        $books = $bookRepository->findAll();

        dump($books);


        return new JsonResponse('Hello world');
    }

    /**
     *
     * This is querying using the querybuilder
     *
     * this returns Entity OBJETCS (which is cool!) in this case from the Book class
     *
     * @Route("/query")
     */
    public function findByQueryAction(Request $request)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->get('doctrine')->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        /*
         * SELECT * FROM book WHERE id = 7 ORDER BY id ASC;
         */
        $queryBuilder
            ->select('b')
            ->from(Book::class, 'b')
            ->where('b.id > :id')
            ->orderBy('b.id', 'ASC');

        $query = $queryBuilder->getQuery();

        $query->setParameter('id', 7);

        $result = $query->getResult();

        dump($result);

        return new JsonResponse('Hello world');
    }

    /**
     * This way allows you to use good old plain SQL
     *
     * but it returns arrays of 'plain' data (not objects)
     *
     * @Route("/nativeQuery")
     */
    public function findByNativeQueryAction(Request $request)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->get('doctrine')->getEntityManager();

        $sql = "SELECT * FROM book WHERE id > 7 ORDER BY id ASC";

        // 1st Create a "prepared statement"
        $statement = $entityManager->getConnection()->prepare($sql);

        // 2nd you execute it
        $statement->execute();

        // 3rd fetch the result
        $result = $statement->fetchAll();

        //dump($result);

        return new JsonResponse($result);
    }


    /**
     * Final example mixing books and chapters
     *
     * @Route("/chapters")
     */
    public function bookWithChaptersAction()
    {
        // Setup
        $book = new Book();

        $book->setName('Harry Potter');

        $chapter1 = new Chapter();
        $chapter1->setName('first');

        $chapter2 = new Chapter();
        $chapter2->setName('second');

        $chapters = [$chapter1, $chapter2];
        $book->setChapters($chapters);

        /** @var EntityManager $entityManager */
        $entityManager = $this->get('doctrine')->getEntityManager();


        // We have to persists all the entities (book and chapters)
        $entityManager->persist($book);
        $entityManager->persist($chapter1);
        $entityManager->persist($chapter2);
        $entityManager->flush();

        return new JsonResponse('');
    }

}
