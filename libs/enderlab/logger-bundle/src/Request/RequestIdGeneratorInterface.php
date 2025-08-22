<?php

namespace EnderLab\LoggerBundle\Request;

interface RequestIdGeneratorInterface
{
    public function generate(): string;
}
