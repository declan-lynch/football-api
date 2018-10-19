<?php

namespace App\Repository;

use App\Entity\League;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RepositoryTest extends WebTestCase
{
    private $orm;
    private $doctrine;

    protected function setUp()
    {
        self::bootKernel();
        $container = self::$container;
        $this->doctrine = $container->get('doctrine');
        $this->orm = $this->doctrine->getManager();
    }

    private function save($object)
    {
        $rval = $this->orm->persist($object);
        $this->orm->flush();
        return $rval;
    }

    protected function fetchLeague($id)
    {
        return $this->fetchObject(League::class, $id);
    }

    protected function fetchTeam($id)
    {
        return $this->fetchObject(Team::class, $id);
    }

    private function fetchObject($class, $id)
    {
        return $this->doctrine->getRepository($class)->find($id);
    }

    /**
     * @expectedException \Doctrine\DBAL\Exception\NotNullConstraintViolationException
     */

    public function testTeam_empty_errors()
    {
        $team = new Team();
        $this->save($team);
    }

    public function testTeam_no_strip_persists()
    {
        $name_str = 'new team';
        $team = new Team();
        $team->setName($name_str);
        $this->save($team);
        $from_db = $this->fetchTeam($team->getId());
        $this->assertEquals($name_str, $from_db->getName());
    }

    /**
     * @expectedException Doctrine\DBAL\Exception\UniqueConstraintViolationException
     */
    public function testTeam_duplicate_name_error()
    {
        $name_str = 'new name2';
        $team1 = new Team();
        $team1->setName($name_str);
        $this->save($team1);

        $team2 = new Team();
        $team2->setName($name_str);
        $this->save($team2);

    }

    /**
     * @expectedException Doctrine\DBAL\Exception\UniqueConstraintViolationException
     */
    public function testTeam_duplicate_strip_error()
    {
        $names = ['new name3', 'new name4'];
        $colour = 'blue';
        $team1 = new Team();
        $team1->setName(array_pop($names));
        $team1->setStrip($colour);
        $this->save($team1);

        $team2 = new Team();
        $team2->setName(array_pop($names));
        $team2->setStrip($colour);
        $this->save($team2);

    }

    /**
     * @expectedException \Doctrine\DBAL\Exception\NotNullConstraintViolationException
     */

    public function testLeague_empty_errors()
    {
        $league = new League();
        $this->save($league);
    }

    public function testLeague_nameonly_persists()
    {
        $name_str = 'new league';
        $league = new League();
        $league->setName($name_str);
        $this->save($league);
        $from_db = $this->fetchLeague($league->getId());
        $this->assertEquals($name_str, $from_db->getName());
    }

    public function testTeam_set_leagueid()
    {
        $team_str = 'new team set league';
        $team = new Team();
        $team->setName($team_str);
        $this->save($team);
        $league_str = 'new league again';
        $league = new League();
        $league->setName($league_str);
        $this->save($league);
        $league_id = $league->getId();
        $team->setLeagueId($league_id);
        $this->save($team);
        $this->assertEquals($league, $team->getLeague());
    }

    public function testTeam_set_leagueid_then_delete_league()
    {
        $team_str = 'new team set league2';
        $team = new Team();
        $team->setName($team_str);
        $this->save($team);
        $league_str = 'new league again2';
        $league = new League();
        $league->setName($league_str);
        $this->save($league);
        $league_id = $league->getId();
        $team->setLeagueId($league_id);
        $this->save($team);
        $this->orm->remove($league);
        $this->orm->flush();
        $this->assertEquals(null, $team->getLeague());
    }
}