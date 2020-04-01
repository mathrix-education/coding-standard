<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class IntegrityTest extends TestCase
{
    private const STANDARDS = ['PSR12', 'SlevomatCodingStandard'];
    private string $standard = '';

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

        $dataset = [];

        foreach ($rules as $rule) {
            $dataset[$rule] = [$rule];
        }

        return $dataset;
    }

    /**
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
