# Unitgen
[![Build Status](https://travis-ci.org/AlexyAV/unitgen.svg?branch=master)](https://travis-ci.org/AlexyAV/unitgen)
[![Code Climate](https://codeclimate.com/github/AlexyAV/unitgen/badges/gpa.svg)](https://codeclimate.com/github/AlexyAV/unitgen)
[![Test Coverage](https://codeclimate.com/github/AlexyAV/unitgen/badges/coverage.svg)](https://codeclimate.com/github/AlexyAV/unitgen/coverage)

The generator of the basic structure for the unit tests. The generated classes are based on tests [phpunit](https://phpunit.de) version >= 5.4.*.
Unitgen is is a command line tool that recursively(optional) analyze the specified path and generates unit test files. The directory structure is reproduced according to the original structure.

## Installation

Installation via [Composer](https://getcomposer.org/). Add to your composer.json file.
```
{
    "require-dev": {
        "phpunit/phpunit": "5.4.*",
        "unitgen/unitgen": "dev-master"
    }
}
```

## Examlpe of created test

For source class "SourceClass":
<pre lang="php">
namespace source\class\path;

class SourceClass
{
    /**
     * @throws \Exception
     */
    public function bar()
    {
        ...
    }
}
</pre>
Will be generated next structure within "SourceClassTest":
<pre lang="php">
use PHPUnit\Framework\TestCase;

class SourceClassTest extends TestCase
{
    private $sourceClass;
    
    public function setUp()
    {
        $this->sourceClass = new \source\class\path\SourceClass();
    }
    
    public function testBar()
    {
        "//TODO Implement test for bar method\n";
        $this->assertTrue(false);
    }
    
    public function testBarException()
    {
        "//TODO Implement test for bar method\n";
        $this->expectException(\Exception::class);
    }
    
    public function tearDown()
    {
        "//TODO Implement tearDown method";
    }
}
</pre>
Unitgen generates test method for public method only. Also it looks for annotation exception data and generates appropriate test methods.

NOTE: Unitgen does not affect existing files.

## Configuration
For use unitgen you should specify a configuration file.

Here is an exaple of configuration file with all available options:
<pre lang="php">
return [
    'source'        => [
        'path'    => [], // required
        'exclude' => []
    ],
    'savePath'      => '', // required
    'classExclude'  => [
        'name'      => [], // array of full class names
        'regexp'    => [], // array of reqular expressions
        'parent'    => [], // array of parent classes
        'implement' => [], // array of interfaces
    ],
];
</pre>

| Name | Description | Required | Example | Type |
|--------------|--------------------------------------------------------------------------------------------------------------------------|----------|-------------------------------------------------------------------------------------------------------------------------------|--------|
| path | Specified source path directory. Unitgen will walk throught recursively(optional) and generate corresponding test files. Boolean value specifies to walk recursively. | true | [     'first-source-path'      => true,     'second-source-path' => false ] | array |
| exclude | Directories that will be excluded from source path. | false | ['second-source-path'] | array |
| savePath | Writable path to save generated tests. Must already exist. | true | 'generated-test-path' | string |
| classExclude | Classes that will be excluded from source path. | false | 'name'=> [Examle::class], 'regexp'=> ['/^Controller.*/'], 'parent'=> [\Exception::class],  'implement' => [\Iterator::class], | array |

## Example of usage

```
$ cd "vendor/.../unitget/bin"

$ ./unitgen run "config-path"

Start test generation.
======================

Generation completed successful.
-------------------------
Number of files in source path: 9
Number of generated test classes: 3
-------------------------
Generated in:0.12969493865967
```


