<?php

declare(strict_types=1);

namespace KickflipMonoTests;

use Kickflip\Models\PageData;
use SebastianBergmann\Exporter\Exporter;

use function array_combine;
use function array_map;
use function array_values;
use function explode;
use function implode;
use function is_array;
use function is_int;
use function preg_replace;
use function sprintf;
use function str_replace;

use const PHP_EOL;

trait DataProviderHelpers
{
    /**
     * @param array<string|string|int, object|array<array-key, mixed>> $array
     *
     * @return array<string, object|array<array-key, mixed>>
     */
    public function autoAddDataProviderKeys(array $array): array
    {
        $exporter = new Exporter();
        $normalizedKeys = [];
        foreach ($array as $key => $data) {
            if (!is_array($data) && !($data instanceof PageData)) {
                $wrapped = [$data];
                $normalizedKeys[] = $exporter->shortenedRecursiveExport($wrapped);
            } elseif ($data instanceof PageData) {
                $wrapped = [$data];
                $exportedName = $exporter->shortenedRecursiveExport($wrapped);
                $normalizedKeys[] = str_replace('...', $data->getTitleId(), $exportedName);
            } elseif (is_int($key)) {
                $normalizedKeys[] = sprintf('(%s)', $exporter->shortenedRecursiveExport($data));
            } else {
                $normalizedKeys[] = sprintf('data set "%s"', $key);
            }
        }

        if (!is_array(array_values($array)[0])) {
            return array_combine($normalizedKeys, array_map(fn ($value) => [$value], $array));
        }

        return array_combine($normalizedKeys, $array);
    }

    public static function stripMixIdsFromHtml(string $htmlString): string
    {
        $resultLines = explode(PHP_EOL, $htmlString);
        $resultLines = preg_replace('/(href|src)="(.*)\?id=(.*)"/', '$1="$2"', $resultLines);

        return implode(PHP_EOL, $resultLines);
    }
}
