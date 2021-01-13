<?php


namespace Invertus\dpdBaltics\DTO;

class ShipmentData
{
    /**
     * @var string
     */
    private $addressId;

    /**
     * @var string
     */
    private $company;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $surname;

    /**
     * @var string
     */
    private $address1;

    /**
     * @var string
     */
    private $address2;

    /**
     * @var string
     */
    private $postCode;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $phoneArea;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $product;

    /**
     * @var string
     */
    private $dateShipment;

    /**
     * @var string
     */
    private $reference1;

    /**
     * @var string
     */
    private $reference2;

    /**
     * @var string
     */
    private $reference3;

    /**
     * @var string
     */
    private $reference4;

    /**
     * @var string
     */
    private $weight;

    /**
     * @var string
     */
    private $parcelAmount;

    /**
     * @var string
     */
    private $goods_price;

    /**
     * @var string
     */
    private $labelFormat;

    /**
     * @var string
     */
    private $labelPosition;

    /**
     * @var string
     */
    private $idPudo;

    /**
     * @var string
     */
    private $dpdCountry;

    /**
     * @var string
     */
    private $dpdCity;

    /**
     * @var string
     */
    private $dpdZipCode;

    /**
     * @var string
     */
    private $dpdStreet;

    /**
     * @var string
     */
    private $selectedPudoId;

    /**
     * @var string
     */
    private $selectedPudoIsoCode;

    /**
     * @var bool
     */
    private $isPudo;

    /**
     * @var string
     */
    private $deliveryTime;

    /**
     * @var bool
     */
    private $dpdDocumentReturn = false;

    /**
     * @var string
     */
    private $dpdDocumentReturnNumber;
    /**
     * @return string
     */
    public function getAddressId()
    {
        return $this->addressId;
    }

    /**
     * @param string $addressId
     */
    public function setAddressId($addressId)
    {
        $this->addressId = $addressId;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * @param string $address1
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
    }

