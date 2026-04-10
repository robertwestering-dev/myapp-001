<?php

namespace App\Console\Commands;

use App\Support\Questionnaires\QuestionnaireLibraryImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use JsonException;
use Throwable;

#[Signature('questionnaires:import
    {path : Pad naar het JSON-importbestand}')]
#[Description('Importeer questionnaires uit een JSON-exportbestand')]
class ImportQuestionnairesCommand extends Command
{
    public function __construct(private readonly QuestionnaireLibraryImporter $importer)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = $this->resolvePath((string) $this->argument('path'));

        if (! File::exists($path)) {
            $this->error('Importbestand niet gevonden: '.$path);

            return self::FAILURE;
        }

        try {
            /** @var array<string, mixed> $payload */
            $payload = json_decode(File::get($path), true, flags: JSON_THROW_ON_ERROR);
            $result = $this->importer->import($payload);
        } catch (JsonException $exception) {
            $this->error('Importbestand bevat geen geldige JSON.');
            $this->line($exception->getMessage());

            return self::FAILURE;
        } catch (Throwable $exception) {
            $this->error('Import mislukt.');
            $this->line($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Questionnaire-import afgerond.');
        $this->line('Aantal questionnaires: '.$result['questionnaires']);

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
