<?php

/*
 * This file is part of the PengarWin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PengarWin\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PengarWin\DoubleEntryBundle\Model\Journal as BaseJournal;
use PengarWin\DoubleEntryBundle\Model\JournalInterface;

/**
 * Journal
 *
 * @author Tom Haskins-Vaughan <tom@tomhv.uk>
 * @since  2014-10-11
 *
 * @ORM\Entity
 * @ORM\Table(name="pengarwin_journal")
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
