<?php

namespace App\Tests\Entity;

use App\Entity\League;
use App\Entity\Team;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    public function testTeamLoads()
    {
        $team = new Team();
        $this->assertInstanceOf(Team::class, $team);
    }

    public function testLeagueLoads()
    {
        $league = new League();
        $this->assertInstanceOf(League::class, $league);
    }

    public function testTeam_create_without_league()
    {
        $team = new Team();
        $team->setName('');
        $this->assertEquals('', $team->getName());
    }

    /**
     * @expectedException \TypeError
     */
    public function testTeam_setLeague_as_string_typeerror()
    {
        $team = new Team();
        $team->setLeague('new league');
    }

    /**
     * @expectedException \TypeError
     */
    public function testTeam_setLeague_as_bool_typeerror()
    {
        $team = new Team();
        $team->setLeague(true);
    }

    /**
     * @expectedException \TypeError
     */
    public function testLeague_setTeam_as_bool_typeerror()
    {
        $league = new League();
        $league->addTeam(true);
    }


}