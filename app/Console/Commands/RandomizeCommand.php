<?php

namespace App\Console\Commands;

use App\Item;
use App\Rom;
use App\Services\RandomizerService;
use App\Support\Flips;
use App\Support\ItemCollection;
use App\World;
use Graphp\GraphViz\GraphViz;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RandomizeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'randomize {input_file=goonies2.nes : base rom to randomize}'
    . ' {output_directory=./ : where to place randomized rom}'
    . ' {--spoiler : generate a spoiler file}'
    . ' {--no-rom : no not generate output rom}'
    . ' {--bps : make a bps file}'
    . ' {--graph : make a graph image}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a randomization of file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $inputFile = is_string($this->argument('input_file')) ? $this->argument('input_file') : 'goonies2.nes';
        $outputDirectory = is_string($this->argument('output_directory')) ? $this->argument('output_directory') : './';

        if (!is_readable($inputFile)) {
            $this->error('Source File not readable');

            return 100;
        }

        if (!is_dir($outputDirectory) || !is_writable($outputDirectory)) {
            $this->error('Target Directory not writable');

            return 101;
        }

        $hash = substr(hash('md5', (string) microtime(true)), 0, 10);

        $flips = new Flips;

        $rom = new Rom($inputFile);

        if ($rom->getMD5() == Rom::ORGINAL_MD5) {
            // @codeCoverageIgnoreStart
            $tmp_file = tempnam(sys_get_temp_dir(), "Patched-$hash-");

            if (!is_string($tmp_file) || !is_writable($tmp_file)) {
                $this->error('Cannot apply base patch');

                return 101;
            }

            $rom->save($tmp_file);
            file_put_contents($tmp_file, $flips->applyBpsToFile($tmp_file, public_path('bps/base.bps')));

            $rom = new Rom($tmp_file);
            // @codeCoverageIgnoreEnd
        }

        $world = new World(new ItemCollection);
        $rand = new RandomizerService;
        $rand->randomize($world);

        $rand->writeToRom($world, $rom);

        if (!($this->option('no-rom') ?? false)) {
            // @codeCoverageIgnoreStart
            $output_file = sprintf('%s/G2-VT_%s.nes', $outputDirectory, $hash);

            if (!is_string($output_file) || !is_writable($outputDirectory)) {
                $this->error('Cannot write patched rom');

                return 102;
            }

            $rom->save($output_file);
            Log::info(sprintf('Rom Saved: %s', $output_file));
            $this->info(sprintf('Rom Saved: %s', $output_file));
            // @codeCoverageIgnoreEnd
        }

        if ($this->option('bps')) {
            // @codeCoverageIgnoreStart
            $output_file = sprintf('%s/G2-VT_%s.bps', $outputDirectory, $hash);

            if (!is_string($output_file) || !is_writable($outputDirectory)) {
                $this->error('Cannot write patch file');

                return 103;
            }

            $tmp_file = tempnam(sys_get_temp_dir(), "Bps-$hash-");

            if (!is_string($tmp_file) || !is_writable($tmp_file)) {
                $this->error('Cannot write tempory patch file');

                return 104;
            }

            $rom->save($tmp_file);

            $data = $flips->createBpsFromFiles(env('ROM_BASE') ?? $inputFile, $tmp_file);

            file_put_contents($output_file, $data);

            Log::info(sprintf('Bps Saved: %s', $output_file));
            $this->info(sprintf('Bps Saved: %s', $output_file));
            // @codeCoverageIgnoreEnd
        }

        if ($this->option('spoiler')) {
            // @codeCoverageIgnoreStart
            $spoiler_file = sprintf('%s/G2-VT_%s.json', $outputDirectory, $hash);

            if (!is_string($spoiler_file) || !is_writable($outputDirectory)) {
                $this->error('Cannot write spoiler file');

                return 105;
            }

            file_put_contents($spoiler_file, json_encode($rand->getSpoiler(), JSON_PRETTY_PRINT));
            Log::info(sprintf('Spoiler Saved: %s', $spoiler_file));
            $this->info(sprintf('Spoiler Saved: %s', $spoiler_file));
            // @codeCoverageIgnoreEnd
        }

        if ($this->option('graph')) {
            // @codeCoverageIgnoreStart
            $image_file = sprintf('%s/G2-VT_%s.png', $outputDirectory, $hash);

            $world->setItems(Item::all());

            $start = $world->getVertex('Start');
            $graph = $world->getReachableGraphFromVertex($start);

            $temp_image = (new GraphViz)->createImageFile($graph);
            rename($temp_image, $image_file);
            Log::info(sprintf('Graph Saved: %s', $image_file));
            $this->info(sprintf('Graph Saved: %s', $image_file));
            // @codeCoverageIgnoreEnd
        }

        return 0;
    }
}
