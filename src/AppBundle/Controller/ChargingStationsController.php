<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Pump;
use AppBundle\Entity\Station;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class ChargingStationsController extends Controller
{
    /**
     * @Route("/stations")
     */
    public function indexAction()
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->get('doctrine')->getEntityManager();



        $station = new Station();
        $name = 'station-' . time();
        $station->setName($name);

        $pump1 = new Pump();
        $pump1->setName(time());
        $pump1->setType('A');
        $pump1->setStation($station);

        $pump2 = new Pump();
        $pump2->setName(time());
        $pump2->setType('B');
        $pump2->setStation($station);



        $entityManager->persist($station);
        $entityManager->persist($pump1);
        $entityManager->persist($pump2);

        $entityManager->flush();

        return new Response("Just created station -> $name");
    }

    /**
     * @Route("/station/{id}")
     *
     * @param $id
     *
     * @return Response
     */
    public function stationAction($id)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->get('doctrine')->getEntityManager();
        $stationRepository = $entityManager->getRepository(Station::class);

        /** @var Station $station */
        $station = $stationRepository->find($id);

        if ($station === null) {
            return new Response('NOT FOUND!');
        }

        $data = [];

        // Add to the array the station data
        $data['id'] = $station->getId();
        $data['name'] = $station->getName();

        $data['pumps'] = [];
        $pumps = $station->getPumps();

        /** @var Pump $pump */
        foreach ($pumps as $pump) {
            $pumpData = [];

            $pumpData['id'] = $pump->getId();
            $pumpData['name'] = $pump->getName();
            $pumpData['type'] = $pump->getType();

            $data['pumps'][] = $pumpData;
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/stations-join")
     */
    public function joinAction()
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->get('doctrine')->getEntityManager();
        $connection = $entityManager->getConnection();
        $statement = $connection->prepare("SELECT * FROM station st JOIN pump pm ON pm.station_id = st.id WHERE st.id= :id");
        $statement->bindValue('id', 7);
        $statement->execute();
        $results = $statement->fetchAll();

        dump($results);

        return new JsonResponse($results);
    }

}