    /**
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @param string $address2
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
    }

    /**
     * @return string
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * @param string $postCode
     */
    public function setPostCode($postCode)
    {
        $this->postCode = $postCode;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPhoneArea()
    {
        return $this->phoneArea;
    }

    /**
     * @param string $phoneArea
     */
    public function setPhoneArea($phoneArea)
    {
        $this->phoneArea = $phoneArea;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param string $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return string
     */
    public function getDateShipment()
    {
        return $this->dateShipment;
    }

    /**
     * @param string $dateShipment
     */
    public function setDateShipment($dateShipment)
    {
        $this->dateShipment = $dateShipment;
    }

    /**
     * @return string
     */
    public function getReference1()
    {
        return $this->reference1;
    }

    /**
     * @param string $reference1
     */
    public function setReference1($reference1)
    {
        $this->reference1 = $reference1;
    }

    /**
     * @return string
     */
    public function getReference2()
    {
        return $this->reference2;
    }

    /**
     * @param string $reference2
     */
    public function setReference2($reference2)
    {
        $this->reference2 = $reference2;
    }

    /**
     * @return string
     */
    public function getReference3()
    {
        return $this->reference3;
    }

    /**
     * @param string $reference3
     */
    public function setReference3($reference3)
    {
        $this->reference3 = $reference3;
    }

    /**
     * @return string
     */
    public function getReference4()
    {
        return $this->reference4;
    }

    /**
     * @param string $reference4
     */
    public function setReference4($reference4)
    {
        $this->reference4 = $reference4;
    }

    /**
     * @return string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param string $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return string
     */
    public function getParcelAmount()
    {
        return $this->parcelAmount;
    }

    /**
     * @param string $parcelAmount
     */
    public function setParcelAmount($parcelAmount)
    {
        $this->parcelAmount = $parcelAmount;
    }

    /**
     * @return string
     */
    public function getGoodsPrice()
    {
        return $this->goods_price;
    }

    /**
     * @param string $goods_price
     */
    public function setGoodsPrice($goods_price)
    {
        $this->goods_price = $goods_price;
    }

    /**
     * @return string
     */
    public function getLabelFormat()
    {
        return $this->labelFormat;
    }

    /**
     * @param string $labelFormat
     */
    public function setLabelFormat($labelFormat)
    {
        $this->labelFormat = $labelFormat;
    }

    /**
     * @return string
     */
    public function getLabelPosition()
    {
        return $this->labelPosition;
    }

    /**
     * @param string $labelPosition
     */
    public function setLabelPosition($labelPosition)
    {
        $this->labelPosition = $labelPosition;
    }

    /**
     * @return string
     */
    public function getIdPudo()
    {
        return $this->idPudo;
    }

    /**
     * @param string $idPudo
     */
    public function setIdPudo($idPudo)
    {
        $this->idPudo = $idPudo;
    }

    /**
     * @return string
     */
    public function getDpdCountry()
    {
        return $this->dpdCountry;
    }

    /**
     * @param string $dpdCountry
     */
    public function setDpdCountry($dpdCountry)
    {
        $this->dpdCountry = $dpdCountry;
    }

    /**
     * @return string
     */
    public function getDpdCity()
    {
        return $this->dpdCity;
    }

    /**
     * @param string $dpdCity
     */
    public function setDpdCity($dpdCity)
    {
        $this->dpdCity = $dpdCity;
    }

    /**
     * @return string
     */
    public function getDpdZipCode()
    {
        return $this->dpdZipCode;
    }

    /**
     * @param string $dpdZipCode
     */
    public function setDpdZipCode($dpdZipCode)
    {
        $this->dpdZipCode = $dpdZipCode;
    }

    /**
     * @return string
     */
    public function getDpdStreet()
    {
        return $this->dpdStreet;
    }

    /**
     * @param string $dpdStreet
     */
    public function setDpdStreet($dpdStreet)
    {
        $this->dpdStreet = $dpdStreet;
    }

    /**
     * @return string
     */
    public function getSelectedPudoId()
    {
        return $this->selectedPudoId;
    }

    /**
     * @param string $selectedPudoId
     */
    public function setSelectedPudoId($selectedPudoId)
    {
        $this->selectedPudoId = $selectedPudoId;
    }

    /**
     * @return string
     */
    public function getSelectedPudoIsoCode()
    {
        return $this->selectedPudoIsoCode;
    }

    /**
     * @param string $selectedPudoIsoCode
     */
    public function setSelectedPudoIsoCode($selectedPudoIsoCode)
    {
        $this->selectedPudoIsoCode = $selectedPudoIsoCode;
    }

    /**
     * @return bool
     */
    public function isPudo()
    {
        return $this->isPudo;
    }

    /**
     * @param bool $isPudo
     */
    public function setIsPudo($isPudo)
    {
        $this->isPudo = $isPudo;
    }

    /**
     * @return string
     */
    public function getDeliveryTime()
    {
        return $this->deliveryTime;
    }

    /**
     * @param string $deliveryTime
     */
    public function setDeliveryTime($deliveryTime)
    {
        $this->deliveryTime = $deliveryTime;
    }

    /**
     * @return bool
     */
    public function isDpdDocumentReturn()
    {
        return $this->dpdDocumentReturn;
    }

    /**
     * @param bool $dpdDocumentReturn
     */
    public function setDpdDocumentReturn($dpdDocumentReturn)
    {
        $this->dpdDocumentReturn = $dpdDocumentReturn;
    }

    /**
     * @return string
     */
    public function getDpdDocumentReturnNumber()
    {
        return $this->dpdDocumentReturnNumber;
    }

    /**
     * @param string $dpdDocumentReturnNumber
     */
    public function setDpdDocumentReturnNumber($dpdDocumentReturnNumber)
    {
        $this->dpdDocumentReturnNumber = $dpdDocumentReturnNumber;
    }
}
