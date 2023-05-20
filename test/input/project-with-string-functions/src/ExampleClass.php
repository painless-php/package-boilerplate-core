<?php

namespace ProjectWithEnum;

class ExampleClass
{
    public function doStuff()
    {
        // require php 8
        str_starts_with('foo', 'bar');

        // require mbstring extension
        mb_strlen('foo');
    }
}
