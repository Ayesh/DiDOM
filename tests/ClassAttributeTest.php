<?php

declare(strict_types=1);

namespace DiDom\Tests;

use DiDom\Element;
use DiDom\ClassAttribute;
use DOMComment;
use DOMText;
use InvalidArgumentException;

class ClassAttributeTest extends TestCase
{
    public function testConstructorWithTextNode()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The element must contain DOMElement node.');

        $element = new Element(new DOMText('foo'));

        new ClassAttribute($element);
    }

    public function testConstructorWithCommentNode()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The element must contain DOMElement node.');

        $element = new Element(new DOMComment('foo'));

        new ClassAttribute($element);
    }

    public function testAdd()
    {
        // without class attribute
        $element = new Element('div', null);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals(null, $element->getAttribute('class'));

        $classAttribute->add('foo');

        $this->assertEquals('foo', $element->getAttribute('class'));

        // with empty class attribute
        $element = new Element('div', null, [
            'class' => '',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals('', $element->getAttribute('class'));

        $classAttribute->add('foo');

        $this->assertEquals('foo', $element->getAttribute('class'));

        // class attribute with spaces
        $element = new Element('div', null, [
            'class' => '   ',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals('   ', $element->getAttribute('class'));

        $classAttribute->add('foo');

        $this->assertEquals('foo', $element->getAttribute('class'));

        // with not empty class attribute
        $element = new Element('div', null, [
            'class' => 'foo bar',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals('foo bar', $element->getAttribute('class'));

        $classAttribute->add('baz');

        $this->assertEquals('foo bar baz', $element->getAttribute('class'));

        // with existing class name
        $element = new Element('div', null, [
            'class' => 'foo bar baz',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals('foo bar baz', $element->getAttribute('class'));

        $classAttribute->add('bar');

        $this->assertEquals('foo bar baz', $element->getAttribute('class'));
    }

    public function testAddMultipleWithInvalidClassName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class name must be a string, NULL given.');

        $element = new Element('div', null, [
            'class' => 'foo',
        ]);

        $classAttribute = new ClassAttribute($element);

        $classAttribute->addMultiple(['bar', null]);
    }

    public function testAddMultiple()
    {
        $element = new Element('div', null, [
            'class' => 'foo',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals('foo', $element->getAttribute('class'));

        $classAttribute->addMultiple([
            'bar', 'baz',
        ]);

        $this->assertEquals('foo bar baz', $element->getAttribute('class'));
    }

    public function testGetAll()
    {
        // without class attribute
        $element = new Element('div', null);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals([], $classAttribute->getAll());

        // with empty class attribute
        $element = new Element('div', null, [
            'class' => '',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals([], $classAttribute->getAll());

        // class attribute with spaces
        $element = new Element('div', null, [
            'class' => '   ',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals([], $classAttribute->getAll());

        // one class
        $element = new Element('div', null, [
            'class' => 'foo',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals(['foo'], $classAttribute->getAll());

        // several classes
        $element = new Element('div', null, [
            'class' => 'foo bar baz',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals(['foo', 'bar', 'baz'], $classAttribute->getAll());

        // with multiple spaces between class names
        $element = new Element('div', null, [
            'class' => 'foo   bar   baz',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals(['foo', 'bar', 'baz'], $classAttribute->getAll());
    }

    public function testGetAllPropertiesAfterEmptyClassAttribute()
    {
        $element = new Element('div', null, [
            'class' => 'foo bar baz',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals(['foo', 'bar', 'baz'], $classAttribute->getAll());

        $element->setAttribute('class', '');

        $this->assertEquals([], $classAttribute->getAll());
    }

    public function testContains()
    {
        $element = new Element('div', null, [
            'class' => 'foo bar',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertTrue($classAttribute->contains('foo'));
        $this->assertTrue($classAttribute->contains('bar'));
        $this->assertFalse($classAttribute->contains('baz'));
    }

    public function testRemove()
    {
        $element = new Element('div', null, [
            'class' => 'foo bar baz',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals('foo bar baz', $element->getAttribute('class'));

        $classAttribute->remove('bar');

        $this->assertEquals('foo baz', $element->getAttribute('class'));

        // with nonexistent class name
        $element = new Element('div', null, [
            'class' => 'foo bar',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals('foo bar', $element->getAttribute('class'));

        $classAttribute->remove('baz');

        $this->assertEquals('foo bar', $element->getAttribute('class'));
    }

    public function testRemoveMultipleWithInvalidClassName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class name must be a string, NULL given.');

        $element = new Element('div', null, [
            'class' => 'foo bar baz',
        ]);

        $classAttribute = new ClassAttribute($element);

        $classAttribute->removeMultiple(['foo', null]);
    }

    public function testRemoveMultiple()
    {
        $element = new Element('div', null, [
            'class' => 'foo bar baz',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals('foo bar baz', $element->getAttribute('class'));

        $classAttribute->removeMultiple(['foo', 'bar']);

        $this->assertEquals('baz', $element->getAttribute('class'));

        // with nonexistent class name
        $element = new Element('div', null, [
            'class' => 'foo bar baz',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals('foo bar baz', $element->getAttribute('class'));

        $classAttribute->removeMultiple(['bar', 'qux']);

        $this->assertEquals('foo baz', $element->getAttribute('class'));
    }

    public function testRemoveAllWithInvalidClassName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class name must be a string, NULL given.');

        $element = new Element('div', null, [
            'class' => 'foo bar baz',
        ]);

        $classAttribute = new ClassAttribute($element);

        $classAttribute->removeAll(['foo', null]);
    }

    public function testRemoveAll()
    {
        $element = new Element('div', null, [
            'class' => 'foo bar baz',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals('foo bar baz', $element->getAttribute('class'));

        $classAttribute->removeAll();

        $this->assertEquals('', $element->getAttribute('class'));
    }

    public function testRemoveAllWithExclusions()
    {
        $element = new Element('div', null, [
            'class' => 'foo bar baz',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals('foo bar baz', $element->getAttribute('class'));

        $classAttribute->removeAll(['bar']);

        $this->assertEquals('bar', $element->getAttribute('class'));

        // with nonexistent class name
        $element = new Element('div', null, [
            'class' => 'foo bar baz',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertEquals('foo bar baz', $element->getAttribute('class'));

        $classAttribute->removeAll(['bar', 'qux']);

        $this->assertEquals('bar', $element->getAttribute('class'));
    }

    public function testGetElement()
    {
        $element = new Element('div', null, [
            'class' => 'foo bar baz',
        ]);

        $classAttribute = new ClassAttribute($element);

        $this->assertSame($element, $classAttribute->getElement());
    }
}
