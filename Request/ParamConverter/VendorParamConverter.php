<?php

namespace PengarWin\CoreBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityManager;
use PengarWin\DoubleEntryBundle\Model\OrganizationHandlerInterface;

/**
 * VendorParamConverter
 *
 * @author Tom Haskins-Vaughan <tom@tomhv.uk>
 * @since  1.0.0
 */
class VendorParamConverter implements ParamConverterInterface
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var OrganizationHandlerInterface
     */
    protected $organizationHandler;

    /**
     * Constructor
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  1.0.0
     *
     * @param  EntityManager                $em
     * @param  OrganizationHandlerInterface $organizationHandler
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function __construct(
        EntityManager $em,
        OrganizationHandlerInterface $organizationHandler
    )
    {
        $this->em = $em;
        $this->organizationHandler = $organizationHandler;
    }

    /**
     * Stores the object in the request
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  1.0.0
     *
     * @param Request        $request       The request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $vendor = $this->em->getRepository('PengarWinCoreBundle:Vendor')
            ->findOneBy(array(
                'slug' => $request->attributes->get('slug'),
                'organization' => $this->organizationHandler->getOrganization(),
            ))
        ;

        if (!$vendor) {
            throw new NotFoundHttpException(sprintf(
                'No Vendor found for slug "%s"',
                $request->attributes->get('slug')
            ));
        }

        $request->attributes->set($configuration->getName(), $vendor);

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @author Tom Haskins-Vaughan <tom@tomhv.uk>
     * @since  1.0.0
     *
     * @param  ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        return 'PengarWin\CoreBundle\Entity\Vendor' === $configuration->getClass();
    }
}
