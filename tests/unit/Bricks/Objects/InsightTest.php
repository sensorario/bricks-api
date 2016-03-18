<?php

namespace Bricks\Objects;

use PHPUnit_Framework_TestCase;
use DateTime;

final class InsightTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->randomShop = 'randomShop' . rand(1111,9999);
        $this->randomSet = 'randomSet' . rand(1111,9999);
        $this->randomValue = 'randomValue' . rand(1111,9999);
        $this->randomUpdate = new \DateTime('now');

        $this->request = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->getMock();

        $this->request->request = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\ParameterBag')
            ->setMethods(['get'])
            ->getMock();

        $this->request->request->expects($this->at(0))
            ->method('get')
            ->with('shop')
            ->will($this->returnValue($this->randomShop));

        $this->request->request->expects($this->at(1))
            ->method('get')
            ->with('set')
            ->will($this->returnValue($this->randomSet));

        $this->request->request->expects($this->at(2))
            ->method('get')
            ->with('value')
            ->will($this->returnValue($this->randomValue));

        $this->request->request->expects($this->at(3))
            ->method('get')
            ->with('update')
            ->will($this->returnValue($this->randomUpdate));

        $this->insight = Insight::box([
            'shop' => $this->request->request->get('shop'),
            'set' => $this->request->request->get('set'),
            'value' => $this->request->request->get('value'),
            'update' => $this->request->request->get('update'),
        ]);

        $this->jsonResponse = $this->insight->jsonSerialize();
    }

    public function testShopIsPresentInResponse()
    {
        $this->assertEquals(
            $this->randomShop,
            $this->jsonResponse['shop']
        );
    }

    public function testTimestampIsInUTCFormat()
    {
        $this->assertEquals(
            (new DateTime())->getTimestamp(),
            $this->insight->get('update')->getTimestamp()
        );
    }
}
