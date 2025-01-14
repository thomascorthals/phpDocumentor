<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Guides\References;

use phpDocumentor\Guides\Meta\Entry;
use phpDocumentor\Guides\RenderContext;

class Resolver
{
    /**
     * @param string[] $attributes
     */
    public function resolve(
        RenderContext $environment,
        string $data,
        array $attributes = []
    ): ?ResolvedReference {
        $resolvedFileReference = $this->resolveFileReference($environment, $data, $attributes);

        if ($resolvedFileReference !== null) {
            return $resolvedFileReference;
        }

        $resolvedAnchorReference = $this->resolveAnchorReference($environment, $data, $attributes);

        if ($resolvedAnchorReference !== null) {
            return $resolvedAnchorReference;
        }

        return null;
    }

    /**
     * @param string[] $attributes
     */
    private function resolveFileReference(
        RenderContext $environment,
        string $url,
        array $attributes = []
    ): ?ResolvedReference {
        $entry = null;
        $file = $environment->canonicalUrl($url);

        if ($file !== null) {
            $entry = $environment->getMetas()->get($file);
        }

        if ($entry === null) {
            return null;
        }

        return $this->createResolvedReference($file, $environment, $entry, $attributes);
    }

    /**
     * @param string[] $attributes
     */
    private function resolveAnchorReference(
        RenderContext $environment,
        string $data,
        array $attributes = []
    ): ?ResolvedReference {
        $entry = $environment->getMetas()->findLinkMetaEntry($data);

        if ($entry !== null) {
            return $this->createResolvedReference($entry->getFile(), $environment, $entry, $attributes, $data);
        }

        return null;
    }

    /**
     * @param string[] $attributes
     */
    private function createResolvedReference(
        ?string $file,
        RenderContext $environment,
        Entry $entry,
        array $attributes = [],
        ?string $anchor = null
    ): ResolvedReference {
        $url = $entry->getUrl();

        if ($url !== '') {
            $url = $environment->relativeUrl('/' . $url) . ($anchor !== null ? '#' . $anchor : '');
        }

        return new ResolvedReference(
            $file,
            $entry->getTitle(),
            $url,
            $entry->getTitles(),
            $attributes
        );
    }
}
