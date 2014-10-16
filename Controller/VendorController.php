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
use PengarWin\CoreBundle\Entity\Vendor;

/**
 * VendorController
 *
 * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
 * @since  2014-10-14
 */
class VendorController extends Controller
{
    /**
     * index
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2014-10-14
     *
     * @Template
     *
     * @param  Request $request
     */
    public function indexAction(Request $request)
    {
        $this->get('white_october_breadcrumbs')
            ->addItem('Home', $this->get('router')->generate('_homepage'))
            ->addItem('Vendors', $this->get('router')->generate('pengarwin_vendor'))
        ;

        $em = $this->get('doctrine')->getManager();

        $form = $this->createFormBuilder(new Vendor())
            ->add('name')
            ->add('offsetAccount')
            ->add('save', 'submit', array('label' => 'Create Vendor'))
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($form->getData());
            $em->flush();

            return $this->redirect($this->generateUrl('pengarwin_vendor'));
        }

        $vendors = $em->getRepository('PengarWinCoreBundle:Vendor')
            ->findAll()
        ;

        return array(
            'vendors' => $vendors,
            'form'     => $form->createView(),
        );
    }

    /**
     * show
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2014-10-14
     *
     * @Template
     * @ParamConverter("vendor", class="PengarWinCoreBundle:Vendor")
     *
     * @param  Request $request
     */
    public function showAction(Request $request, Vendor $vendor)
    {
        $this->get('white_october_breadcrumbs')
            ->addItem('Home', $this->get('router')->generate('_homepage'))
            ->addItem('Vendors', $this->get('router')->generate('pengarwin_vendor'))
            ->addItem(
                $vendor->getName(),
                $this->get('router')->generate('pengarwin_vendor_show', array(
                    'slug' => $vendor->getSlug(),
                ))
            )
        ;

        return array('vendor' => $vendor);
    }
}
