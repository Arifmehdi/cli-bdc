<?php

namespace App\Console\Commands\Sitemap;

use App\Models\Blog;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BlogSitemap extends Command
{
    protected $signature = 'blog-sitemap';
    protected $description = 'Generate XML sitemap from blog posts';

    protected $categories = [
        'research' => [1, 2, 3, 4, 5, 6, 7],
        'beyond-car' => [8, 9, 10, 11]
    ];

    // Add this property for the base URL
    protected $baseUrl = 'https://bestdreamcar.com';

    public function handle()
    {
        foreach ($this->categories as $category => $subCategoryIds) {
            $posts = $this->getPostsForCategory($subCategoryIds);
            $xml = $this->generateSitemapXml($posts, $category);
            $this->saveSitemap($xml, $category);
        }

        $this->info('All sitemaps generated successfully');
    }

    protected function getPostsForCategory(array $subCategoryIds)
    {
        return Blog::with(['subcategory' => function($query) {
                $query->select('id', 'name');
            }])
            ->whereIn('sub_category_id', $subCategoryIds)
            ->where('status', 1) // Only published posts
            ->orderBy('updated_at', 'desc')
            ->get(['id', 'sub_category_id', 'slug', 'updated_at']);
    }

    protected function generateSitemapXml($posts, string $category)
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" />');

        // Add homepage - using the baseUrl property
        $this->addUrlToXml($xml, $this->baseUrl . '/', now()->format('Y-m-d'), 'daily', '1.0');

        // Add blog posts
        foreach ($posts as $post) {
            if (!$post->subcategory) continue;

            // Special handling for 'research' category
            $categoryPath = ($category === 'research') ? 'research-review' : Str::slug($category);
            $urlPath = $categoryPath . '/' . Str::slug($post->subcategory->name) . '/' . $post->slug;

            $this->addUrlToXml(
                $xml,
                $this->baseUrl . '/' . $urlPath,
                $post->updated_at->format('Y-m-d'),
                'weekly',
                '0.8'
            );
        }

        return $xml->asXML();
    }

    protected function addUrlToXml(\SimpleXMLElement $xml, string $url, string $lastmod, string $changefreq, string $priority)
    {
        $urlNode = $xml->addChild('url');
        $urlNode->addChild('loc', htmlspecialchars($url, ENT_XML1));
        $urlNode->addChild('lastmod', $lastmod);
        $urlNode->addChild('changefreq', $changefreq);
        $urlNode->addChild('priority', $priority);
    }

    protected function saveSitemap(string $xml, string $category)
    {
        $filename = "sitemap-{$category}.xml";
        $directory = public_path("blog/{$category}");
        $path = "{$directory}/{$filename}";

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Format XML with proper indentation
        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml);

        file_put_contents($path, $dom->saveXML());
        $this->info("Sitemap generated for {$category} at: blog/{$category}/{$filename}");
    }
}
