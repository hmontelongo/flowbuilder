<?php

namespace App\Livewire\FlowBuilder;

use App\Models\Flow;
use Livewire\Component;

class Canvas extends Component
{
    public Flow $flow;

    /**
     * @var array<int, array{id: string, type: string, name: string, x: int, y: int}>
     */
    public array $nodes = [];

    /**
     * @var array<int, array{id: string, source: string, target: string}>
     */
    public array $edges = [];

    public string $activeTool = 'flows';

    public string $flowName = '';

    public function setActiveTool(string $toolId): void
    {
        $this->activeTool = $toolId;
    }

    public function mount(Flow $flow): void
    {
        $this->flow = $flow;
        $this->nodes = $flow->nodes ?? [];
        $this->edges = $flow->edges ?? [];
        $this->flowName = $flow->name;
    }

    private function persist(): void
    {
        $this->flow->update([
            'name' => $this->flowName,
            'nodes' => $this->nodes,
            'edges' => $this->edges,
        ]);
    }

    /**
     * Sync the full flow state from Vue Flow.
     *
     * @param  array<int, array{id: string, type: string, position: array{x: float, y: float}, data: array<string, mixed>}>  $nodes
     * @param  array<int, array{id: string, source: string, target: string}>  $edges
     */
    public function syncFlow(array $nodes, array $edges): void
    {
        $this->nodes = collect($nodes)->map(function ($node) {
            return [
                'id' => $node['id'],
                'type' => $node['type'],
                'name' => $node['data']['label'] ?? $node['type'],
                'x' => (int) round($node['position']['x']),
                'y' => (int) round($node['position']['y']),
                'data' => $node['data'] ?? [],
                'state' => $node['data']['state'] ?? ['mode' => 'normal'],
            ];
        })->toArray();

        $this->edges = collect($edges)->map(function ($edge) {
            return [
                'id' => $edge['id'],
                'source' => $edge['source'],
                'target' => $edge['target'],
            ];
        })->toArray();

        $this->persist();
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
        $this->persist();
    }

    public function addEdge(string $edgeId, string $source, string $target): void
    {
        $this->edges[] = [
            'id' => $edgeId,
            'source' => $source,
            'target' => $target,
        ];
        $this->persist();
    }

    public function removeEdge(string $edgeId): void
    {
        $this->edges = array_values(
            array_filter($this->edges, fn ($edge) => $edge['id'] !== $edgeId)
        );
        $this->persist();
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
        $this->persist();
    }

    public function addNode(string $nodeId, string $type, string $name, int $x, int $y): void
    {
        $this->nodes[] = [
            'id' => $nodeId,
            'type' => $type,
            'name' => $name,
            'x' => $x,
            'y' => $y,
            'data' => [],
        ];
        $this->persist();
    }

    /**
     * Update the data payload for a specific node.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateNodeData(string $nodeId, array $data): void
    {
        foreach ($this->nodes as $index => $node) {
            if ($node['id'] === $nodeId) {
                $this->nodes[$index]['data'] = array_merge($node['data'] ?? [], $data);
                break;
            }
        }
        $this->persist();
    }

    public function clearCanvas(): void
    {
        $this->nodes = [];
        $this->edges = [];
        $this->persist();

        // Dispatch browser event to reset Vue Flow
        $this->dispatch('canvas-cleared');
    }

    public function render()
    {
        return view('livewire.flow-builder.canvas')
            ->layout('components.layouts.canvas');
    }
}
