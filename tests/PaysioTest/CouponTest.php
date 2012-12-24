<?php

namespace PaysioTest;

class CouponTest extends AbstractTest
{
    public function testCreate()
    {
        $code = 'cp_10_' . mt_rand();
        $object = \Paysio\Coupon::create(array(
            'code' => $code,
            'percent_off' => '10'
        ));

        $this->assertInstanceOf('\Paysio\Coupon', $object);
        $this->assertNotEmpty($object->id);
        $this->assertEquals($code, $object->code);
        $this->assertEquals('10', $object->percent_off);

        return $object;
    }
    
    /**
     * @depends testCreate
     */
    public function testCreateEvent(\Paysio\Coupon $object)
    {
        $this->_testEvent('create', $object);
    }

    /**
     * @depends testCreate
     */
    public function testFetch(\Paysio\Coupon $createdObject)
    {
        return $this->_testFetch($createdObject);
    }

    /**
     * @depends testCreate
     */
    public function testDiscount(\Paysio\Coupon $coupon)
    {
        $charge = \Paysio\Charge::create(array(
            'amount' => 10000,
            'description' => 'Test charge',
            'payment_system_id' => 'test',
            'coupon' => $coupon->code,
            'ip' => '127.0.0.1' // for test ONLY, regular uses $_SERVER['REMOTE_ADDR']
        ));

        $this->assertInstanceOf('\Paysio\Discount', $charge->discount);
        $this->assertEquals(1000, $charge->discount->amount);
        $this->assertInstanceOf('\Paysio\Coupon', $charge->discount->reason);
        $this->assertEquals($coupon->id, $charge->discount->reason->id);
    }

    /**
     * @depends testCreate
     */
    public function testFetchAll(\Paysio\Coupon $createdObject)
    {
        return $this->_testFetchAll($createdObject);
    }

    /**
     * @depends testFetch
     */
    public function testUpdate(\Paysio\Coupon $createdObject)
    {
        $object = \Paysio\Coupon::retrieve($createdObject->id);
        $object->max_amount = '1234';
        $object->save();

        $this->assertInstanceOf('\Paysio\Coupon', $object);
        $this->assertEquals($createdObject->id, $object->id);
        $this->assertEquals('1234', $object->max_amount);

        return $object;
    }
    
    /**
     * @depends testUpdate
     */
    public function testUpdateEvent(\Paysio\Coupon $object)
    {
        $this->_testEvent('update', $object);
    }

    /**
     * @depends testCreate
     */
    public function testLog(\Paysio\Coupon $createdObject)
    {
        $this->_testLog($createdObject);
    }

    /**
     * @depends testUpdate
     */
    public function testCheck(\Paysio\Coupon $createdObject)
    {
        $object = \Paysio\Coupon::check($createdObject->code);

        $this->assertInstanceOf('\Paysio\Coupon', $object);
        $this->assertEquals($createdObject->id, $object->id);
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(\Paysio\Coupon $createdObject)
    {
        return $this->_testDelete($createdObject);
    }
    
    /**
     * @depends testDelete
     */
    public function testDeleteEvent(\Paysio\Coupon $object)
    {
        $this->_testEvent('delete', $object);
    }
}