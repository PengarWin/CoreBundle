<?php

namespace PengarWin\CoreBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="pengarwin_user")
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
     * @ORM\ManyToMany(targetEntity="Organization", inversedBy="users", cascade={"persist"})
     * @ORM\JoinTable(name="pengarwin_user_organization_ref")
     */
    protected $organizations;

    /**
     * __construct()
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2014-10-20
     */
    public function __construct()
    {
        $this->organizations = new ArrayCollection();

        parent::__construct();
    }

    /**
     * Add organization
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2014-10-20
     *
     * @param  Organization $organization
     *
     * @return Account
     */
    public function addOrganization(Organization $organization)
    {
        $this->organizations->add($organization);

        return $this;
    }

    /**
     * Remove organization
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2014-10-20
     *
     * @param  Organization $organization
     */
    public function removeOrganization(Organization $organization)
    {
        $this->organizations->remove($organization);
    }

    /**
     * Get organizations
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2014-10-20
     *
     * @return ArrayCollection|Organization
     */
    public function getOrganizations()
    {
        return $this->organizations;
    }

    /**
     * Create default Organization
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2014-10-20
     *
     * @ORM\PrePersist
     */
    public function createDefaultOrganization()
    {
        $organization = new Organization();
        $organization->setName($this->getUsername());

        $this->organizations[] = $organization;
    }

    /**
     * Get default Organization
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2014-10-23
     *
     * @return Organization
     */
    public function getDefaultOrganization()
    {
        return $this->getOrganizations()->first();
    }
}
