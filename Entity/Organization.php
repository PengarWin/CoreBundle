<?php

/*
 * This file is part of the Phospr package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phospr\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Criteria;
use Phospr\DoubleEntryBundle\Model\OrganizationInterface;
use Phospr\DoubleEntryBundle\Model\Organization as BaseOrganization;

/**
 * Organization
 *
 * Represents a group of users who share access to a set of accounts
 *
 * @author Tom Haskins-Vaughan <tom@tomhv.uk>
 * @since  0.8.0
 *
 * @ORM\Entity
 * @ORM\Table(name="phospr_organization")
 */
class Organization extends BaseOrganization implements OrganizationInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="organizations")
     */
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="Account", mappedBy="organization", cascade={"persist"})
     */
    protected $accounts;

    /**
     * @ORM\OneToMany(targetEntity="Vendor", mappedBy="organization", cascade={"persist"})
     */
    protected $vendors;

    /**
     * @ORM\OneToOne(targetEntity="Account", cascade={"persist"})
     * @ORM\JoinColumn(name="chart_of_accounts_id", referencedColumnName="id")
     */
    protected $chartOfAccounts;
}
