<?php

namespace App\Console\Commands;

use App\Support\Questionnaires\QuestionnaireLibraryExporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('questionnaires:export
    {--questionnaire=* : Questionnaire IDs om te exporteren}
    {--path= : Doelbestand voor de JSON-export}')]
#[Description('Exporteer questionnaires uit de bibliotheek naar een JSON-bestand')]
class ExportQuestionnairesCommand extends Command
{
    public function __construct(private readonly QuestionnaireLibraryExporter $exporter)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $questionnaireIds = array_values(array_filter(array_map(
            static fn (string $value): int => (int) $value,
            (array) $this->option('questionnaire'),
        )));

        $path = $this->resolvePath(
            $this->option('path')
                ? (string) $this->option('path')
                : 'storage/app/questionnaires/questionnaires-export-'.now()->format('Ymd-His').'.json',
        );

        $payload = $this->exporter->export($questionnaireIds === [] ? null : $questionnaireIds);

        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $this->info('Questionnaire-export opgeslagen.');
        $this->line($path);
        $this->line('Aantal questionnaires: '.count($payload['questionnaires']));

        return self::SUCCESS;
    }

    protected function resolvePath(string $path): string
    {
        if (str_starts_with($path, DIRECTORY_SEPARATOR)) {
            return $path;
        }

        return base_path($path);
    }
}
