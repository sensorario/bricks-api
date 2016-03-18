<?php

namespace Bricks;

use PHPUnit_Framework_TestCase;
use DateTime;

final class InsightTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->randomShop = 'randomShop' . rand(1111,9999);

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

        $this->insight = Insight::fromRequest($this->request);

        $this->jsonResponse = $this->insight->jsonSerialize();
    }

    public function testShopIsPresentInResponse()
    {
        $this->assertEquals($this->randomShop, $this->jsonResponse['shop']);
    }

    public function testTimestampIsInUTCFormat()
    {
        $this->assertEquals(
            (new DateTime())->getTimestamp(),
            $this->insight->getTimestamp()
        );
    }
}
