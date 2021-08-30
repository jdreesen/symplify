<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\PhpNodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\ValueObject\AttributeKey;

final class LatteVariableCollectingNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private $userVariableNames = [];

    public function enterNode(Node $node): Node|null
    {
        if (! $node instanceof Variable) {
            return null;
        }

        if ($this->isGeneratedVariable($node)) {
            return null;
        }

        if ($node->name instanceof Expr) {
            return null;
        }

        // system one → skip
        if (in_array($node->name, ['this', 'iterations', 'ʟ_l', 'ʟ_v'], true)) {
            return null;
        }

        $this->userVariableNames[] = $node->name;
        return null;
    }

    /**
     * @return string[]
     */
    public function getUsedVariableNames(): array
    {
        return $this->userVariableNames;
    }

    /**
     * Is this variable generated by latte functions/macros?
     */
    private function isGeneratedVariable(Variable $variable): bool
    {
        $parentNode = $variable->getAttribute(AttributeKey::PARENT);
        if (! $parentNode instanceof Foreach_) {
            return false;
        }

        if ($parentNode->keyVar === $variable) {
            return true;
        }

        return $parentNode->valueVar === $variable;
    }
}