<?php

namespace PaysioTest;

class PayoutTest extends AbstractTest
{
    public function testCreate()
    {
        $payout = \Paysio\Payout::create(array(
            'amount' => 1234,
            'currency_id' => 'rur',
            'description' => 'Test charge',
            'payment_system_id' => 'test_phone_payout',
            'wallet' => array(
                'account' => '79999999999'
            )
        ));

        $this->assertNotEmpty($payout->id);
        $this->assertEquals(1234, $payout->amount);
        $this->assertEquals('rur', $payout->currency_id);
        $this->assertEquals('Test charge', $payout->description);
        $this->assertEquals('test_phone_payout', $payout->payment_system_id);
        $this->assertEquals('pending', $payout->status);

        $this->assertInstanceOf('\Paysio\Wallet', $payout->wallet);
        $this->assertEquals('phone_number', $payout->wallet->type);
        $this->assertEquals('79999999999', $payout->wallet->account);

        return $payout;
    }

    /**
     * @depends testCreate
     */
    public function testCreateEvent(\Paysio\Payout $createdPayout)
    {
        $this->_testEvent('create', $createdPayout);
    }

    /**
     * @depends testCreate
     */
    public function testFetch(\Paysio\Payout $createdPayout)
    {
        $payout = \Paysio\Payout::retrieve($createdPayout->id);

        $this->assertInstanceOf('\Paysio\Payout', $payout);
        $this->assertEquals($createdPayout->id, $payout->id);

        $this->assertInstanceOf('\Paysio\Wallet', $payout->wallet);
        $this->assertEquals('phone_number', $payout->wallet->type);
        $this->assertEquals('79999999999', $payout->wallet->account);

        return $payout;
    }

    /**
     * @depends testCreate
     */
    public function testFetchAll(\Paysio\Payout $createdPayout)
    {
        return $this->_testFetchAll($createdPayout);
    }

    /**
     * @depends testCreate
     */
    public function testLog(\Paysio\Payout $createdPayout)
    {
        $this->_testLog($createdPayout);
    }

    public function testError()
    {
        try {
            $payout = \Paysio\Payout::create(array(
                'amount' => 1234,
                'description' => 'Test charge',
                'payment_system_id' => 'test_phone_payout',
            ));
        } catch (\Paysio\Api\Exception\BadRequest $e) {
            $params = $e->getParams();

            $this->assertCount(1, $params);
            $this->assertEquals('validateWallet', $params[0]->code);
            $this->assertEquals('wallet_id', $params[0]->name);
        } catch (\Exception $e) {
            $this->fail('Wrong exception type ' . get_class($e));
        }
    }
}