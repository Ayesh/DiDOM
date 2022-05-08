<?php

declare(strict_types=1);

namespace DiDom\Tests;

use DiDom\Element;
use DiDom\StyleAttribute;
use DOMComment;
use DOMText;
use InvalidArgumentException;

class StyleAttributeTest extends TestCase
{
    public function testConstructorWithTextNode()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The element must contain DOMElement node.');

        $element = new Element(new DOMText('foo'));

        new StyleAttribute($element);
    }

    public function testConstructorWithCommentNode()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The element must contain DOMElement node.');

        $element = new Element(new DOMComment('foo'));

        new StyleAttribute($element);
    }

    public function testSetProperty()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertEquals('color: blue; border: 1px solid black', $element->getAttribute('style'));

        $styleAttribute->setProperty('font-size', '16px');

        $this->assertEquals('color: blue; border: 1px solid black; font-size: 16px', $element->getAttribute('style'));
    }

    public function testSetMultiplePropertiesWithInvalidPropertyName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Property name must be a string, integer given.');

        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $styleAttribute->setMultipleProperties([
            'width' => '50px',
            'height',
        ]);
    }

    public function testSetMultiplePropertiesWithInvalidPropertyValue()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Property value must be a string, NULL given.');

        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $styleAttribute->setMultipleProperties([
            'width' => '50px',
            'height' => null,
        ]);
    }

    public function testSetMultipleProperties()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertEquals('color: blue; border: 1px solid black', $element->getAttribute('style'));

        $styleAttribute->setMultipleProperties([
            'font-size' => '16px',
            'font-family' => 'Times',
        ]);

        $this->assertEquals('color: blue; border: 1px solid black; font-size: 16px; font-family: Times', $element->getAttribute('style'));
    }

    /**
     * @param string $styleString
     * @param string $propertyName
     * @param string $expectedResult
     *
     * @dataProvider getPropertyDataProvider
     */
    public function testGetProperty($styleString, $propertyName, $expectedResult)
    {
        $element = new Element('div', null, [
            'style' => $styleString,
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertEquals($expectedResult, $styleAttribute->getProperty($propertyName));
    }

    public function getPropertyDataProvider(): array
    {
        return [
            [
                'color: blue; font-size: 16px; border: 1px solid black',
                'font-size',
                '16px',
            ],
            [
                'background-image: url(https://example.com/image.jpg); background-repeat: no-repeat',
                'background-image',
                'url(https://example.com/image.jpg)',
            ],
            [
                'color: blue; font-size: 16px; border: 1px solid black;',
                'foo',
                null,
            ],
        ];
    }

    public function testGetPropertyWithDefaultValue()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertNull($styleAttribute->getProperty('font-size'));
        $this->assertEquals('16px', $styleAttribute->getProperty('font-size', '16px'));
    }

    public function testGetMultiplePropertiesWithInvalidPropertyName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Property name must be a string, NULL given.');

        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $styleAttribute->getMultipleProperties(['color', null]);
    }

    /**
     * @param string $styleString
     * @param array $propertyNames
     * @param string $expectedResult
     *
     * @dataProvider getMultiplePropertiesDataProvider
     */
    public function testGetMultipleProperties($styleString, $propertyNames, $expectedResult)
    {
        $element = new Element('div', null, [
            'style' => $styleString,
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertEquals($expectedResult, $styleAttribute->getMultipleProperties($propertyNames));
    }

    public function getMultiplePropertiesDataProvider()
    {
        return [
            [
                'color: blue; font-size: 16px; font-family: Times; border: 1px solid black',
                ['font-size'],
                [
                    'font-size' => '16px',
                ],
            ],
            [
                'color: blue; font-size: 16px; font-family: Times; border: 1px solid black',
                ['font-size', 'border'],
                [
                    'font-size' => '16px',
                    'border' => '1px solid black',
                ],
            ],
            [
                'color: blue; font-size: 16px; font-family: Times; border: 1px solid black',
                ['font-size', 'border', 'width'],
                [
                    'font-size' => '16px',
                    'border' => '1px solid black',
                ],
            ],
        ];
    }

    /**
     * @param string $styleString
     * @param string $expectedResult
     *
     * @dataProvider getAllPropertiesDataProvider
     */
    public function testGetAllProperties($styleString, $expectedResult)
    {
        $element = new Element('div', null, [
            'style' => $styleString,
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertEquals($expectedResult, $styleAttribute->getAllProperties());
    }

    public function getAllPropertiesDataProvider(): array
    {
        return [
            [
                '',
                [],
            ],
            [
                'color: blue; font-size: 16px; border: 1px solid black',
                [
                    'color' => 'blue',
                    'font-size' => '16px',
                    'border' => '1px solid black',
                ],
            ],
            [
                'color: blue; font-size: 16px; border: 1px solid black',
                [
                    'color' => 'blue',
                    'font-size' => '16px',
                    'border' => '1px solid black',
                ],
            ],
        ];
    }

    public function testGetAllPropertiesAfterEmptyStyleAttribute()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertEquals(['color' => 'blue'], $styleAttribute->getAllProperties());

        $element->setAttribute('style', '');

        $this->assertEquals([], $styleAttribute->getAllProperties());
    }

    public function testHasProperty()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertTrue($styleAttribute->hasProperty('color'));
        $this->assertFalse($styleAttribute->hasProperty('width'));
    }

    public function testRemoveProperty()
    {
        $styleString = 'color: blue; font-size: 16px; border: 1px solid black';

        $element = new Element('span', 'foo', [
            'style' => $styleString,
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertEquals($styleString, $element->getAttribute('style'));

        $styleAttribute->removeProperty('font-size');

        $this->assertEquals('color: blue; border: 1px solid black', $element->getAttribute('style'));
    }

    public function testRemoveMultiplePropertiesWithInvalidPropertyName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Property name must be a string, NULL given.');

        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $styleAttribute->removeMultipleProperties(['color', null]);
    }

    /**
     * @param string $styleString
     * @param array $propertyNames
     * @param string $expectedResult
     *
     * @dataProvider removeMultiplePropertiesDataProvider
     */
    public function testRemoveMultipleProperties($styleString, $propertyNames, $expectedResult)
    {
        $element = new Element('div', null, [
            'style' => $styleString,
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertEquals($styleString, $element->getAttribute('style'));

        $styleAttribute->removeMultipleProperties($propertyNames);

        $this->assertEquals($expectedResult, $element->getAttribute('style'));
    }

    public function removeMultiplePropertiesDataProvider(): array
    {
        return [
            [
                'color: blue; font-size: 16px; font-family: Times; border: 1px solid black',
                [
                    'font-size',
                ],
                'color: blue; font-family: Times; border: 1px solid black',
            ],
            [
                'color: blue; font-size: 16px; font-family: Times; border: 1px solid black',
                [
                    'font-size', 'border',
                ],
                'color: blue; font-family: Times',
            ],
            [
                'color: blue; font-size: 16px; font-family: Times; border: 1px solid black',
                [
                    'font-size', 'border', 'width',
                ],
                'color: blue; font-family: Times',
            ],
        ];
    }

    public function testRemoveAllPropertiesWithInvalidPropertyName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Property name must be a string, NULL given.');

        $element = new Element('div', null, [
            'style' => 'color: blue; border: 1px solid black',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $styleAttribute->removeAllProperties(['color', null]);
    }

    /**
     * @param string $styleString
     * @param array $exclusions
     * @param string $expectedResult
     *
     * @dataProvider removeAllPropertiesDataProvider
     */
    public function testRemoveAllProperties($styleString, $exclusions, $expectedResult)
    {
        $element = new Element('div', null, [
            'style' => $styleString,
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertEquals($styleString, $element->getAttribute('style'));

        $styleAttribute->removeAllProperties($exclusions);

        $this->assertEquals($expectedResult, $element->getAttribute('style'));
    }

    public function removeAllPropertiesDataProvider(): array
    {
        return [
            [
                'color: blue; font-size: 16px; font-family: Times; border: 1px solid black',
                [
                    'font-size',
                ],
                'font-size: 16px',
            ],
            [
                'color: blue; font-size: 16px; font-family: Times; border: 1px solid black',
                [
                    'font-size', 'border',
                ],
                'font-size: 16px; border: 1px solid black',
            ],
            [
                'color: blue; font-size: 16px; font-family: Times; border: 1px solid black',
                [
                    'font-size', 'border', 'width',
                ],
                'font-size: 16px; border: 1px solid black',
            ],
        ];
    }

    public function testGetElement()
    {
        $element = new Element('div', null, [
            'style' => 'color: blue; font-size: 16px',
        ]);

        $styleAttribute = new StyleAttribute($element);

        $this->assertSame($element, $styleAttribute->getElement());
    }
}
