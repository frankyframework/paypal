<?php
namespace Paypal\entity;

class PaypalPaymentEntity
{
    private $id;
    private $order_id;
    private $data;
    private $created_at;
   
    public function __construct($data = null)
    {
        if (null != $data) {
            $this->exchangeArray($data);
        }
    }


    public function exchangeArray($data)
    {
        $this->id                   = (isset($data['id']))                  ? $data['id']                   : null;
        $this->order_id                  = (isset($data['order_id']))                 ? $data['order_id']                  : null;
        $this->data       = (isset($data['data']))      ? $data['data']       : null;
        $this->created_at           = (isset($data['created_at']))          ? $data['created_at']           : null;
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setValidation()
    {
        return array(
        _ecommerce("Order") => array("valor" => $this->order_id,"required"),
        );
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getOrderId()
    {
        return $this->order_id;
    }

    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

 }

