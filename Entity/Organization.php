<?php

/*
 * This file is part of the PengarWin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PengarWin\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Criteria;
use PengarWin\DoubleEntryBundle\Model\Organization as BaseOrganization;

/**
 * Organization
 *
 * Represents a group of users who share access to a set of accounts
 *
 * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
 * @since  2014-10-20
 *
 * @ORM\Entity
 * @ORM\Table(name="pengarwin_organization")
 */
class Organization extends BaseOrganization
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="organizations")
     */
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="Account", mappedBy="organization", cascade={"persist"})
     */
    protected $accounts;
}
