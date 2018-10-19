<?php

namespace App\DataFixtures;

use App\Entity\League;
use App\Entity\Team;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {

        $datasets = [];

        $datasets[] = [
            'league' => 'High Flyers',
            'teams' => [
                'Canaries' => 'Yellow',
                'Leadenhallers' => 'Blue',
                'Shardists' => 'Black',
                ]
            ];

        $datasets[] = [
            'league' => 'Mainstream',
            'teams' => [
            'Blue Water' => 'While',
            'Brent Cross' => 'Green',
            'Westfield' => 'Red',
                ]
        ];

        $datasets[] = [
            'league' => 'Bricks and Mortar',
            'teams' => [
            'Gregs' => 'Orange',
            'Aldi' => 'Purple',
            'Snappy Snaps' => 'Grey',
                ]
        ];

        foreach( $datasets as $data){

            $newleague = new League();
            $newleague->setName($data['league']);
            $manager->persist($newleague);

            foreach($data['teams'] as $teamname=>$stripcolour)
            {
                $newteam = new Team();
                $newteam->setName($teamname);
                $newteam->setStrip($stripcolour);
                $newteam->setLeague($newleague);

                $manager->persist($newteam);
            }

        }

        $manager->flush();
    }

}