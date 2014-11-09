<?php

/*
 * This file is part of the Phospr package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phospr\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phospr\DoubleEntryBundle\Model\Journal as BaseJournal;
use Phospr\DoubleEntryBundle\Model\JournalInterface;

/**
 * Journal
 *
 * @author Tom Haskins-Vaughan <tom@tomhv.uk>
 * @since  0.8.0
 *
 * @ORM\Entity
 * @ORM\Table(name="phospr_journal")
 */
class Journal extends BaseJournal implements JournalInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Organization")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $organization;

    /**
     * @ORM\ManyToOne(targetEntity="Vendor", inversedBy="journals", cascade={"persist"})
     * @ORM\JoinColumn(name="vendor_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $vendor;

    /**
     * @ORM\OneToMany(targetEntity="Posting", mappedBy="journal", cascade={"persist"})
     */
    protected $postings;
}
