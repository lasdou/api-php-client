<?php

namespace Akeneo\Pim\ApiClient\Api;

use Akeneo\Pim\ApiClient\Client\ResourceClientInterface;
use Akeneo\Pim\ApiClient\Exception\InvalidArgumentException;
use Akeneo\Pim\ApiClient\Pagination\PageFactoryInterface;
use Akeneo\Pim\ApiClient\Pagination\PageInterface;
use Akeneo\Pim\ApiClient\Pagination\ResourceCursorFactoryInterface;
use Akeneo\Pim\ApiClient\Pagination\ResourceCursorInterface;

/**
 * API implementation to manage the products.
 *
 * @author    Laurent Petard <laurent.petard@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductApi implements ProductApiInterface
{
    public const PRODUCTS_URI = 'api/rest/v1/products';
    public const PRODUCT_URI = 'api/rest/v1/products/%s';

    public function __construct(
        protected ResourceClientInterface $resourceClient,
        protected PageFactoryInterface $pageFactory,
        protected ResourceCursorFactoryInterface $cursorFactory
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $code, array $queryParameters = []): array
    {
        return $this->resourceClient->getResource(static::PRODUCT_URI, [$code], $queryParameters);
    }

    /**
     * {@inheritdoc}
     */
    public function listPerPage(int $limit = 100, bool $withCount = false, array $queryParameters = []): PageInterface
    {
        $data = $this->resourceClient->getResources(static::PRODUCTS_URI, [], $limit, $withCount, $queryParameters);

        return $this->pageFactory->createPage($data);
    }

    /**
     * {@inheritdoc}
     */
    public function all(int $pageSize = 100, array $queryParameters = []): ResourceCursorInterface
    {
        $queryParameters['pagination_type'] = 'search_after';

        $firstPage = $this->listPerPage($pageSize, false, $queryParameters);

        return $this->cursorFactory->createCursor($pageSize, $firstPage);
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $code, array $data = []): int
    {
        if (array_key_exists('identifier', $data)) {
            throw new InvalidArgumentException('The parameter "identifier" should not be defined in the data parameter');
        }

        $data['identifier'] = $code;

        return $this->resourceClient->createResource(static::PRODUCTS_URI, [], $data);
    }

    /**
     * {@inheritdoc}
     */
    public function upsert(string $code, array $data = []): int
    {
        return $this->resourceClient->upsertResource(static::PRODUCT_URI, [$code], $data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $code): int
    {
        return $this->resourceClient->deleteResource(static::PRODUCT_URI, [$code]);
    }

    /**
     * {@inheritdoc}
     */
    public function upsertList($resources): \Traversable
    {
        return $this->resourceClient->upsertStreamResourceList(static::PRODUCTS_URI, [], $resources);
    }
}
