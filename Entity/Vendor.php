<?php

/*
 * This file is part of the PengarWin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PengarWin\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PengarWin\DoubleEntryBundle\Model\Vendor as BaseVendor;
use PengarWin\DoubleEntryBundle\Model\VendorInterface;

/**
 * Vendor
 *
 * @author Tom Haskins-Vaughan <tom@tomhv.uk>
 * @since  2014-10-13
 *
 * @ORM\Entity
 * @ORM\Table(name="pengarwin_vendor")
 */
class Vendor extends BaseVendor implements VendorInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Journal", mappedBy="vendor", cascade={"persist"})
     */
    protected $journals;

    /**
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="vendors")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $organization;

    /**
     * @ORM\ManyToOne(targetEntity="Account", cascade={"persist"})
     * @ORM\JoinColumn(name="default_offset_account_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $defaultOffsetAccount;
}
