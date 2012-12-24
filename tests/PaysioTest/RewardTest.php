<?php

namespace PaysioTest;

class RewardTest extends AbstractTest
{
    public function testCreate()
    {
        $object = \Paysio\Reward::create(array(
            'title' => 'test',
            'percent_off' => '10',
            'payment_amount' => 1200
        ));

        $this->assertInstanceOf('\Paysio\Reward', $object);
        $this->assertNotEmpty($object->id);
        $this->assertEquals('test', $object->title);
        $this->assertEquals('10', $object->percent_off);
        $this->assertEquals('1200', $object->payment_amount);

        return $object;
    }
    
    /**
     * @depends testCreate
     */
    public function testCreateEvent(\Paysio\Reward $object)
    {
        $this->_testEvent('create', $object);
    }

    /**
     * @depends testCreate
     */
    public function testFetch(\Paysio\Reward $createdObject)
    {
        return $this->_testFetch($createdObject);
    }
    
    /**
     * @depends testCreate
     */
    public function testDiscount(\Paysio\Reward $reward)
    {
        $params = array(
            'payment_system_id' => 'test_success',
            'description' => 'Test charge',
            'wallet' => array(
                'type' => 'phone_number',
                'account' => '79999999999'
            ),
            'ip' => '127.0.0.1' // for test ONLY, regular uses $_SERVER['REMOTE_ADDR']
        );

        $params['amount'] = 1250;
        $testCharge = \Paysio\Charge::create($params);

        $params['amount'] = 10000;
        $charge = \Paysio\Charge::create($params);

        $this->assertInstanceOf('\Paysio\Discount', $charge->discount);
        $this->assertEquals(1000, $charge->discount->amount);
        $this->assertInstanceOf('\Paysio\Reward', $charge->discount->reason);
        $this->assertEquals($reward->id, $charge->discount->reason->id);
    }

    /**
     * @depends testCreate
     */
    public function testFetchAll(\Paysio\Reward $createdObject)
    {
        return $this->_testFetchAll($createdObject);
    }

    /**
     * @depends testFetch
     */
    public function testUpdate(\Paysio\Reward $createdObject)
    {
        $object = \Paysio\Reward::retrieve($createdObject->id);
        $object->max_amount = '1234';
        $object->save();

        $this->assertInstanceOf('\Paysio\Reward', $object);
        $this->assertEquals($createdObject->id, $object->id);
        $this->assertEquals('1234', $object->max_amount);

        return $object;
    }
    
    /**
     * @depends testUpdate
     */
    public function testUpdateEvent(\Paysio\Reward $object)
    {
        $this->_testEvent('update', $object);
    }

    /**
     * @depends testCreate
     */
    public function testLog(\Paysio\Reward $createdObject)
    {
        $this->_testLog($createdObject);
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(\Paysio\Reward $createdObject)
    {
        return $this->_testDelete($createdObject);
    }
    
    /**
     * @depends testDelete
     */
    public function testDeleteEvent(\Paysio\Reward $object)
    {
        $this->_testEvent('delete', $object);
    }
}