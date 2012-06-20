<?php

namespace HtmlTools\Extension\Symfony\Form\DataTransformer;

use HtmlTools\Helpers;
use Symfony\Component\Form\DataTransformerInterface;

class AddHeadingsIdTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        return $value === '' ? null : Helpers::addHeadingsId($value);
    }
}
