<?php

declare(strict_types=1);

namespace Mathrix\Standard\Tests;

use PHPUnit\Framework\TestCase;
use function dirname;
use function file_get_contents;
use function preg_match_all;
use function preg_quote;
use function shell_exec;
use function sort;
use function strpos;

class RulesInclusionTest extends TestCase
{
    private const STANDARDS = ['PSR12', 'SlevomatCodingStandard'];

    /** @var string The standard content. */
    private string $standard = '';

    /**
     * @return string[][] The rules dataset.
     */
    public function createRulesDataset(): array
    {
        $phpcs = dirname(__DIR__) . '/vendor/bin/phpcs';
        $rules = [];

        foreach (self::STANDARDS as $standard) {
            $output  = shell_exec("{$phpcs} -e --standard={$standard}");
            $pattern = '/(' . preg_quote($standard, '/') . '\..*)$/m';
            preg_match_all($pattern, $output, $matches);

            if (isset($matches[0])) {
                $rules = [...$rules, ...$matches[0]];
            }
        }

        sort($rules);

        $dataset = [];

        foreach ($rules as $rule) {
            $dataset[$rule] = [$rule];
        }

        return $dataset;
    }

    /**
     * @testdox sees the rule $rule in the standard
     * @dataProvider createRulesDataset
     *
     * @param string $rule The rule to check.
     */
    public function testRulePresent(string $rule): void
    {
        if (!$this->standard) {
            $this->standard = file_get_contents(dirname(__DIR__) . '/Mathrix/ruleset.xml');
        }

        $this->assertNotFalse(strpos($this->standard, $rule), 'Missing ' . $rule . ' from the standard');
    }
}
