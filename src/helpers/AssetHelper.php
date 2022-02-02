<?php

namespace percipiolondon\staff\helpers;

use Cake\Utility\Hash;
use Craft;
use craft\elements\Asset as AssetElement;
use craft\feedme\Plugin;
use craft\helpers\Assets as AssetsHelper;
use craft\helpers\FileHelper;
use craft\helpers\StringHelper;
use craft\helpers\UrlHelper;

class AssetHelper
{
    // Public Methods
    // =========================================================================

    /**
     * @param $srcName
     * @param $dstName
     * @param int $chunkSize
     * @param bool $returnbytes
     * @return bool|int
     */
    public static function downloadFile($srcName, $dstName, $chunkSize = 1, $returnbytes = true)
    {
        $assetDownloadCurl = Plugin::$plugin->getSettings()->assetDownloadCurl;

        // Provide some legacy support
        if ($assetDownloadCurl) {
            $ch = curl_init($srcName);
            $fp = fopen($dstName, 'wb');

            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

            curl_exec($ch);
            curl_close($ch);

            return fclose($fp);
        }

        $newChunkSize = $chunkSize * (1024 * 1024);
        $bytesCount = 0;
        $handle = fopen($srcName, 'rb');
        $fp = fopen($dstName, 'wb');

        if ($handle === false) {
            return false;
        }

        while (!feof($handle)) {
            $data = fread($handle, $newChunkSize);
            fwrite($fp, $data, strlen($data));

            if ($returnbytes) {
                $bytesCount += strlen($data);
            }
        }

        $status = fclose($handle);

        fclose($fp);

        if ($returnbytes && $status) {
            return $bytesCount;
        }

        return $status;
    }

    /**
     * @param string $url
     */
    public static function fetchRemoteImage(string $url)
    {
        $uploadedAssets = [];

        $tempPath = self::createTempPath();

        // Download each image. Note we've already checked if there's an existing asset and if the
        // user has set to use that instead so we're good to proceed.
//        try {
            $filename = self::getRemoteUrlFilename($url);

            $fetchedImage = $tempPath . $filename;

            // But also check if we've downloaded this recently, use the copy in the temp directory
            $cachedImage = FileHelper::findFiles($tempPath, [
                'only' => [$filename],
                'recursive' => false,
            ]);

            Craft::info('Fetching remote image `{i}` - `{j}`', ['i' => $url, 'j' => $filename]);

            if (!$cachedImage) {
                self::downloadFile($url, $fetchedImage);
            } else {
                $fetchedImage = $cachedImage[0];
            }


            $result = self::createAsset($fetchedImage, $filename, 2);

            if ($result) {
                $uploadedAssets[] = $result;
            } else {
                Craft::error('Failed to create asset from `{i}`', ['i' => $url]);
            }
//        } catch (\Throwable $e) {
//            Craft::error('Asset error: `{url}` - `{e}`.', ['url' => $url, 'e' => $e->getMessage()]);
//        }

        return $uploadedAssets;
    }

