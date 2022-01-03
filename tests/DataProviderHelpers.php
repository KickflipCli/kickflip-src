<?php

declare(strict_types=1);

namespace KickflipMonoTests;

use SebastianBergmann\Exporter\Exporter;

use function array_combine;
use function array_map;
use function array_values;
use function is_array;
use function is_int;
use function sprintf;

trait DataProviderHelpers
{
    /**
     * @param array $array<array-key, array>
     *
     * @return array<string, array>
     */
    public function autoAddDataProviderKeys(array $array)
    {
        $exporter = new Exporter();
        $normalizedKeys = [];
        foreach ($array as $key => $data) {
            if (is_int($key)) {
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
}
