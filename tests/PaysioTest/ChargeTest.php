<?php

namespace PaysioTest;

class ChargeTest extends AbstractTest
{
    public function testCreate()
    {
        $charge = \Paysio\Charge::create(array(
            'amount' => 1234,
            'description' => 'Test charge',
            'payment_system_id' => 'test_success',
            'success_url' => 'http://test.pays.io/success.php',
            'failure_url' => 'http://test.pays.io/failure.php',
            'return_url' => 'http://test.pays.io/',
            'wallet' => array(
                'account' => '79999999999'
            ),
            'ip' => '127.0.0.2' // for test ONLY, regular uses $_SERVER['REMOTE_ADDR']
        ));

        $this->assertNotEmpty($charge->id);
        $this->assertEquals(1234, $charge->amount);
        $this->assertEquals('Test charge', $charge->description);
        $this->assertEquals('test_success', $charge->payment_system_id);
        $this->assertEquals('http://test.pays.io/success.php', $charge->success_url);
        $this->assertEquals('http://test.pays.io/failure.php', $charge->failure_url);
        $this->assertEquals('http://test.pays.io/', $charge->return_url);
        $this->assertEquals('127.0.0.2', $charge->ip);
        $this->assertEquals('paid', $charge->status);

        $response = $charge->getResponse();
        $this->assertEquals(201, $response->getCode());
        $this->assertEquals('http://test.pays.io/success.php?charge_id=' . $charge->id, $response->getLocation());

        $this->assertInstanceOf('\Paysio\Wallet', $charge->wallet);
        $this->assertEquals('phone_number', $charge->wallet->type);
        $this->assertEquals('79999999999', $charge->wallet->account);

        return $charge;
    }

    /**
     * @depends testCreate
     */
    public function testCreateEvent(\Paysio\Charge $createdCharge)
    {
        $this->_testEvent(array('create', 'success'), $createdCharge);
    }

    /**
     * @depends testCreate
     */
    public function testFetch(\Paysio\Charge $createdCharge)
    {
        $charge = \Paysio\Charge::retrieve($createdCharge->id);

        $this->assertInstanceOf('\Paysio\Charge', $charge);
        $this->assertEquals($createdCharge->id, $charge->id);

        $this->assertInstanceOf('\Paysio\Wallet', $charge->wallet);
        $this->assertEquals('phone_number', $charge->wallet->type);
        $this->assertEquals('79999999999', $charge->wallet->account);

        return $charge;
    }

    /**
     * @depends testCreate
     */
    public function testFetchAll(\Paysio\Charge $createdCharge)
    {
        return $this->_testFetchAll($createdCharge);
    }

    /**
     * @depends testFetch
     */
    public function testRefund(\Paysio\Charge $charge)
    {
        $fetchedCharge = clone $charge;

        $charge = $fetchedCharge->refund();

        $this->assertEquals($charge->id, $fetchedCharge->id);
        $this->assertEquals('refunded', $charge->status);
        $this->assertEquals($charge->amount_refunded, $fetchedCharge->amount);

        return $charge;
    }

    /**
     * @depends testRefund
     */
    public function testRefundEvent(\Paysio\Charge $createdCharge)
    {
        $this->_testEvent('refund', $createdCharge);
    }

    /**
     * @depends testCreate
     */
    public function testLog(\Paysio\Charge $createdCharge)
    {
        $this->_testLog($createdCharge);
    }

    public function testError()
    {
        try {
            $charge = \Paysio\Charge::create(array(
                'amount' => 1234,
                'description' => 'Test charge',
                'payment_system_id' => 'test_mobile',
                'ip' => '127.0.0.2' // for test ONLY, regular uses $_SERVER['REMOTE_ADDR']
            ));
        } catch (\Paysio\Api\Exception\BadRequest $e) {
            $params = $e->getParams();

            $this->assertCount(1, $params);
            $this->assertEquals('required', $params[0]->code);
            $this->assertEquals('wallet[account]', $params[0]->name);
        } catch (\Exception $e) {
            $this->fail('Wrong exception type ' . get_class($e));
        }
    }

    public function testInvoiceCreate()
    {
        $charge = \Paysio\Charge::create(array(
            'amount' => 1234,
            'description' => 'Test charge',
            'ip' => '127.0.0.2' // for test ONLY, regular uses $_SERVER['REMOTE_ADDR']
        ));

        $this->assertNotEmpty($charge->id);
        $this->assertEquals(1234, $charge->amount);
        $this->assertEquals(null, $charge->payment_system_id);
        $this->assertEquals('pending', $charge->status);

        $response = $charge->getResponse();

        $this->assertContains('v1/charges/' . $charge->id . '/invoice', $response->getLocation());

        return $charge;
    }

    /**
     * @depends testInvoiceCreate
     */
    public function testInvoiceUpdate(\Paysio\Charge $cratedCharge)
    {
        $charge = \Paysio\Charge::retrieve($cratedCharge->id);
        $charge->populate(array(
            'payment_system_id' => 'test_success',
            'wallet' => array(
                'account' => '79999999999'
            ),
        ));
        $charge->save();

        $this->assertEquals($cratedCharge->id, $charge->id);
        $this->assertEquals('test_success', $charge->payment_system_id);
        $this->assertEquals('paid', $charge->status);

        $this->assertInstanceOf('\Paysio\Wallet', $charge->wallet);
        $this->assertEquals('phone_number', $charge->wallet->type);
        $this->assertEquals('79999999999', $charge->wallet->account);

        $response = $charge->getResponse();
        $this->assertEquals(201, $response->getCode());
        $this->assertContains('payment/success?charge_id=' . $charge->id, $response->getLocation());

        return $charge;
    }

    /**
     * @depends testInvoiceCreate
     */
    public function testInvoiceRedirect(\Paysio\Charge $cratedCharge)
    {
        $charge = \Paysio\Charge::retrieve($cratedCharge->id);
        $charge->invoice();

        $response = $charge->getResponse();
        $this->assertEquals(201, $response->getCode());
        $this->assertContains('payment/success?charge_id=' . $charge->id, $response->getLocation());

        $this->assertInstanceOf('\Paysio\Wallet', $charge->wallet);
        $this->assertEquals('phone_number', $charge->wallet->type);
        $this->assertEquals('79999999999', $charge->wallet->account);
    }
}