<?php

/*
 * This file is part of the PengarWin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PengarWin\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use PengarWin\CoreBundle\Entity\Journal;

/**
 * JournalController
 *
 * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
 * @since  2014-10-09
 */
class JournalController extends Controller
{
    /**
     * post
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2014-10-15
     *
     * @Template
     * @ParamConverter("journal", class="PengarWinCoreBundle:Journal")
     *
     * @param  Request $request
     * @param  Journal $journal
     */
    public function postAction(Request $request, Journal $journal)
    {
        $em = $this->get('doctrine')->getManager();

        $account = $em
            ->getRepository('PengarWinCoreBundle:Account')
            ->findOneBy(array('path' => $request->get('path')))
        ;

        $journal->post();

        $em->persist($journal);
        $em->flush();

        return $this->redirect($this->generateUrl(
            'pengarwin_account_show', array('path' => $account->getPath())
        ));
    }
}
