<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

class CsvExporter
{
    /** @var resource|null */
    private mixed $handle = null;

    public function open(): static
    {
        $handle = fopen('php://output', 'w');

        if ($handle === false) {
            Log::error('CSV export: kon php://output niet openen.');

            return $this;
        }

        $this->handle = $handle;

        // UTF-8 BOM so Excel opens the file with the correct encoding.
        fwrite($this->handle, "\xEF\xBB\xBF");

        return $this;
    }

    /**
     * @param  array<int, string>  $row
     */
    public function writeRow(array $row): void
    {
        if ($this->handle === null) {
            return;
        }

        fputcsv($this->handle, $row);
    }

    public function close(): void
    {
        if ($this->handle !== null) {
            fclose($this->handle);
            $this->handle = null;
        }
    }

    public function isOpen(): bool
    {
        return $this->handle !== null;
    }
}
