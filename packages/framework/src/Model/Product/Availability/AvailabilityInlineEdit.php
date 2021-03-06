<?php

namespace Shopsys\FrameworkBundle\Model\Product\Availability;

use Shopsys\FrameworkBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\FrameworkBundle\Form\Admin\Product\Availability\AvailabilityFormType;
use Symfony\Component\Form\FormFactoryInterface;

class AvailabilityInlineEdit extends AbstractGridInlineEdit
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade
     */
    protected $availabilityFacade;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityDataFactoryInterface
     */
    protected $availabilityDataFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityGridFactory $availabilityGridFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade $availabilityFacade
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityDataFactoryInterface $availabilityDataFactory
     */
    public function __construct(
        AvailabilityGridFactory $availabilityGridFactory,
        AvailabilityFacade $availabilityFacade,
        FormFactoryInterface $formFactory,
        AvailabilityDataFactoryInterface $availabilityDataFactory
    ) {
        parent::__construct($availabilityGridFactory);

        $this->availabilityFacade = $availabilityFacade;
        $this->formFactory = $formFactory;
        $this->availabilityDataFactory = $availabilityDataFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData $availabilityData
     * @return int
     */
    protected function createEntityAndGetId($availabilityData)
    {
        $availability = $this->availabilityFacade->create($availabilityData);

        return $availability->getId();
    }

    /**
     * @param int $availabilityId
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData $availabilityData
     */
    protected function editEntity($availabilityId, $availabilityData)
    {
        $this->availabilityFacade->edit($availabilityId, $availabilityData);
    }

    /**
     * @param int|null $availabilityId
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm($availabilityId)
    {
        if ($availabilityId !== null) {
            $availability = $this->availabilityFacade->getById((int)$availabilityId);
            $availabilityData = $this->availabilityDataFactory->createFromAvailability($availability);
        } else {
            $availabilityData = $this->availabilityDataFactory->create();
        }

        return $this->formFactory->create(AvailabilityFormType::class, $availabilityData);
    }
}
