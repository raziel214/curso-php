<?php

declare(strict_types=1);

namespace App\Project\Infrastructure\Persistence;

use App\Project\Domain\Project;
use App\Project\Domain\ProjectRepository;

final class EloquentProjectRepository implements ProjectRepository
{
    public function all(): array
    {
        return ProjectModel::query()->orderByDesc('id')->get()
            ->map(fn (ProjectModel $m) => $this->toDomain($m))
            ->values()->all();
    }

    public function find(int $id): ?Project
    {
        $model = ProjectModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function save(Project $project): Project
    {
        $model = $project->id() !== null ? ProjectModel::findOrFail($project->id()) : new ProjectModel();
        $model->fill([
            'title' => $project->title(),
            'description' => $project->description(),
            'technologies' => $project->technologies(),
        ])->save();

        return $project->withId((int) $model->id);
    }

    public function delete(int $id): void
    {
        ProjectModel::destroy($id);
    }

    private function toDomain(ProjectModel $m): Project
    {
        return new Project((int) $m->id, (string) $m->title, (string) $m->description, $m->technologies ?? []);
    }
}
