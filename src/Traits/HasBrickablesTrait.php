<?php

namespace Okipa\LaravelBrickables\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Okipa\LaravelBrickables\Abstracts\Brickable;
use Okipa\LaravelBrickables\Exceptions\InvalidBrickableClassException;
use Okipa\LaravelBrickables\Exceptions\NotRegisteredBrickableClassException;
use Okipa\LaravelBrickables\Facades\Brickables;
use Okipa\LaravelBrickables\Models\Brick;

trait HasBrickablesTrait
{
    /** @inheritDoc */
    public function addBricks(array $bricksData): Collection
    {
        $createdBricks = new Collection();
        foreach ($bricksData as $brickData) {
            $createdBricks->push($this->addBrick($brickData[0], $brickData[1]));
        }

        return $createdBricks;
    }

    /** @inheritDoc */
    public function addBrick(string $brickableClass, array $data): Brick
    {
        $this->checkBrickableType($brickableClass);
        $this->checkBrickableIsRegistered($brickableClass);
        $brick = $this->createBrick($brickableClass, $data);
        $this->handleSingleBricks($brickableClass, $brick);

        return $brick;
    }

    /**
     * @param string $brickableClass
     *
     * @throws \Okipa\LaravelBrickables\Exceptions\InvalidBrickableClassException
     */
    protected function checkBrickableType(string $brickableClass): void
    {
        if (! app($brickableClass) instanceof Brickable) {
            throw new InvalidBrickableClassException('The given ' . $brickableClass
                . ' brickable class should extend ' . Brickable::class . '.');
        }
    }

    /**
     * @param string $brickableClass
     *
     * @throws \Okipa\LaravelBrickables\Exceptions\NotRegisteredBrickableClassException
     */
    protected function checkBrickableIsRegistered(string $brickableClass): void
    {
        if (! in_array($brickableClass, config('brickables.registered'))) {
            throw new NotRegisteredBrickableClassException('The given ' . $brickableClass
                . ' brickable is not registered in the config(\'brickables.registered\') array.');
        }
    }

    /**
     * @param string $brickableClass
     * @param array $data
     *
     * @return \Okipa\LaravelBrickables\Models\Brick
     */
    protected function createBrick(string $brickableClass, array $data): Brick
    {
        /** @var Brickable $brickable */
        $brickable = app($brickableClass);
        $brickModel = $brickable->getBrickModel();
        $brickModel->model_type = $this->getMorphClass();
        $brickModel->model_id = $this->id;
        $brickModel->brickable_type = $brickableClass;
        $brickModel->data = $data;
        $brickModel->save();

        return $brickModel;
    }

    /**
     * @param string $brickableClass
     * @param \Okipa\LaravelBrickables\Models\Brick $brick
     *
     * @return void
     * @throws \Okipa\LaravelBrickables\Exceptions\NotRegisteredBrickableClassException
     * @throws \Okipa\LaravelBrickables\Exceptions\InvalidBrickableClassException
     */
    protected function handleSingleBricks(string $brickableClass, Brick $brick): void
    {
        if (in_array($brickableClass, $this->hasSingleBrick ?: [])) {
            $this->clearBricksExcept($brickableClass, collect()->push($brick));
        }
    }

    /** @inheritDoc */
    public function clearBricksExcept(string $brickableClass, Collection $excludeBricks): void
    {
        $this->checkBrickableType($brickableClass);
        $this->checkBrickableIsRegistered($brickableClass);
        $this->getBricks($brickableClass)->reject(function (Brick $brick) use ($excludeBricks) {
            return $excludeBricks->where($brick->getKeyName(), $brick->getKey())->count();
        })->each->delete();
    }

    /** @inheritDoc */
    public function getBricks(?string $brickableClass = null): Collection
    {
        /** @var \Okipa\LaravelBrickables\Models\Brick $bricksBaseModel */
        $bricksBaseModel = app(config('brickables.bricks.model'));
        $query = $bricksBaseModel->where('model_type', $this->getMorphClass())->where('model_id', $this->id);
        if ($brickableClass) {
            $query->where('brickable_type', $brickableClass);
        }
        $bricks = $query->ordered()->get();

        return Brickables::castBricks($bricks);
    }

    /** @inheritDoc */
    public function clearBricks(string $brickableClass): void
    {
        $this->checkBrickableType($brickableClass);
        $this->checkBrickableIsRegistered($brickableClass);
        $this->getBricks($brickableClass)->each->delete();
    }

    /** @inheritDoc */
    public function getFirstBrick(string $brickableClass): ?Brick
    {
        $this->checkBrickableType($brickableClass);
        $this->checkBrickableIsRegistered($brickableClass);

        return $this->getBricks()->where('brickable_type', $brickableClass)->first();
    }

    /** @inheritDoc */
    public function getReadableClassName(): string
    {
        return __(ucfirst(Str::snake(class_basename($this), ' ')));
    }
}
