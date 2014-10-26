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

/**
 * AccountController
 *
 * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
 * @since  2014-10-09
 */
class AccountController extends Controller
{
    /**
     * index
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2014-10-09
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

        $accounts = $em->getRepository('PengarWinCoreBundle:Account')
            ->findAll()
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
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2014-10-11
     *
     * @Template
     * @ParamConverter("account", class="PengarWinCoreBundle:Account")
     *
     * @param  Request $request
     */
    public function showAction(Request $request, Account $account)
    {
        $journal = new Journal();
        $journal->setDate(new \DateTime());

        $form = $this->createFormBuilder($journal)
            ->add('date', 'date', array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'attr' => array(
                    'size' => 5,
                )
            ))
            ->add('proposedVendorName')
            ->add('description')
            ->add('proposedOffsetAccountSegmentation')
            ->add('creditAmount', 'number', array(
                'attr' => array(
                    'size' => 2,
                )
            ))
            ->add('debitAmount', 'number', array(
                'attr' => array(
                    'size' => 2,
                )
            ))
            ->add('save', 'submit', array('label' => 'Create'))
            ->getForm()
        ;

        $segments = array($account->getPath() =>$account->getName());

        $_account = $account;

        while ($_account = $_account->getParent()) {
            if ($_account->getLvl()) {
                $segments[$_account->getPath()] = $_account->getName();
            }
        }

        $segments = array_reverse($segments);

        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem(
                'Home',
                $this->get('router')->generate('_homepage')
            )
            ->addItem(
                'Accounts',
                $this->get('router')->generate('pengarwin_account')
            )
        ;

        foreach ($segments as $path => $label) {
            $breadcrumbs->addItem(
                $label,
                $this->get('router')->generate('pengarwin_account_show', array(
                    'path' => $path
                ))
            );
        }

        $calculatedBalance = 0;

        foreach ($account->getPostings() as $posting) {
            $calculatedBalance += $posting->getAmount();

            $posting->setCalculatedBalance($calculatedBalance);
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine')->getManager();

            $amount  = $form->getData()->getCreditAmount();
            $amount -= $form->getData()->getDebitAmount();

            $vendor = $em->getRepository('PengarWinCoreBundle:Vendor')
                ->findOneBy(array(
                    'name' => $form->getData()->getProposedVendorName(),
                ))
            ;

            $chart = $em->getRepository('PengarWinCoreBundle:Account')
                ->findOneBy(array('lvl' => 0))
            ;

            $offsetAccount = $this->get('pengarwin.account_handler')
                ->getAccountFromSegmentation(
                    $chart,
                    $form->getData()
                        ->getProposedOffsetAccountSegmentation()
                )
            ;

            if (!$offsetAccount->getId()) {
                $em->persist($chart);
            }

            $form->getData()->setOffsetAccount($offsetAccount);

            if (!$vendor) {
                $vendor = new Vendor();
                $vendor->setName($form->getData()->getProposedVendorName());
                $vendor->setDefaultOffsetAccount($form->getData()->getOffsetAccount());
            }

            $form->getData()->setVendor($vendor);

            $posting = new Posting();
            $posting->setAccount($account);
            $posting->setAmount($amount);

            $offsetPosting = new Posting();
            $offsetPosting->setAccount($form->getData()->getOffsetAccount());
            $offsetPosting->setAmount(-1*$amount);

            $form->getData()->addPosting($posting);
            $form->getData()->addPosting($offsetPosting);

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
