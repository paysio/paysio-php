<?php

namespace PaysioTest;

class WalletTest extends AbstractTest
{
    public function testCreate()
    {
        $object = \Paysio\Wallet::create(array(
            'account' => '79999999999',
            'type' => 'phone_number'
        ));

        $this->assertInstanceOf('\Paysio\Wallet', $object);
        $this->assertNotEmpty($object->id);
        $this->assertEquals('phone_number', $object->type);
        $this->assertEquals('79999999999', $object->account);

        return $object;
    }
    
    /**
     * @depends testCreate
     */
    public function testCreateEvent(\Paysio\Wallet $object)
    {
        $this->_testEvent(array('create', 'update'), $object);
    }

    /**
     * @depends testCreate
     */
    public function testFetch(\Paysio\Wallet $createdObject)
    {
        return $this->_testFetch($createdObject);
    }

    /**
     * @depends testCreate
     */
    public function testFetchAll(\Paysio\Wallet $createdObject)
    {
        return $this->_testFetchAll($createdObject);
    }

    /**
     * @depends testFetch
     */
    public function testUpdate(\Paysio\Wallet $createdObject)
    {
        $object = \Paysio\Wallet::retrieve($createdObject->id);
        $object->account = '79999999998';
        $object->save();

        $this->assertInstanceOf('\Paysio\Wallet', $object);
        $this->assertEquals($createdObject->id, $object->id);
        $this->assertEquals('79999999998', $object->account);

        return $object;
    }

    /**
     * @depends testUpdate
     */
    public function testUpdateEvent(\Paysio\Wallet $object)
    {
        $this->_testEvent('update', $object);
    }

    /**
     * @depends testCreate
     */
    public function testLog(\Paysio\Wallet $createdObject)
    {
        $this->_testLog($createdObject);
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(\Paysio\Wallet $createdObject)
    {
        return $this->_testDelete($createdObject);
    }
    
    /**
     * @depends testDelete
     */
    public function testDeleteEvent(\Paysio\Wallet $object)
    {
        $this->_testEvent('delete', $object);
    }
}