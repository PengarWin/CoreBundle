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
use PengarWin\DoubleEntryBundle\Form\Type\JournalType;
use PengarWin\DoubleEntryBundle\Exception\JournalImbalanceException;

/**
 * JournalController
 *
 * @author Tom Haskins-Vaughan <tom@tomhv.uk>
 * @since  2014-10-09
 */
class JournalController extends Controller
{
    /**
     * post
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  2014-10-15
     *
     * @Template
     * @ParamConverter("journal", class="\PengarWin\CoreBundle\Entity\Journal")
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

    /**
     * edit
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  1.0.0
     *
     * @Template
     * @ParamConverter("journal", class="\PengarWin\CoreBundle\Entity\Journal")
     *
     * @param  Request $request
     * @param  Journal $journal
     */
    public function editAction(Request $request, Journal $journal)
    {
        $form = $this->createForm('journal', $journal);
        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $em = $this->get('doctrine')->getManager();
                $em->persist($form->getData());
                $em->flush();

                return $this->redirect($this->generateUrl(
                    'pengarwin_account_show', array(
                        'path' => $journal
                            ->getPostings()
                            ->first()
                            ->getAccount()
                            ->getPath(),
                    )
                ));
            } catch (JournalImbalanceException $e) {
                exit($e->getMessage());
            }
        }

        return array(
            'journal' => $journal,
            'form' => $form->createView(),
        );
    }
}
