<?php

namespace Okipa\LaravelBrickables\Tests\Unit;

use Illuminate\Support\Facades\Route;
use Okipa\LaravelBrickables\Abstracts\Brickable;
use Okipa\LaravelBrickables\Tests\BrickableTestCase;
use Okipa\LaravelBrickables\Tests\Models\Page;

class BrickableTest extends BrickableTestCase
{
    /** @test */
    public function brickable_can_set_and_returns_label()
    {
        $brickable = new Class extends Brickable {
            public function setLabel(): string
            {
                return 'Dummy label';
            }

            public function setValidationRules(): array
            {
                return [];
            }
        };
        $this->assertEquals('Dummy label', $brickable->getLabel());
    }

    /** @test */
    public function brickable_can_set_and_returns_brick_view_path()
    {
        $brickable = new Class extends Brickable {
            public function setBrickViewPath(): string
            {
                return 'dummy.brick.view.path';
            }

            public function setValidationRules(): array
            {
                return [];
            }
        };
        $this->assertEquals('dummy.brick.view.path', $brickable->getBrickViewPath());
    }

    /** @test */
    public function brickable_can_set_and_returns_form_view_path()
    {
        $brickable = new Class extends Brickable {
            public function setFormViewPath(): string
            {
                return 'dummy.form.view.path';
            }

            public function setValidationRules(): array
            {
                return [];
            }
        };
        $this->assertEquals('dummy.form.view.path', $brickable->getFormViewPath());
    }

    /** @test */
    public function brickable_can_set_and_returns_store_route()
    {
        Route::post('dummy/store', function () {
        })->name('dummy.store');
        $brickable = new Class extends Brickable {
            public function setStoreRouteName(): string
            {
                return 'dummy.store';
            }

            public function setValidationRules(): array
            {
                return [];
            }
        };
        $this->assertEquals('http://localhost/dummy/store', $brickable->getStoreRoute());
    }

    /** @test */
    public function brickable_can_set_and_returns_edit_route()
    {
        Route::get('dummy/edit/{brick}', function () {
        })->name('dummy.edit');
        $brickable = new Class extends Brickable {
            public function setEditRouteName(): string
            {
                return 'dummy.edit';
            }

            public function setValidationRules(): array
            {
                return [];
            }
        };
        config()->set('brickables.registered', [get_class($brickable)]);
        $page = factory(Page::class)->create();
        $brick = $page->addBrick(get_class($brickable), []);
        $this->assertEquals('http://localhost/dummy/edit/' . $brick->id, $brickable->getEditRoute($brick));
    }

    /** @test */
    public function brickable_can_set_and_returns_update_route()
    {
        Route::put('dummy/update/{brick}', function () {
        })->name('dummy.update');
        $brickable = new Class extends Brickable {
            public function setUpdateRouteName(): string
            {
                return 'dummy.update';
            }

            public function setValidationRules(): array
            {
                return [];
            }
        };
        config()->set('brickables.registered', [get_class($brickable)]);
        $page = factory(Page::class)->create();
        $brick = $page->addBrick(get_class($brickable), []);
        $this->assertEquals('http://localhost/dummy/update/' . $brick->id, $brickable->getUpdateRoute($brick));
    }

    /** @test */
    public function brickable_can_set_and_returns_destroy_route()
    {
        Route::delete('dummy/destroy/{brick}', function () {
        })->name('dummy.destroy');
        $brickable = new Class extends Brickable {
            public function setDestroyRouteName(): string
            {
                return 'dummy.destroy';
            }

            public function setValidationRules(): array
            {
                return [];
            }
        };
        config()->set('brickables.registered', [get_class($brickable)]);
        $page = factory(Page::class)->create();
        $brick = $page->addBrick(get_class($brickable), []);
        $this->assertEquals('http://localhost/dummy/destroy/' . $brick->id, $brickable->getDestroyRoute($brick));
    }

    /** @test */
    public function brickable_can_set_and_returns_validation_rules()
    {
        $brickable = new Class extends Brickable {
            public function setValidationRules(): array
            {
                return ['custom' => ['validation', 'rules']];
            }
        };
        $this->assertEquals(['custom' => ['validation', 'rules']], $brickable->getValidationRules());
    }

    /** @test */
    public function brickable_can_get_validated_keys()
    {
        $brickable = new Class extends Brickable {
            public function setValidationRules(): array
            {
                return ['custom' => ['validation', 'rules']];
            }
        };
        $this->assertEquals(['custom'], $brickable->getValidatedKeys());
    }
}
