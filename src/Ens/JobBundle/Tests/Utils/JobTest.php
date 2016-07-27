<?php 

namespace Ens\JobBundle\Tests\Utils;
use Ens\JobBundle\Utils\Job as Jobs;
 
class JobTest extends \PHPUnit_Framework_TestCase
{
  public function testSlugify()
  {
  	$this->assertEquals('n-a', Jobs::slugify(''));
  	$this->assertEquals('n-a', Jobs::slugify(' - '));

    $this->assertEquals('sensio', Jobs::slugify('Sensio'));
    $this->assertEquals('sensio-labs', Jobs::slugify('sensio labs'));
    $this->assertEquals('sensio-labs', Jobs::slugify('sensio   labs'));
    $this->assertEquals('paris-france', Jobs::slugify('paris,france'));
    $this->assertEquals('sensio', Jobs::slugify('  sensio'));
    $this->assertEquals('sensio', Jobs::slugify('sensio  '));
    $this->assertEquals('developpeur-web', Jobs::slugify('Développeur Web'));
  }
}
