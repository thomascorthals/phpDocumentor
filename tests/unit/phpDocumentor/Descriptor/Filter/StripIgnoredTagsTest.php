<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Filter;

use phpDocumentor\Configuration\ApiSpecification;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DocBlock;
use phpDocumentor\Descriptor\TagDescriptor;
use PHPUnit\Framework\TestCase;

final class StripIgnoredTagsTest extends TestCase
{
    /** @var StripIgnoredTags */
    private $fixture;

    protected function setUp(): void
    {
        $this->fixture = new StripIgnoredTags();
    }

    public function testIgnoresNonTagDescriptors(): void
    {
        $object = new class implements Filterable {
            public function getName(): string
            {
                return 'someTag';
            }

            public function getDescription(): ?DocBlock\DescriptionDescriptor
            {
                return null;
            }

            public function setErrors(Collection $errors): void
            {
            }
        };

        self::assertSame(
            $object,
            ($this->fixture)(new FilterPayload($object, ApiSpecification::createDefault()))->getFilterable()
        );
    }

    public function testFiltersIgnoredTags(): void
    {
        $object = new TagDescriptor('someTag');

        $apiSpecification = ApiSpecification::createDefault();
        $apiSpecification['ignore-tags'] = ['someTag'];

        self::assertNull(($this->fixture)(new FilterPayload($object, $apiSpecification))->getFilterable());
    }
}
