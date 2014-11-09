<?php

/*
 * This file is part of the Phospr package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phospr\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phospr\DoubleEntryBundle\Model\Posting as BasePosting;
use Phospr\DoubleEntryBundle\Model\PostingInterface;

/**
 * Posting
 *
 * @author Tom Haskins-Vaughan <tom@tomhv.uk>
 * @since  0.8.0
 *
 * @ORM\Entity
 * @ORM\Table(name="phospr_posting")
 * @ORM\HasLifecycleCallbacks()
 */
class Posting extends BasePosting implements PostingInterface
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
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="postings", cascade={"persist"})
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $account;

    /**
     * @ORM\ManyToOne(targetEntity="Journal", inversedBy="postings")
     * @ORM\JoinColumn(name="journal_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $journal;
}
