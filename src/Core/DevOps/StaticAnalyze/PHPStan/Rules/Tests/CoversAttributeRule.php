<?php declare(strict_types=1);

namespace Shopware\Core\DevOps\StaticAnalyze\PHPStan\Rules\Tests;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\CoversNothing;
use Shopware\Core\Framework\Log\Package;

/**
 * @internal
 *
 * @implements Rule<InClassNode>
 */
#[Package('core')]
class CoversAttributeRule implements Rule
{
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     *
     * @return array<array-key, RuleError|string>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->hasCovers($node)) {
            return [];
        }

        if (TestRuleHelper::isUnitTestClass($node->getClassReflection())) {
            return [
                RuleErrorBuilder::message('Unit test classes must have CoversClass, CoversFunction or CoversNothing attribute')
                    ->identifier('shopware.testCovers')
                    ->build(),
            ];
        }

        return [];
    }

    private function hasCovers(InClassNode $class): bool
    {
        foreach ($class->getOriginalNode()->attrGroups as $group) {
            $name = $group->attrs[0]->name;

            if (\in_array($name->toString(), [CoversClass::class, CoversFunction::class, CoversNothing::class], true)) {
                return true;
            }
        }

        return false;
    }
}
