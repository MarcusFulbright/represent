Introduction
===========
Represent provides a serialization layer that can create and configure different representations of resources. When implementing a web service, having dedicated layer of that service control the representation of resources will provide a high degree of flexibility. In the context of a RESTful web service, REST embodies a set of design constraints. Two concepts lie underneath the technologies that have evolved from the design constraints of REST. Resources, and Representations.

* Resources: Data models usually compose the resources offered by a web service. In a broader sense, a resource is anything important enough to be referred to as a thing in and of itself. For a RESTful web service, URI's describe resources. GET /car/1 described a car resource.
* Representations: A representation is any machine readable document that contains any information about a resource. For RESTful services, a representation should also explain the current state of the resource.

Creating different representations of resources can be as simple as letting a client decide what format it would like to receive. Or it can get more complicated, such as controlling what properties of a resource should get included in a response. Represent packages all of this functionality together.


Serializers and Builders
=========================
Your application should interact with Represent's serializers. Currently, a GenericSerializer and a HalSerializer exist. These classes handle preparing a specific representation of a given object and returning the data in a serialized format (currently JSON only). These serializers use builders to construct the desired representation.
The most important Builder is the GenericRepresentation Builder. This is the entry point for all representations. The GenericBuilder handles parsing out the configuration for a class (currently given in annotations) and creates a representation composed of stdClass objects, arrays, and primitive type values. Format specific builders, such as the HalBuilder, take a Generic Representation and modify it comply with a specific format's standards. In the case of Hal, handling _links and _embedded.
You can interact with the builders directly, however, only interacting with the serializers can reduce complexity in your application and help keep the representation layer separated.

Generic Configuration
=====================
Configuration that always gets used to create a Generic Representation. All of these options will work with any specific format builder. Currently, Represent only supports class configuration with annotations. All examples will show annotations.

Class Level
-----------
The following annotations function at the class level:

### Exclusion Policy
This gets set at the class level and can either be `whiteList` or `blackList`. This tells The GenericRepresentationBuilder what properties to either include or exclude.
 * `whiteList`: all properties included by default unless they are marked `hidden`
 * `blackList`: all properties excluded by default unless they are marked with `show`

 NOTE: if an exclusion policy is not specified, `whiteList` is used by default

Example:

```php
use Represent\Annotations as Represent;

/**
 * @Represent\ExclusionPolicy(policy="whiteList")
 */
class Car
```

Property Level
--------------
The following annotations function at the property level:

### Hide
Only used when a class's exclusion policy is set to `whiteList`. Can be used to hide a property from all representations.

Example (excludes vinNumber):

```php
use Represent\Annotations as Represent;

/**
 * @Represent\ExclusionPolicy(policy="whiteList")
 */
 class Car
 {
     public $make;

     public $model;

     public $dateBuilt;

     /**
      * @Represent\Hide()
      */
     private $vinNumber;
 }
```

### Show
Only used when a class's exclusion policy is set to `blackList`. Can be used to show properties in representations.

Example (shows make and model):

```php
use Represent\Annotations as Represent

 /**
  * @Represent\ExclusionPolicy(policy="blackList")
  */
 class Car
 {
     /**
      * @Represent\Show()
      */
     public $make;

     /**
      * @Represent\Show()
      */
     public $model;

     public $dateBuilt;

     private $vinNumber;
 }
 ```

