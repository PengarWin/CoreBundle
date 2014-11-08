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
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use PengarWin\CoreBundle\Entity\Account;
use PengarWin\CoreBundle\Entity\Journal;
use PengarWin\CoreBundle\Entity\Posting;
use PengarWin\CoreBundle\Entity\Vendor;
use PengarWin\DoubleEntryBundle\Form\Type\SimpleJournalType;

/**
 * AccountController
 *
 * @author Tom Haskins-Vaughan <tom@tomhv.uk>
 * @since  0.8.0
 */
class AccountController extends Controller
{
    /**
     * index
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.8.0
     *
     * @Template
     *
     * @param  Request $request
     */
    public function indexAction(Request $request)
    {
        $this->get('white_october_breadcrumbs')
            ->addItem('Home', $this->get('router')->generate('_homepage'))
            ->addItem('Accounts', $this->get('router')->generate('pengarwin_account'))
        ;

        $em = $this->get('doctrine')->getManager();

        $form = $this->createFormBuilder(new Account())
            ->add('parent')
            ->add('name')
            ->add('save', 'submit', array('label' => 'Create Account'))
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($form->getData());
            $em->flush();

            return $this->redirect($this->generateUrl('pengarwin_account'));
        }


        $criteria = new \Doctrine\Common\Collections\Criteria();
        $criteria
            ->where($criteria->expr()->eq(
                'organization',
                $this->get('pengarwin.organization_handler')->getOrganization()
            ))
            ->andWhere($criteria->expr()->gt('lvl', 0))
        ;

        $accounts = $em->getRepository('PengarWinCoreBundle:Account')
            ->matching($criteria)
        ;

        if ('json' == $request->getRequestFormat()) {
            $accountsArray = array();

            foreach ($accounts as $account) {
                if (1 < $account->getLvl()) {
                    $vendorsArray[] = array(
                      'label'         => $account->getSegmentation(),
                      'value'         => $account->getSegmentation(),
                    );
                }
            }

            $response = new Response(json_encode($vendorsArray, JSON_PRETTY_PRINT));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        return array(
            'accounts' => $accounts,
            'form'     => $form->createView(),
        );
    }

    /**
     * show
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.8.0
     *
     * @Template
     * @ParamConverter("account", class="PengarWin\CoreBundle\Entity\Account")
     *
     * @param  Request $request
     */
    public function showAction(Request $request, Account $account)
    {
        $this->get('white_october_breadcrumbs')
            ->addItem('Home',
                $this->get('router')->generate('_homepage')
            )
            ->addItem('Accounts',
                $this->get('router')->generate('pengarwin_account')
            )
        ;

        $treePath = $this->get('pengarwin.account_handler')
            ->getTreePath($account)
        ;

        foreach ($treePath as $node) {
            if ($node->getLvl()) {
                $this->get('white_october_breadcrumbs')
                    ->addItem($node->getName(),
                        $this->get('router')->generate('pengarwin_account_show', array(
                            'path' => $node->getPath()
                    )))
                ;
            }
        }

        $calculatedBalance = 0;

        foreach ($account->getPostings() as $posting) {
            $calculatedBalance += $posting->getAmount();

            $posting->setCalculatedBalance($calculatedBalance);
        }

        $journal = $this->get('pengarwin.journal_handler')->createJournal();

        $form = $this->createForm(new SimpleJournalType(), $journal);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine')->getManager();

            $amount  = $form->getData()->getCreditAmount();
            $amount -= $form->getData()->getDebitAmount();

            $form->getData()->addPosting(new Posting($account, $amount));
            $form->getData()->addPosting(new Posting(
                $form->getData()->getOffsetAccount(),
                -1*$amount
            ));

            $em->persist($form->getData());
            $em->flush();

            return $this->redirect($this->generateUrl(
                'pengarwin_account_show', array('path' => $account->getPath())
            ));
        }

        return array(
            'account' => $account,
            'form'    => $form->createView(),
        );
    }
}
