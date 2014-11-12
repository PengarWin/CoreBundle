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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Phospr\CoreBundle\Entity\Vendor;
use Phospr\DoubleEntryBundle\Form\Type\VendorType;

/**
 * VendorController
 *
 * @author Tom Haskins-Vaughan <tom@tomhv.uk>
 * @since  0.8.0
 */
class VendorController extends Controller
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
            ->addItem('Vendors', $this->get('router')->generate('phospr_vendor'))
        ;

        $organization = $this->get('phospr.organization_handler')
            ->getOrganization()
        ;

        $vendor = new Vendor();
        $vendor->setOrganization($organization);

        $form = $this->createForm(new VendorType(), $vendor, array(
            'label' => 'Create',
            'action' => $this->generateUrl('phospr_vendor_new'),
        ));

        $vendors = $organization->getVendors();

        if ('json' == $request->getRequestFormat()) {
            $vendorsArray = array();

            foreach ($vendors as $vendor) {
                $vendorsArray[] = array(
                  'label'                     => $vendor->getName(),
                  'value'                     => $vendor->getName(),
                  'defaultOffsetAccount'      => $vendor
                      ->getDefaultOffsetAccountSegmentation(),
                  'defaultJournalDescription' => $vendor
                      ->getDefaultJournalDescription(),
                  'defaultJournalCreditAmount' => $vendor
                      ->getDefaultJournalCreditAmount(),
                  'defaultJournalDebitAmount' => $vendor
                      ->getDefaultJournalDebitAmount(),
                );
            }

            $response = new Response(json_encode($vendorsArray, JSON_PRETTY_PRINT));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        return array(
            'vendors' => $vendors,
            'form'     => $form->createView(),
        );
    }

    /**
     * new
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.8.0
     *
     * @Template
     *
     * @param  Request $request
     */
    public function newAction(Request $request)
    {
        $this->get('white_october_breadcrumbs')
            ->addItem('Home', $this->get('router')->generate('_homepage'))
            ->addItem('Vendors', $this->get('router')->generate('phospr_vendor'))
            ->addItem('New', $this->get('router')->generate('phospr_vendor_new'))
        ;

        $vendor = new Vendor();
        $vendor->setOrganization(
            $this->get('phospr.organization_handler')->getOrganization()
        );

        $form = $this->createForm(new VendorType(), $vendor, array(
            'label' => 'Create',
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->redirect($this->generateUrl(
                'phospr_vendor_show', array(
                    'slug' => $vendor->getSlug(),
                )
            ));
        }

        return array('form'  => $form->createView());
    }

    /**
     * show
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.8.0
     *
     * @Template
     *
     * @ParamConverter("vendor", class="Phospr\CoreBundle\Entity\Vendor")
     *
     * @param  Request $request
     * @param  Vendor  $vendor
     */
    public function showAction(Request $request, Vendor $vendor)
    {
        $this->get('white_october_breadcrumbs')
            ->addItem('Home', $this->get('router')->generate('_homepage'))
            ->addItem('Vendors', $this->get('router')->generate('phospr_vendor'))
            ->addItem(
                $vendor->getName(),
                $this->get('router')->generate('phospr_vendor_show', array(
                    'slug' => $vendor->getSlug(),
                ))
            )
        ;

        return array('vendor' => $vendor);
    }

    /**
     * edit
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  0.8.0
     *
     * @Template
     *
     * @ParamConverter("vendor", class="Phospr\CoreBundle\Entity\Vendor")
     *
     * @param  Request $request
     * @param  Vendor  $vendor
     */
    public function editAction(Request $request, Vendor $vendor)
    {
        $this->get('white_october_breadcrumbs')
            ->addItem('Home', $this->get('router')->generate('_homepage'))
            ->addItem('Vendors', $this->get('router')->generate('phospr_vendor'))
            ->addItem(
                $vendor->getName(),
                $this->get('router')->generate('phospr_vendor_show', array(
                    'slug' => $vendor->getSlug(),
                ))
            )
            ->addItem(
                'Edit',
                $this->get('router')->generate('phospr_vendor_edit', array(
                    'slug' => $vendor->getSlug(),
                ))
            )
        ;

        $form = $this->createForm(new VendorType(), $vendor);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->redirect($this->generateUrl(
                'phospr_vendor_show', array(
                    'slug' => $vendor->getSlug(),
                )
            ));
        }

        return array(
            'vendor' => $vendor,
            'form' => $form->createView(),
        );
    }
}
