<?php

namespace AppBundle\Entity;

use AppBundle\Entity\LocalBusiness\ClosingRulesTrait;
use AppBundle\Entity\LocalBusiness\FulfillmentMethodsTrait;
use AppBundle\Entity\LocalBusiness\ShippingOptionsInterface;
use AppBundle\Entity\LocalBusiness\ShippingOptionsTrait;
use AppBundle\OpeningHours\OpenCloseInterface;
use AppBundle\OpeningHours\OpenCloseTrait;
use AppBundle\Sylius\Order\OrderInterface;
use Doctrine\Common\Collections\ArrayCollection;

class Hub implements OpenCloseInterface
{
    use ClosingRulesTrait;
    use FulfillmentMethodsTrait;
    use ShippingOptionsTrait;
    use OpenCloseTrait;

    private $id;
    private $name;
    private $address;
    private $restaurants;
    private $contract;

    public function __construct()
    {
        $this->restaurants = new ArrayCollection();
        $this->closingRules = new ArrayCollection();

        $this->fulfillmentMethods = new ArrayCollection();
        $this->addFulfillmentMethod('delivery', true);
        $this->addFulfillmentMethod('collection', false);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     *
     * @return self
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRestaurants()
    {
        return $this->restaurants;
    }

    /**
     * @param mixed $restaurants
     *
     * @return self
     */
    public function setRestaurants($restaurants)
    {
        $this->restaurants = $restaurants;

        return $this;
    }

    /**
     * @param mixed $restaurant
     */
    public function addRestaurant($restaurant)
    {
        $this->restaurants->add($restaurant);
    }

    /**
     * @return Contract
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * @param Contract $contract
     */
    public function setContract(Contract $contract)
    {
        $this->contract = $contract;
    }

    /**
     * @return int
     */
    public function getItemsTotalForRestaurant(OrderInterface $order, LocalBusiness $restaurant): int
    {
        $total = 0;
        foreach ($order->getItems() as $item) {
            if ($restaurant->hasProduct($item->getVariant()->getProduct())) {
                $total += $item->getTotal();
            }
        }

        return $total;
    }

    /**
     * @return float
     */
    public function getPercentageForRestaurant(OrderInterface $order, LocalBusiness $restaurant): float
    {
        $total = $order->getItemsTotal();
        $itemsTotal = $this->getItemsTotalForRestaurant($order, $restaurant);

        return round($itemsTotal / $total, 2);
    }
}
