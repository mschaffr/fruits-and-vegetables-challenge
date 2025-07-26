<?php

namespace App\Command;

use App\Database\FoodDatabase;
use App\Service\FoodImporter;
use JsonException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportFoodsCommand extends Command
{
    public function __construct(private readonly FoodImporter $importer, private readonly FoodDatabase $database)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:import-foods')
            ->setDescription('Imports food data from an external source')
            ->setHelp('This command allows you to import food data from a specified source into the application.')
            ->addArgument('file', InputArgument::REQUIRED, 'The path to the file containing food data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');
        if (!file_exists($filePath)) {
            $output->writeln('<error>File not found: ' . $filePath . '</error>');
            return Command::FAILURE;
        }

        // if loading data by JSON file is the future way of importing data, we can anticipate the size of the file
        // and based on that we can decide to load it in chunks, or use a streaming parser.
        // for simplicity, we'll just read the whole file into memory here.
        // in a real-world scenario, this could be a large file, and we might want to use a more memory-efficient
        // approach.
        $data = file_get_contents($filePath);
        if ($data === false) {
            $output->writeln('<error>Failed to read file: ' . $filePath . '</error>');
            return Command::FAILURE;
        }

        try {
            $foods = json_decode(json: $data, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $output->writeln(sprintf('<error>Invalid JSON format in file "%s" in line %s</error>', $filePath, $e->getLine()));
            return Command::FAILURE;
        }

        $this->importer->import($foods);

        foreach ($this->importer->getErrorMessages() as $message) {
            $output->writeln('<error>' . $message . '</error>');
        }

        foreach ($this->importer->getInfoMessages() as $message) {
            $output->writeln('<info>' . $message . '</info>');
        }

        if ($this->importer->hasErrors()) {
            return Command::FAILURE;
        }

        // database persistence for imports usually have design decisions to make, like transactions, error handling, repeatability, async behavior etc.
        // based on these decisions, the implementation can vary and is not really reflected in this example.
        // we'll just persist the collections as they are, without any additional logic as it doesn't matter by task definition.
        foreach ($this->importer->getCollections() as $type => $collection) {
            $this->database->persist($collection, $type);
        }

        $output->writeln('Food data imported successfully.');

        return Command::SUCCESS;
    }
}
