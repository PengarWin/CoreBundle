<?php

namespace PengarWin\CoreBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Team", inversedBy="users", cascade={"persist"})
     * @ORM\JoinTable(name="user_team_ref")
     */
    protected $teams;

    /**
     * __construct()
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2014-10-20
     */
    public function __construct()
    {
        $this->teams = new ArrayCollection();

        parent::__construct();
    }

    /**
     * Add team
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2014-10-20
     *
     * @param  Team $team
     *
     * @return Account
     */
    public function addTeam(Team $team)
    {
        $this->teams->add($team);

        return $this;
    }

    /**
     * Remove team
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2014-10-20
     *
     * @param  Team $team
     */
    public function removeTeam(Team $team)
    {
        $this->teams->remove($team);
    }

    /**
     * Get teams
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2014-10-20
     *
     * @return ArrayCollection|Team
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * Create default Team
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2014-10-20
     *
     * @ORM\PrePersist
     */
    public function createDefaultTeam()
    {
        $team = new Team();
        $team->setName($this->getUsername());

        $this->teams[] = $team;
    }
}
