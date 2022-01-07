<?php

declare(strict_types=1);

namespace KickflipDocs\View\Markdown;

use Kickflip\KickflipHelper;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalink;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContents;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContentsPlaceholder;
use League\CommonMark\Extension\TableOfContents\TableOfContentsGenerator;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\NodeIterator;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;
use League\Config\Exception\InvalidConfigurationException;

/**
 * @see \League\CommonMark\Extension\TableOfContents\TableOfContentsBuilder
 */
final class TableOfContentsBuilder implements ConfigurationAwareInterface
{
    public const POSITION_TOP = 'top';
    public const POSITION_BEFORE_HEADINGS = 'before-headings';
    public const POSITION_PLACEHOLDER = 'placeholder';

    /** @psalm-readonly-allow-private-mutation */
    private ConfigurationInterface $config;

    public function onDocumentParsed(DocumentParsedEvent $event): void
    {
        $document = $event->getDocument();
        $toc = $this->getTableOfContentsGenerator()->generate($document);
        if ($toc === null) {
            // No linkable headers exist, so no TOC could be generated
            return;
        }

        // Add custom CSS class(es), if defined
        $class = $this->config->get('table_of_contents/html_class');
        if ($class !== null) {
            $toc->data->append('attributes/class', $class);
        }

        // Register TOC as global
        $kickflipConfig = KickflipHelper::config();
        $kickflipConfig->set('pageToc', clone $toc);

        // Add the TOC to the Document
        $position = $this->config->get('table_of_contents/position');
        match ($position) {
            self::POSITION_TOP => $document->prependChild($toc),
            self::POSITION_BEFORE_HEADINGS => self::insertBeforeFirstLinkedHeading($document, $toc),
            self::POSITION_PLACEHOLDER => self::replacePlaceholders($document, $toc),
            default => throw InvalidConfigurationException::forConfigOption('table_of_contents/position', $position),
        };
    }

    private static function insertBeforeFirstLinkedHeading(Document $document, TableOfContents $toc): void
    {
        foreach ($document->iterator(NodeIterator::FLAG_BLOCKS_ONLY) as $node) {
            if (! $node instanceof Heading) {
                continue;
            }

            foreach ($node->children() as $child) {
                if ($child instanceof HeadingPermalink) {
                    $node->insertBefore($toc);

                    return;
                }
            }
        }
    }

    private static function replacePlaceholders(Document $document, TableOfContents $toc): void
    {
        $nodeIterator = $document->iterator(NodeIterator::FLAG_BLOCKS_ONLY);
        foreach ($nodeIterator as $node) {
            // Add the block once we find a placeholder
            if (! $node instanceof TableOfContentsPlaceholder) {
                continue;
            }

            $node->replaceWith(clone $toc);
        }
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }

    private function getTableOfContentsGenerator(): TableOfContentsGenerator
    {
        return new TableOfContentsGenerator(
            (string) $this->config->get('table_of_contents/style'),
            (string) $this->config->get('table_of_contents/normalize'),
            (int) $this->config->get('table_of_contents/min_heading_level'),
            (int) $this->config->get('table_of_contents/max_heading_level'),
            (string) $this->config->get('heading_permalink/fragment_prefix'),
        );
    }
}
