<?php

declare(strict_types=1);

namespace App\Services\Articles;

use InvalidArgumentException;
use Tiptap\Editor;
use Tiptap\Extensions\StarterKit;
use Tiptap\Marks\Link;

/**
 * Turns the editor's JSON into safe HTML. Only the nodes and marks listed here are
 * rendered, so scripts, raw HTML, images, and unsafe link addresses are dropped.
 */
class ArticleBody
{
    /**
     * @return array<string, mixed>
     */
    public function parse(string $json): array
    {
        $data = json_decode($json, true);

        if (! is_array($data) || ($data['type'] ?? null) !== 'doc') {
            throw new InvalidArgumentException('The article body is not a valid document.');
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $document
     */
    public function render(array $document): string
    {
        $editor = new Editor([
            'extensions' => [
                new StarterKit([
                    'heading' => ['levels' => [2, 3]],
                    'code' => false,
                    'codeBlock' => false,
                    'horizontalRule' => false,
                    'strike' => false,
                ]),
                new Link([
                    'HTMLAttributes' => ['rel' => 'noopener noreferrer nofollow'],
                    'allowedProtocols' => ['http', 'https', 'mailto', 'tel'],
                ]),
            ],
        ]);

        return $editor->setContent($document)->getHTML();
    }

    /**
     * @param  array<string, mixed>  $document
     */
    public function isEmpty(array $document): bool
    {
        return trim(strip_tags($this->render($document))) === '';
    }
}
