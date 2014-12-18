<?php

namespace Represent\Tests\Serializer;

use Represent\Instantiator\GenericInstantiator;
use Represent\Serializer\Deserializer;
use Represent\Serializer\DoctrineDeserializer;
use Represent\Test\RepresentTestCase;

class DeserializerTest extends RepresentTestCase
{
    private $adultFieldNames = array(
        'firstName',
        'lastName',
        'publicTest',
        'age',
        'children'
    );

    private $childFieldNames = array(
        'firstName',
        'lastName',
    );

    private $adultMapping = array(
        'fieldName' => 'children',
        'mappedBy'  => 'childid',
        'targetEntity'  => 'Represent\Test\Fixtures\Annotated\Child',
        'cascade'  => array(
            0  => 'persist'
        ),
        'orphanRemoval'  => false,
        'fetch'  => 2,
        'type'  => 4,
        'inversedBy'  => null,
        'isOwningSide'  => false,
        'sourceEntity'  => 'Represent\Test\Fixtures\Annotated\Adult',
        'isCascadeRemove'  => false,
        'isCascadePersist'  => true,
        'isCascadeRefresh'  => false,
        'isCascadeMerge'  => false,
        'isCascadeDetach'  => false
    );

    private $adultClass = 'Represent\Test\Fixtures\Annotated\Adult';

    private $childClass = 'Represent\Test\Fixtures\Annotated\Child';

    public function testFromStdObjectWithCollectionAssociation()
    {
        $associationNames = array('children');

        $metaData = $this->getDoctrineClassMetaMock();
        $metaData->shouldReceive('getFieldNames')->andReturn($this->adultFieldNames, $this->childFieldNames);
        $metaData->shouldReceive('getAssociationNames')->andReturn($associationNames, array());
        $metaData->shouldReceive('getAssociationMapping')->with('children')->andReturn($this->adultMapping);
        $metaData->shouldReceive('isSingleValuedAssociation')->andReturn(false);
        $metaData->shouldReceive('isCollectionValuedAssociation')->andReturn(true);

        $child = new \stdClass();
        $child->firstName = 'child';
        $child->lastName  = 'last';

        $data  = new \stdClass;
        $data->firstName = 'Harry';
        $data->lastName  = 'LockHart';
        $data->age       = 35;
        $data->publicTest = 'test';
        $data->children   = array($child, $child);

        $em = $this->getEntityManagerMock();
        $em->shouldReceive('getClassMetadata')->andReturn($metaData);

        $handler = $this->getPropertyHandlerMock();
        $handler->shouldReceive('propertyTypeOverride')->andReturnNull();
        $handler->shouldReceive('handleTypeConversion')->andReturnUsing(
            function($type, $value) {
                return $value;
            }
        );


        $deSerializer = new DoctrineDeserializer(null, new GenericInstantiator(), $em, $handler);
        $result = $this->getReflectedMethod($deSerializer, 'fromStdObject')->invoke($deSerializer, $data, $this->adultClass);

        $this->assertInstanceOf($this->adultClass, $result);
        $this->assertEquals('Harry', $result->getFirstName());
        $this->assertInstanceof('Doctrine\Common\Collections\ArrayCollection', $result->getChildren());
        $children = $result->getChildren()->toArray();
        $child1 = $children[0];
        $child2 = $children[1];
        $this->assertInstanceOf($this->childClass, $child1);
        $this->assertEquals('child', $child1->getFirstName());
        $this->assertInstanceOf($this->childClass, $child2);

        $this->assertEquals('child', $child2->getFirstName());
    }
}