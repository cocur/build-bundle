<?php

namespace Bc\Bundle\StaticSiteBundle\Writer;

interface WriterInterface
{
    public function write($name, $content);
}
