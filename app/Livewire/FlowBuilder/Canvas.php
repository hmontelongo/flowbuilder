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

    public string $activeTool = 'flows';

    public string $flowName = 'Nombre del chatbot';

    public function setActiveTool(string $toolId): void
    {
        $this->activeTool = $toolId;
    }

    public function mount(): void
    {
        // Start with empty canvas - blocks will be added via the palette
        $this->nodes = [];
        $this->edges = [];
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

    public function removeNode(string $nodeId): void
    {
        $this->nodes = array_values(
            array_filter($this->nodes, fn ($node) => $node['id'] !== $nodeId)
        );
        // Also remove any edges connected to this node
        $this->edges = array_values(
            array_filter($this->edges, fn ($edge) => $edge['source'] !== $nodeId && $edge['target'] !== $nodeId)
        );
    }

    public function addNode(string $nodeId, string $type, string $name, int $x, int $y): void
    {
        $this->nodes[] = [
            'id' => $nodeId,
            'type' => $type,
            'name' => $name,
            'x' => $x,
            'y' => $y,
        ];
    }

    public function render()
    {
        return view('livewire.flow-builder.canvas')
            ->layout('components.layouts.canvas');
    }
}
