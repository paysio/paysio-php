<?php

namespace PaysioTest;

use \Paysio\Api\AbstractResource;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    protected function _testFetch(AbstractResource $createdObject)
    {
        $objectClass = get_class($createdObject);
        $object = $objectClass::retrieve($createdObject->id);

        $this->assertInstanceOf($objectClass, $object);
        $this->assertEquals($createdObject->id, $object->id);

        return $object;
    }

    protected function _testFetchAll(AbstractResource $createdObject)
    {
        $objectClass = get_class($createdObject);
        $objects = $objectClass::all();

        foreach ($objects as $object) {
            if (!($object instanceof $objectClass)) {
                $this->fail('Object not instance of ' . $objectClass);
            }
            if (empty($object->id)) {
                $this->fail('Object id is empty');
            }
            if ($object->id == $createdObject->id) {
                $found = true;
                break;
            }
        }

        if (!isset($found)) {
            $this->fail('Object not found');
        }

        return $object;
    }

    protected function _testLog(AbstractResource $createdObject)
    {
        $logs = \Paysio\Log::all(array('object_id' => $createdObject->id), null, 4);
        $this->assertGreaterThanOrEqual(2, count($logs));

        $log = $logs[0];

        $this->assertInstanceOf('\Paysio\Log', $log);
        $this->assertContains($createdObject->id, $log->response_body);
        $this->assertGreaterThanOrEqual(time() - 5, $log->created);
    }

    protected function _testDelete(AbstractResource $createdObject)
    {
        $objectClass = get_class($createdObject);
        $object = $objectClass::retrieve($createdObject->id);
        $object->delete();

        $this->assertInstanceOf($objectClass, $object);
        $this->assertEquals($createdObject->id, $object->id);

        try {
            $object = $objectClass::retrieve($createdObject->id);
            $object->delete();
            $this->fail('Repeat call should throw NotFound exception');
        } catch (\Paysio\Api\Exception\NotFound $e) {

        } catch (\Exception $e) {
            $this->fail('Wrong type exceptions throw - ' . get_class($e));
        }

        return $object;
    }

    protected function _testEvent($type, AbstractResource $object)
    {
        $type = (array)$type;
        $objectClass = get_class($object);
        $events = \Paysio\Event::all(array('object_id' => $object->id, 'count' => 4));

        foreach ($events as $event) {
            if (!($event instanceof \Paysio\Event)) {
                $this->fail('Event instance of ' . get_class($event));
            }
            if (empty($event->id)) {
                $this->fail('Event id is empty');
            }
            foreach ($type as $t) {
                if (strpos($event->type, $t) !== false) {
                    $found = true;
                    break;
                }
            }
            if (isset($found)) {
                break;
            }
        }
        if (!isset($found)) {
            $this->fail('Event "' . implode(',', $type) . '" not found');
        }

        $this->assertInstanceOf('\Paysio\Event', $event);
        $this->assertInstanceOf($objectClass, $event->data);
        $this->assertEquals($object->id, $event->data->id);
        $this->assertGreaterThanOrEqual(time() - 10, $event->created);
    }
}