    /**
     * @param string $tempFilePath
     * @param string $filename
     * @param int $folderId
     * @param string $field
     * @param string $element
     * @param string $conflict
     * @param bool $updateSearchIndexes
     * @return int
     * @throws \Throwable
     * @throws \craft\errors\AssetLogicException
     * @throws \craft\errors\ElementNotFoundException
     * @throws \craft\errors\FileException
     * @throws \yii\base\Exception
     */
    private static function createAsset($tempFilePath, $filename, $folderId = 2)
    {
        $assets = Craft::$app->getAssets();

        $folder = $assets->findFolder(['id' => $folderId]);

        // Create the new asset (even if we're setting it to replace)
        $asset = new AssetElement();
        $asset->tempFilePath = $tempFilePath;
        $asset->filename = $filename;
        $asset->newFolderId = $folder->id;
        $asset->volumeId = $folder->volumeId;
        $asset->avoidFilenameConflicts = true;
        $asset->setScenario(AssetElement::SCENARIO_CREATE);

        $result = Craft::$app->getElements()->saveElement($asset, true, true);

        if ($result) {
            // Annoyingly, you have to create the asset field, then move it to the temp directly, then replace the conflicting
            // asset, so there's a bit more work here than I would've thought...
            if ($asset->conflictingFilename !== null) {
                $conflictingAsset = AssetElement::findOne(['folderId' => $folder->id, 'filename' => $asset->conflictingFilename]);

                if ($conflictingAsset) {
                    Craft::info('Replacing existing asset `#{i}` with `#{j}`', ['i' => $conflictingAsset->id, 'j' => $asset->id]);

                    $tempPath = $asset->getCopyOfFile();
                    $assets->replaceAssetFile($conflictingAsset, $tempPath, $conflictingAsset->filename);
                    Craft::$app->getElements()->deleteElement($asset);

                    return $conflictingAsset->id;
                }
            }

            return $asset->id;
        }

        return false;
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    private static function createTempPath()
    {
        $tempPath = Craft::$app->getPath()->getTempPath() . '/staff/';

        if (!is_dir($tempPath)) {
            FileHelper::createDirectory($tempPath);
        }

        return $tempPath;
    }

    /**
     * @param $url
     * @return string
     */
    public static function getRemoteUrlFilename($url)
    {
        // Function to extract a filename from a URL path. It does not query the actual URL however.
        // There are some tricky cases being tested again, and mostly revolves around query strings. We do our best to figure it out!
        // http://example.com/test.php
        // http://example.com/test.php?pubid=image.jpg
        // http://example.com/image.jpg?width=1280&cid=5049
        // http://example.com/image.jpg?width=1280&cid=5049&un=support%40gdomain.com
        // http://example.com/test
        // http://example.com/test?width=1280&cid=5049
        // http://example.com/test?width=1280&cid=5049&un=support%40gdomain.com

        $extension = self::getRemoteUrlExtension($url);

        // PathInfo can't really deal with query strings, so remove it
        $filename = UrlHelper::stripQueryString($url);

        // Can we easily get the extension for this URL?
        $filename = pathinfo($filename, PATHINFO_FILENAME);

        // If there was a query string, append it so this asset remains unique
        $query = parse_url($url, PHP_URL_QUERY);

        if ($query) {
            $filename .= '-' . $query;
        }

        $filename = AssetsHelper::prepareAssetName($filename, false);

        return $filename . '.' . $extension;
    }

    /**
     * @param $url
     * @return string
     */
    public static function getRemoteUrlExtension($url)
    {
        // PathInfo can't really deal with query strings, so remove it
        $extension = UrlHelper::stripQueryString($url);

        // Can we easily get the extension for this URL?
        $extension = StringHelper::toLowerCase(pathinfo($extension, PATHINFO_EXTENSION));

        // We might now have a perfectly acceptable extension, but is it real and allowed by Craft?
        if (!in_array($extension, Craft::$app->getConfig()->getGeneral()->allowedFileExtensions, true)) {
            $extension = '';
        }

        // If we can't easily determine the extension of the url, fetch it
        if (!$extension) {
            $client = Craft::$plugin->service->createGuzzleClient();
            $response = null;

            // Try using HEAD requests (for performance), if it fails use GET
            try {
                $response = $client->head($url);
            } catch (\Throwable $e) {
            }

            try {
                if (!$response) {
                    $response = $client->get($url);
                }
            } catch (\Throwable $e) {
            }

            if ($response) {
                $contentType = $response->getHeader('Content-Type');

                if (isset($contentType[0])) {
                    // Because some servers cram unnecessary things it the Content-Type header.
                    $contentType = explode(';', $contentType[0]);
                    // Convert MIME type to extension
                    $extension = FileHelper::getExtensionByMimeType($contentType[0]);
                }
            }
        }

        return StringHelper::toLowerCase($extension);
    }
}
