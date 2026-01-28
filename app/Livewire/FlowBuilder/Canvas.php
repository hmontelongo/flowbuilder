<?php

namespace App\Livewire\FlowBuilder;

use Livewire\Component;

class Canvas extends Component
{
    /**
     * @var array<int, array{id: string, type: string, name: string, x: int, y: int}>
     */
    public array $nodes = [];

    /**
     * @var array<int, array{id: string, source: string, target: string}>
     */
    public array $edges = [];

    public function mount(): void
    {
        // Initial nodes - spaced 150px apart (256px card width + 150px gap)
        $this->nodes = [
            [
                'id' => 'node-1',
                'type' => 'canal',
                'name' => 'Canal',
                'x' => 100,
                'y' => 150,
            ],
            [
                'id' => 'node-2',
                'type' => 'trigger',
                'name' => 'Nombre de bloque',
                'x' => 500,
                'y' => 150,
            ],
        ];

        // Initial edge connecting them
        $this->edges = [
            [
                'id' => 'edge-1',
                'source' => 'node-1',
                'target' => 'node-2',
            ],
        ];
    }

    public function updateNodePosition(string $nodeId, int $x, int $y): void
    {
        foreach ($this->nodes as $index => $node) {
            if ($node['id'] === $nodeId) {
                $this->nodes[$index]['x'] = $x;
                $this->nodes[$index]['y'] = $y;
                break;
            }
        }
    }

    public function addEdge(string $edgeId, string $source, string $target): void
    {
        $this->edges[] = [
            'id' => $edgeId,
            'source' => $source,
            'target' => $target,
        ];
    }

    public function removeEdge(string $edgeId): void
    {
        $this->edges = array_values(
            array_filter($this->edges, fn ($edge) => $edge['id'] !== $edgeId)
        );
    }

    public function render()
    {
        return view('livewire.flow-builder.canvas')
            ->layout('components.layouts.canvas');
    }
}
