<?php

class ManipleUserConsents_Filter_RelativizeHrefsTest extends PHPUnit_Framework_TestCase
{
    public function testFilterStatic()
    {
        $_SERVER['HTTP_HOST'] = 'localhost:8000';

        $this->assertEquals(
            '<div><a href="/index.html">Back to home</a></div>',
            ManipleUserConsents_Filter_RelativizeHrefs::filterStatic(
                '<div><a href="http://localhost:8000/index.html">Back to home</a></div>'
            )
        );
    }
}
