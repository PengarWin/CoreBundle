<?php

/*
 * This file is part of the Phospr package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phospr\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phospr\DoubleEntryBundle\Model\Vendor as BaseVendor;
use Phospr\DoubleEntryBundle\Model\VendorInterface;
use JMS\Serializer\Annotation as JMSSerializer;

/**
 * Vendor
 *
 * @author Tom Haskins-Vaughan <tom@tomhv.uk>
 * @since  0.8.0
 *
 * @JMSSerializer\ExclusionPolicy("all")
 *
 * @ORM\Entity
 * @ORM\Table(name="phospr_vendor")
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

    /**
     * @JMSSerializer\VirtualProperty
     */
    public function getLabel()
    {
        return $this->getName();
    }
}
