<?php

namespace App;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

class DateRange
{
    private ?CarbonInterface $from;

    private ?CarbonInterface $to;

    public function __construct($from = null, $to = null, $fromBound = "[", $toBound = "]")
    {
        $from = ($from === '') ? $from = null : $from;
        $to = ($to === '') ? $to = null : $to;
        
        $this->from = is_string($from) ? $this->parseFrom($from) : $from;
        $this->to = is_string($to) ? $this->parseTo($to) : $to;

        // when exclusive bound is set,
        // let's canonicalize it to inclusive bounds
        if ($fromBound === '(') {
            $this->from = optional($this->from)->addDay();
        }

        if ($toBound === ')') {
            $this->to = optional($this->to)->subDay();
        }
    }

    private function parseFrom(string $from) : CarbonInterface
    {
        return CarbonImmutable::parse($from);
    }

    private function parseTo(string $to) : CarbonInterface
    {
        return CarbonImmutable::parse($to);
    }

    public function from(): ?CarbonInterface
    {
        return $this->from;
    }

    public function to(): ?CarbonInterface
    {
        return $this->to;
    }
}
