<?php

namespace App\Console\Commands;

use App\Item;
use App\Support\ItemCollection;
use App\World;
use Graphp\Algorithms\ShortestPath\BreadthFirst;
use Graphp\GraphViz\GraphViz;
use Illuminate\Console\Command;

class GraphWalkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'graph:walk'
        . ' {from=Start : Location to start from}'
        . ' {to=Annie : Location to walk to}'
        . ' {--item=* : Equipped Item (All if none set)}'
        . ' {--output= : Setting for testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will create a png of the walk from one named vertex to another.';

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
        $toVertex = is_string($this->argument('to')) ? $this->argument('to') : 'Annie';
        $outputFile = $this->option('output') ?
            is_string($this->option('output')) ? $this->option('output') : 'none'
            : false;

        $world = new World(count($items) ? new ItemCollection($items) : Item::all());

        $start = $world->getVertex('Start');

        if ($fromVertex !== 'Start') {
            $start->setAttribute('graphviz.fillcolor', '');
            $start->setAttribute('graphviz.style', '');

            $start = $world->getVertex($fromVertex);
            $start->setAttribute('graphviz.fillcolor', 'green');
            $start->setAttribute('graphviz.style', 'filled');
        }

        $bf = new BreadthFirst($start);

        try {
            $end = $world->getVertex($toVertex);
        } catch (\OutOfBoundsException $e) {
            $this->error(sprintf('Not Reachable: %s', $toVertex));

            return 100;
        }

        $end->setAttribute('graphviz.fillcolor', 'red');
        $end->setAttribute('graphviz.style', 'filled');

        if (!$bf->hasVertex($end)) {
            $this->error(sprintf('Not Reachable: %s', $toVertex));

            return 101;
        }

        $graph = $bf->getWalkTo($end)->createGraph();
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
