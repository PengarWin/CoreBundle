<?php

/*
 * This file is part of the Phospr package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phospr\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\Criteria;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Phospr\DoubleEntryBundle\Form\Type\SimpleJournalType;
use Phospr\CoreBundle\Entity\Account;

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
            ->addItem('Accounts', $this->get('router')->generate('phospr_account'))
        ;

        $em = $this->get('doctrine')->getManager();
        $ah = $this->get('phospr.account_handler');

        $form = $this->createFormBuilder($ah->createAccount())
            ->add('parent', 'account_selector')
            ->add('name')
            ->add('save', 'submit', array('label' => 'Create Account'))
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $em->persist($form->getData());
                $em->flush();
            } catch (\Doctrine\DBAL\DBALException $e) {
                exit(sprintf(
                    'Account %s already exists',
                    $form->getData()->getSegmentation()
                ));
            }

            return $this->redirect($this->generateUrl('phospr_account'));
        }


        $criteria = new Criteria();
        $criteria
            ->where($criteria->expr()->eq(
                'organization',
                $this->get('phospr.organization_handler')->getOrganization()
            ))
            ->andWhere($criteria->expr()->gt('lvl', 0))
            ->orderBy(array('lft' => Criteria::ASC))
        ;

        $accounts = $em->getRepository('PhosprCoreBundle:Account')
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
     * @ParamConverter("account", class="Phospr\CoreBundle\Entity\Account")
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
                $this->get('router')->generate('phospr_account')
            )
        ;

        $treePath = $this->get('phospr.account_handler')
            ->getTreePath($account)
        ;

        foreach ($treePath as $node) {
            if ($node->getLvl()) {
                $this->get('white_october_breadcrumbs')
                    ->addItem($node->getName(),
                        $this->get('router')->generate('phospr_account_show', array(
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

        $journal = $this->get('phospr.journal_handler')->createJournal();

        $form = $this->createForm(new SimpleJournalType(), $journal);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine')->getManager();

            $amount  = $form->getData()->getCreditAmount();
            $amount -= $form->getData()->getDebitAmount();

            $ph = $this->get('phospr.posting_handler');

            $form->getData()->addPosting($ph->createPosting($account, $amount));
            $form->getData()->addPosting($ph->createPosting(
                $form->getData()->getOffsetAccount(),
                -1*$amount
            ));

            $em->persist($form->getData());
            $em->flush();

            return $this->redirect($this->generateUrl(
                'phospr_account_show', array('path' => $account->getPath())
            ));
        }

        return array(
            'account' => $account,
            'form'    => $form->createView(),
        );
    }
}
