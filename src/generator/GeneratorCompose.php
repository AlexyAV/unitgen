<?php

namespace Unitgen\generator;

/**
 * Class GeneratorCompose
 *
 * @package Unitgen\generator
 */
class GeneratorCompose implements GeneratorInterface
{
    /**
     * @var GeneratorInterface[] List of generators to compose
     */
    private $generator;

    /**
     * @param GeneratorInterface $generator
     *
     * @return bool
     */
    public function addGenerator(GeneratorInterface $generator)
    {
        if ($generator instanceof self) {
            return false;
        }

        $this->generator[] = $generator;

        return true;
    }

    /**
     * @return GeneratorInterface[]
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * Run through all generators to generate test file source.
     *
     * @return string
     */
    public function generate()
    {
        $testSource = '';

        foreach ($this->generator as $generator) {

            $testSource .= $generator->generate();
        }

        return $testSource . '}';
    }
}