<?php

namespace PaysioTest;

class CustomerTest extends AbstractTest
{
    public function testCreate()
    {
        $object = \Paysio\Customer::create(array(
            'email' => 'mail@test.com',
            'phone_number' => '79999999999'
        ));

        $this->assertInstanceOf('\Paysio\Customer', $object);
        $this->assertNotEmpty($object->id);
        $this->assertEquals('mail@test.com', $object->email);
        $this->assertEquals('79999999999', $object->phone_number);

        return $object;
    }
    
    /**
     * @depends testCreate
     */
    public function testCreateEvent(\Paysio\Customer $object)
    {
        $this->_testEvent('create', $object);
    }

    /**
     * @depends testCreate
     */
    public function testFetch(\Paysio\Customer $createdObject)
    {
        return $this->_testFetch($createdObject);
    }

    /**
     * @depends testCreate
     */
    public function testFetchAll(\Paysio\Customer $createdObject)
    {
        return $this->_testFetchAll($createdObject);
    }

    /**
     * @depends testFetch
     */
    public function testUpdate(\Paysio\Customer $createdObject)
    {
        $object = \Paysio\Customer::retrieve($createdObject->id);
        $object->populate(array(
            'email' => '',
            'phone_number' => '79999999998'
        ));
        $object->save();

        $this->assertInstanceOf('\Paysio\Customer', $object);
        $this->assertEquals($createdObject->id, $object->id);
        $this->assertEquals('', $object->email);
        $this->assertEquals('79999999998', $object->phone_number);

        return $object;
    }
    
    /**
     * @depends testUpdate
     */
    public function testUpdateEvent(\Paysio\Customer $object)
    {
        $this->_testEvent('update', $object);
    }

    /**
     * @depends testCreate
     */
    public function testLog(\Paysio\Customer $createdObject)
    {
        $this->_testLog($createdObject);
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(\Paysio\Customer $createdObject)
    {
        return $this->_testDelete($createdObject);
    }
    
    /**
     * @depends testDelete
     */
    public function testDeleteEvent(\Paysio\Customer $object)
    {
        $this->_testEvent('delete', $object);
    }
}