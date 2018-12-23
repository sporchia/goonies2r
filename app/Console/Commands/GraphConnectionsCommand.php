<?php

namespace App\Console\Commands;

use App\Item;
use App\Services\RandomizerService;
use App\Support\ItemCollection;
use App\World;
use Graphp\GraphViz\GraphViz;
use Illuminate\Console\Command;

class GraphConnectionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'graph:connections'
        . ' {from=Start : Location to start from}'
        . ' {--item=* : Equipped Item (All if none set)}'
        . ' {--output= : Setting for testing}'
        . ' {--randomize : Randomize first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will create a png of all accessible Vertices from the named Vertex.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $items = array_map(function ($itemName) {
            return Item::get($itemName);
        }, (array) $this->option('item'));

        $fromVertex = is_string($this->argument('from')) ? $this->argument('from') : 'Start';
        $outputFile = $this->option('output') ?
            is_string($this->option('output')) ? $this->option('output') : 'none'
            : false;

        $world = new World(new ItemCollection);
        if ($this->option('randomize')) {
            $rand = new RandomizerService;
            $rand->randomize($world);
        }

        $world->setItems(count($items) ? new ItemCollection($items) : Item::all());

        $start = $world->getVertex('Start');

        if ($fromVertex !== 'Start') {
            $start->setAttribute('graphviz.fillcolor', '');
            $start->setAttribute('graphviz.style', '');

            $start = $world->getVertex($fromVertex);
            $start->setAttribute('graphviz.fillcolor', 'green');
            $start->setAttribute('graphviz.style', 'filled');
        }

        $graph = $world->getReachableGraphFromVertex($start);

        if ($outputFile) {
            if ($outputFile !== 'none') {
                // @codeCoverageIgnoreStart
                $imgFile = (new GraphViz)->createImageFile($graph);
                rename($imgFile, $outputFile);
                // @codeCoverageIgnoreEnd
            }
        } else {
            // @codeCoverageIgnoreStart
            (new GraphViz)->display($graph);
            // @codeCoverageIgnoreEnd
        }

        return 0;
    }
}
