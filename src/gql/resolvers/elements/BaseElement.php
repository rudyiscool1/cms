<?php
namespace craft\gql\resolvers\elements;

use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\gql\resolvers\BaseResolver;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Class BaseElement
 */
abstract class BaseElement extends BaseResolver
{
    /**
     * @inheritdoc
     */
    public static function getArrayableArguments(): array
    {
        return array_merge(parent::getArrayableArguments(), [
            'siteId',
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function resolve($source, array $arguments, $context, ResolveInfo $resolveInfo)
    {
        $arguments = self::prepareArguments($arguments);
        $fieldName = $resolveInfo->fieldName;

        $query = static::prepareQuery($source, $arguments, $fieldName);

        // If that's already preloaded, then, uhh, skip the preloading?
        if (is_array($query)) {
            return $query;
        }

        /** @var ElementQuery $query */
        $preload = self::extractEagerLoadCondition($resolveInfo);
        return $query->with($preload)->all();
    }

    /**
     * Prepare an element Query based on the source, arguments and the field name on the source.
     *
     * @param mixed $source The source. Null if top-level field being resolved.
     * @param array $arguments Arguments to apply to the query.
     * @param null $fieldName Field name to resolve on the source, if not a top-level resolution.
     *
     * @return mixed
     */
    abstract protected static function prepareQuery($source, array $arguments, $fieldName = null);
}
