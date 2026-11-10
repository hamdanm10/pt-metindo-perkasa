<?php

declare(strict_types=1);

namespace App\Services\Articles;

/**
 * A few sample articles and news items, written in English, for reviewing the landing pages while
 * the dashboard has published nothing. They exist only in sample mode (`landing.sample_content`)
 * and only while the language has no published article. They are general plating knowledge or
 * plain statements about this website, never claims about the company, and every page that shows
 * them says that they are samples. All of them use the one image the client supplied.
 *
 * @phpstan-import-type Summary from LandingArticles
 */
class SampleArticles
{
    public const IMAGE = 'images/landing/articles/sample-business.webp';

    /**
     * Newest first.
     *
     * @return list<Summary>
     */
    public function summaries(): array
    {
        return array_map(fn (array $item): array => $item['summary'], $this->all());
    }

    /**
     * @return array{summary: Summary, body_html: string}|null
     */
    public function find(string $slug): ?array
    {
        foreach ($this->all() as $item) {
            if ($item['summary']['slug'] === $slug) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @return list<array{summary: Summary, body_html: string}>
     */
    private function all(): array
    {
        $items = [];

        foreach ($this->definitions() as $days => [$slug, $type, $title, $excerpt, $body]) {
            $items[] = [
                'summary' => [
                    'slug' => $slug,
                    'type' => $type,
                    'title' => $title,
                    'excerpt' => $excerpt,
                    'date' => now()->subDays($days)->toDateString(),
                    'image' => asset(self::IMAGE),
                    'alt' => $title,
                    'minutes' => ArticleBody::readingMinutes($body),
                    'sample' => true,
                ],
                'body_html' => $body,
            ];
        }

        return $items;
    }

    /**
     * Days ago => slug, type, title, excerpt, body.
     *
     * @return array<int, array{string, string, string, string, string}>
     */
    private function definitions(): array
    {
        return [
            2 => ['zinc-plating-basics', 'article', 'Zinc plating basics: how a thin layer protects steel',
                'A few micrometres of zinc can keep a steel part from rusting for years. Here is how the layer works and what keeps it even.',
                '<p>Steel rusts when oxygen and moisture reach its surface. Zinc plating puts a thin layer of a more reactive metal between the steel and the air, so the zinc corrodes first and the part underneath stays intact.</p>'
                .'<h2>Two ways the layer protects</h2>'
                .'<p>The coating works as a barrier: while it covers the steel, water and oxygen cannot reach it. If a scratch exposes the steel, the zinc around the scratch still corrodes in its place. This is called sacrificial protection, and it is why plated parts hold up at cut edges and threads.</p>'
                .'<h2>What keeps the coating even</h2>'
                .'<ul><li><strong>Bath chemistry.</strong> The concentration of zinc and of the additives has to stay within its range.</li><li><strong>Temperature and current density.</strong> Both change how fast the zinc deposits and how fine its grain is.</li><li><strong>Surface preparation.</strong> Oil, scale or rust left on the part shows up as patches where the zinc does not hold.</li></ul>'
                .'<blockquote>A coating is only as consistent as the process that produced it.</blockquote>'
                .'<h2>After plating</h2>'
                .'<p>A passivation step on top of the zinc slows corrosion further and sets the final colour, for example clear, yellow or black. Which finish suits a part depends on where it will be used and how long it has to last.</p>'],
            9 => ['rack-or-barrel-plating', 'article', 'Rack or barrel plating: which fits your parts?',
                'The way parts are held in the bath changes the finish, the cost and the parts that suit it. A short guide to choosing.',
                '<p>Before a part can be plated it has to be held in the bath and connected to the current. There are two common ways to do it, and the choice shapes the result more than most people expect.</p>'
                .'<h2>Rack plating</h2>'
                .'<p>Each part is hung on a rack, so it is held in a fixed position and the coating builds up evenly over the surface that faces the anode. It suits large, fragile or shape-critical parts, and parts whose appearance matters. The trade-off is a small contact mark where the part hangs, and a higher cost per piece.</p>'
                .'<h2>Barrel plating</h2>'
                .'<p>Many small parts are loaded into a perforated barrel that rotates in the bath. It is efficient for volume, and it is the usual choice for screws, nuts, washers and similar parts. Because the parts tumble against each other, it is not suited to parts that scratch or tangle easily.</p>'
                .'<h2>Questions that decide it</h2>'
                .'<ol><li>How large is the part, and can it survive tumbling?</li><li>How many pieces are there, and how often will they be ordered?</li><li>Does the finish have to look the same on every surface?</li></ol>'
                .'<p>If you are not sure, describe the part and the quantity, and ask for a recommendation before the first batch.</p>'],
            16 => ['process-catalogue-online', 'news', 'Our process catalogue is now online',
                'Browse the plating process categories we work with, from zinc and nickel to anodizing and wastewater treatment.',
                '<p>The Processes page of this website now lists every process category in one place. Choose a category on the left and its picture, description and processes appear on the right.</p>'
                .'<h2>What you will find</h2>'
                .'<ul><li>Each category with a short description.</li><li>The processes that belong to it, listed one by one.</li><li>A link to the page of the category with the full list.</li></ul>'
                .'<p>If you cannot find the process you need, tell us the substrate and the finish you are after, and our team will point you to the closest option.</p>'],
            24 => ['prepare-your-plating-quote', 'article', 'Five things to prepare before asking for a plating quote',
                'A clear brief gets you a clear answer. These details help a plating team recommend the right process faster.',
                '<p>The quickest way to get a useful answer is to send the details that decide the process. Most of them are already on your drawing or in your specification.</p>'
                .'<h2>The brief</h2>'
                .'<ol><li><strong>The base material.</strong> Steel, stainless steel, copper alloys, aluminium and plastics each need a different preparation.</li><li><strong>Size and shape.</strong> A drawing or a clear photo shows whether the part can be racked or barrelled.</li><li><strong>The coating you need.</strong> Name the metal and, if you have one, the thickness.</li><li><strong>The requirement it has to meet.</strong> For example a corrosion test, an appearance standard or a customer specification.</li><li><strong>Quantity and timing.</strong> One sample, a trial batch or a regular order are planned differently.</li></ol>'
                .'<blockquote>Tell us the process and the substrate, and we will walk you through the options.</blockquote>'
                .'<p>If one of these is missing, send what you have. A short conversation is usually enough to fill the gaps.</p>'],
            33 => ['reach-us-on-whatsapp', 'news', 'Reach our customer service on WhatsApp',
                'A button on every page of this website lists our customer service numbers and answers common questions.',
                '<p>Two round buttons now sit at the bottom right of every page of this website.</p>'
                .'<h2>WhatsApp</h2>'
                .'<p>The green button opens a list of our customer service numbers. Choose one and a chat opens with a greeting already typed, so you only have to describe what you need.</p>'
                .'<h2>The chat assistant</h2>'
                .'<p>The red button opens an assistant that answers the most common questions: our processes, our services, how to contact us, our opening hours and where we are. Tap a topic to see the answer, and the assistant can hand you over to customer service at any time.</p>'],
        ];
    }
}
