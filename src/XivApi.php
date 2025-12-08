<?php

declare(strict_types=1);

namespace XivApi;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use XivApi\Client\AssetClient;
use XivApi\Client\MapAssetClient;
use XivApi\Client\SearchClient;
use XivApi\Client\SheetIndexClient;
use XivApi\Client\SheetRowsClient;
use XivApi\Client\VersionClient;
use XivApi\Enums\AssetFormat;
use XivApi\Enums\Language;

/**
 * Main entry point for the XIVAPI client.
 *
 * Provides access to all XIVAPI v2 endpoints. Requires a PSR-18 HTTP client
 * and PSR-17 request factory to be injected.
 */
class XivApi
{
    private const string BASE_URL = 'https://v2.xivapi.com/api/';

    private ?Language $language = null;

    private ?string $gameVersion = null;

    private ?string $schema = null;

    /** @var Language[] */
    private array $localizations = [];

    /**
     * @param  ClientInterface  $http  PSR-18 HTTP client
     * @param  RequestFactoryInterface  $requestFactory  PSR-17 request factory
     */
    public function __construct(
        private readonly ClientInterface $http,
        private readonly RequestFactoryInterface $requestFactory,
    ) {}

    /**
     * Set the default language for all requests.
     */
    public function language(Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Set the default game version for all requests.
     */
    public function gameVersion(string $version): self
    {
        $this->gameVersion = $version;

        return $this;
    }

    /**
     * Set the default schema for all requests.
     */
    public function schema(string $schema): self
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * Configure languages for localized field expansion.
     */
    public function localizations(Language ...$languages): self
    {
        $this->localizations = $languages;

        return $this;
    }

    /**
     * Get the version client.
     */
    public function version(): VersionClient
    {
        return new VersionClient($this->http, $this->requestFactory, self::BASE_URL);
    }

    /**
     * Get the sheet index client for listing all sheets.
     */
    public function sheetIndex(): SheetIndexClient
    {
        return new SheetIndexClient(
            $this->http,
            $this->requestFactory,
            self::BASE_URL,
            $this->gameVersion,
        );
    }

    /**
     * Get a sheet rows client for querying rows from a specific sheet.
     */
    public function sheet(string $name): SheetRowsClient
    {
        return new SheetRowsClient(
            $this->http,
            $this->requestFactory,
            self::BASE_URL,
            $name,
            $this->localizations,
            $this->language,
            $this->gameVersion,
            $this->schema,
        );
    }

    /**
     * Get a search client for searching across sheets.
     */
    public function search(): SearchClient
    {
        return new SearchClient(
            $this->http,
            $this->requestFactory,
            self::BASE_URL,
            $this->localizations,
            $this->language,
            $this->gameVersion,
            $this->schema,
        );
    }

    /**
     * Get an asset client for fetching game assets.
     *
     * @param  string  $path  Game path of the asset (e.g. "ui/icon/051000/051474_hr1.tex")
     * @param  AssetFormat  $format  Output format (png, jpg, webp)
     */
    public function asset(string $path, AssetFormat $format = AssetFormat::Png): AssetClient
    {
        return new AssetClient(
            $this->http,
            $this->requestFactory,
            self::BASE_URL,
            $path,
            $format,
            $this->gameVersion,
        );
    }

    /**
     * Get a map asset client for fetching composed map images.
     *
     * @param  string  $territory  Territory ID (e.g. "s1d1")
     * @param  string  $index  Map index within territory (e.g. "00")
     */
    public function map(string $territory, string $index): MapAssetClient
    {
        return new MapAssetClient(
            $this->http,
            $this->requestFactory,
            self::BASE_URL,
            $territory,
            $index,
            $this->gameVersion,
        );
    }
}
