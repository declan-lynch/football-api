<?php

namespace App\Entity;

use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Team extends BaseEntity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $strip;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\League", inversedBy="teams")
     */
    private $league;

    private $tmpleagueid;

    /**
     * @Groups("default")
     */

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @Groups("default")
     */

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @Groups("default")
     */

    public function getStrip(): ?string
    {
        return $this->strip;
    }

    public function setStrip(?string $strip): self
    {
        $this->strip = $strip;

        return $this;
    }

    public function getLeague(): ?League
    {
        return $this->league;
    }

    public function setLeague(?League $league): self
    {
        $this->league = $league;
        $this->tmpleagueid = null;

        return $this;
    }

    /**
     * @Groups("default")
     */

    public function getLeagueId(): ?int
    {
        return $this->league ? $this->league->getId() : null ;
    }

    public function setLeagueId(int $tmpid): self
    {
        $this->tmpleagueid = $tmpid;
        return $this;
    }


    /**
     * @ORM\PreFlush()
     *
     */
    public function checkLeague(PreFlushEventArgs $event)
    {
        if($this->tmpleagueid) {
            $em = $event->getEntityManager();
            $repo = $em->getRepository(League::class);
            if($league = $repo->find($this->tmpleagueid)){
                $this->setLeague($league);
            }
            $this->tmpleagueid = null;
        }
    }

}
