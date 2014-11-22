<?php

namespace Phospr\CoreBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Phospr\DoubleEntryBundle\Model\UserInterface;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="phospr_user")
 */
class User extends BaseUser implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Organization", inversedBy="users", cascade={"persist"})
     * @ORM\JoinTable(name="phospr_user_organization_ref")
     */
    protected $organizations;

    /**
     * @ORM\Column(type="array", name="my_favourite_account_ids")
     */
    protected $myFavouriteAccountIds = array(4, 16);

    /**
     * __construct()
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.8.0
     */
    public function __construct()
    {
        $this->organizations = new ArrayCollection();

        parent::__construct();
    }

    /**
     * Add organization
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.8.0
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
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.8.0
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
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.8.0
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
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.8.0
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
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.8.0
     *
     * @return Organization
     */
    public function getDefaultOrganization()
    {
        return $this->getOrganizations()->first();
    }

    /**
     * Set myFavouriteAccountIds
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.10.0
     *
     * @param array $myFavouriteAccountIds
     *
     * @return User
     */
    public function setMyFavouriteAccountIds(array $myFavouriteAccountIds)
    {
        $this->myFavouriteAccountIds = $myFavouriteAccountIds;

        return $this;
    }

    /**
     * Get myFavouriteAccountIds
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.10.0
     *
     * @return array
     */
    public function getMyFavouriteAccountIds()
    {
        return $this->myFavouriteAccountIds;
    }
}
