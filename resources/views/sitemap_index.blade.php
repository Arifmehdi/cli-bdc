<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($urls as $url)
        <sitemap>
            <loc>{{ $url['url'] }}</loc>
            <lastmod>{{ $url['lastmod'] }}</lastmod>
        </sitemap>
    @endforeach
</sitemapindex>