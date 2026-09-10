<?php

namespace Tests;

use Illuminate\Support\Facades\Validator;
use Pecotamic\Redirect\Blueprints\RedirectBlueprint;
use PHPUnit\Framework\Attributes\DataProvider;

class TargetFieldValidationTest extends TestCase
{
    #[DataProvider('validTargets')]
    public function test_it_accepts_valid_targets(int $responseCode, ?string $target): void
    {
        $this->assertFalse($this->validate($responseCode, $target)->fails());
    }

    #[DataProvider('invalidTargets')]
    public function test_it_rejects_invalid_targets(int $responseCode, ?string $target): void
    {
        $this->assertTrue($this->validate($responseCode, $target)->fails());
    }

    public static function validTargets(): array
    {
        return [
            'relative path for 301' => [301, '/new-page'],
            'absolute https url for 302' => [302, 'https://example.com/page'],
            'absolute http url for 301' => [301, 'http://example.com'],
            'empty target for a 404 abort' => [404, null],
            'empty target for a 403 abort' => [403, null],
        ];
    }

    public static function invalidTargets(): array
    {
        return [
            'missing target for 301' => [301, null],
            'missing target for 302' => [302, null],
            'url without scheme' => [301, 'example.com/page'],
            'javascript scheme' => [301, 'javascript:alert(1)'],
        ];
    }

    private function validate(int $responseCode, ?string $target)
    {
        return Validator::make(
            ['response_code' => $responseCode, 'target' => $target],
            ['target' => $this->targetFieldRules()]
        );
    }

    private function targetFieldRules(): array
    {
        return $this->findFieldConfig(RedirectBlueprint::make()->contents(), 'target')['validate'];
    }

    private function findFieldConfig(array $node, string $handle): ?array
    {
        if (($node['handle'] ?? null) === $handle && isset($node['field'])) {
            return $node['field'];
        }

        foreach ($node as $value) {
            if (is_array($value) && ($result = $this->findFieldConfig($value, $handle))) {
                return $result;
            }
        }

        return null;
    }
}
