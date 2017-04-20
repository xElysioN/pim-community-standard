<?php

namespace Acme\Bundle\CustomBundle\ActionApplier;

use Akeneo\Bundle\RuleEngineBundle\Model\ActionInterface;
use Acme\Bundle\CustomBundle\Model\ProductPatternAction;
use Akeneo\Component\RuleEngine\ActionApplier\ActionApplierInterface;
use Akeneo\Component\StorageUtils\Updater\PropertySetterInterface;

class PatternActionApplier implements ActionApplierInterface
{
    /** @var PropertySetterInterface */
    protected $propertySetter;

    /**
     * @param PropertySetterInterface $propertySetter
     */
    public function __construct(PropertySetterInterface $propertySetter)
    {
        $this->propertySetter = $propertySetter;
    }

    /**
     * {@inheritdoc}
     */
    public function applyAction(ActionInterface $action, array $products = [])
    {
        $attributes = $action->getAttributes();
        $pattern    = $action->getPattern();

        foreach ($products as $product) {
            $result = $pattern;

            foreach ($attributes as $attributeCode) {
                $value = $product->getValue($attributeCode);

                $content = null === $value ? '' : (string) $value;
                $result = str_replace('%%' . $attributeCode . '%%', $content, $result);
            }

            $this->propertySetter->setData(
                $product,
                $action->getField(),
                $result,
                $action->getOptions()
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ActionInterface $action)
    {
        return $action instanceof ProductPatternAction;
    }
}