### Property
 Used to control the serialized name and data type of the value.

 Example (renames `dateBuilt` to `year` and forces it to integer:

 ```php
use Represent\Annotations as Represent

class Car
{
    public $make;

    public $model;

    /**
     * @Represent\Property(name="year", type="integer")
     */
    public $dateBuilt;

    private $vinNumber
}
 ```

### View
Used to control specific views of resources. A view of a resource is like a particular context that can get represented. Anything excluded via the exclusion policy will get excluded. Properties that do not have a view will always show up, if the exclusion policy allows. Properties with a view will only show up when that view is represented.

Example (the `vinNumber` will only represented in the `owner` view):

```php
use Represent\Annotations as Represent

class Car
{
    public $make;

    public $model;

    public $dateBuilt;

    /**
     * @Represent\View(name={"owner"})
     */
    private $vinNumber;
}
```


Hal Configuration
=================
The following configuration options only work with the Hal Builder and configure options specific to the Hal Format (_links and _embedded).

Class Level
------------
The following annotations function at the class level:

### Link & LinkCollection
LinkCollection the container for all of the Links that should be included in `_links`. Currently links are all generated using the SymfonyUrlGenerator. The links `uri` property takes a route name. Parameters can be defined with expressions using The Expression Language

Example yields the following json:

```php
use Represent\Annotations as Represent;

/**
 * @Represent\LinkCollection(links={
 *    @Represent\Link(
 *         name="self",
 *         uri="get_car_by_ID",
 *         parameters={"id" = "expr('object->id')" }
 *     )
 * })
 */
class Car
{
    public $id;

    public $make;

    public $model;
}
```

```json
{
  "id":1,
  "make":"dodge",
  "model":"neon",
  "_links":{
    "self":{
      "href":"www.mysite.com/car/1"
    }
  }
}
```

Property Level
--------------
The following annotations function at the property level:

### Embedded
 Causes properties to show up in `_embedded` and NOT as properties on the object itself.

 Example yields the following json:

 ```php
use Represent\Annotations as Represent

class Car
{
     /*
      * @Represent\Embedded()
      */
     public $type

     public $dateBuilt;
}

class Type
{
     public $make;

     public $model;
}
```
```json
{
    "dateBuilt":"1989",
    "_embedded":{
        "type": {
            "make":"Dodge",
            "model":"Neon"
        }
    }
}
```

Full Annotation Reference
==========================

#### @ExclusionPolicy
```php
use Represent\Annotations as Represent;

/**
 * @Represent\ExclusionPolicy(policy="whiteList")
 */
class Car
```

| Property | Required | Content                      |
|----------|----------|------------------------------|
| policy   | Yes      | ('whiteList'|'blackList')    |


#### @Hide
```php
use Represent\Annotations as Represent;

/**
 * @Represent\ExclusionPolicy(policy="whiteList")
 */
 class Car
 {
     /**
      * @Represent\Hide()
      */
     private $vinNumber;
 }
```
| Property | Required | Content |
|----------|----------|---------|
| n/a      |   n/a    |  n/a    |


#### @Show
```php
use Represent\Annotations as Represent

 /**
  * @Represent\ExclusionPolicy(policy="blackList")
  */
 class Car
 {
     /**
      * @Represent\Show()
      */
     public $make;
 }
```
| Property | Required | Content |
|----------|----------|---------|
| n/a      |   n/a    |  n/a    |


#### @Property

 ```php
use Represent\Annotations as Represent

class Car
{
    public $make;

    public $model;

    /**
     * @Represent\Property(name="year", type="integer")
     */
    public $dateBuilt;

    private $vinNumber
}
```

| Property | Required |   Content                                      |
|----------|----------|------------------------------------------------|
|  Name    |   No     | string                                         |
|  Type    |   No     | string (integer | string | boolean | datetime) |


#### @View

```php
use Represent\Annotations as Represent

class Car
{
    public $make;

    public $model;

    public $dateBuilt;

    /**
     * @Represent\View(name={"owner"})
     */
    private $vinNumber;
}
```

| Property | Required | Content        |
|----------|----------|----------------|
|  name    | Yes      | array (strings)|


#### @LinkCollection && @Link

```php
use Represent\Annotations as Represent;

/**
 * @Represent\LinkCollection(links={
 *    @Represent\Link(
 *         name="self",
 *         uri="get_car_by_ID",
 *         parameters={"id" = "expr('object->id')" }
 *     )
 * })
 */
class Car
{
    public $id;

    public $make;

    public $model;
}
```
**@LinkCollection**

| Property | Required | Content     |
|----------|----------|-------------|
| links    | Yes      | Array<Link> |

**@Link**

| Property   |  Required  | Content            |
|------------|------------|--------------------|
| name       |   yes      |  string            |
| uri        |   yes      |  string            |
| parameters |   no       |  array (key,value) |
| views      |   no       |  array (string)    |
| absolute   |   no       |  boolean           |


#### @Embedded

 ```php
use Represent\Annotations as Represent

class Car
{
     /*
      * @Represent\Embedded()
      */
     public $type

     public $dateBuilt;
}
```

| Property | Required | Content |
|----------|----------|---------|
|  n/a     |  n/a     |  n/a    |




