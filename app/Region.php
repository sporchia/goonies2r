<?php

namespace App;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;

/**
 * A Region is the over world spaces between rooms.
 */
class Region extends Vertex
{
    /**
     * Create a new Regon.
     *
     * @param \Fhaculty\Graph\Graph $graph       graph to be added to
     * @param string|int            $name        identifier used to uniquely identify this vertex in the graph
     *
     * @return void
     */
    public function __construct(Graph $graph, $name)
    {
        parent::__construct($graph, $name);

        $this->setAttribute('graphviz.fillcolor', 'pink');
        $this->setAttribute('graphviz.style', 'filled');
    }
}
