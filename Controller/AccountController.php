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
use PengarWin\CoreBundle\Entity\Account;

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
        return array(
            'account' => $account,
        );
    }
}
