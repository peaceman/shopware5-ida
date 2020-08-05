<?php

namespace App\Console\Commands;

use App\Domain\DataExtractor;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use League\Csv\Writer;

class Extract extends Command
{
    protected $signature = "ida:extract {outputFile}";

    public function handle(DataExtractor $extractor): void
    {
        $outputFile = $this->argument('outputFile');
        $writer = Writer::createFromPath($outputFile, 'w+');
        $writer->setDelimiter(';');
        $headerWritten = false;

        foreach ($extractor->extract() as $row) {
            if (!$headerWritten) {
                $this->writeHeader($writer, $row);
                $headerWritten = true;
            }

            $writer->insertOne($row);
        }
    }

    private function writeHeader(Writer $writer, array $row): void
    {
        $writer->insertOne(array_map(
            static function (string $value): string {
                return Str::snake($value);
            },
            array_keys($row)
        ));
    }
}
