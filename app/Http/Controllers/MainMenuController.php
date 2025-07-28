<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMainMenuRequest;
use App\Http\Requests\UpdateMainMenuRequest;
use App\Models\MainMenu;
use Illuminate\Http\Request;

class MainMenuController extends CrudController
{
    protected string $modelClass = MainMenu::class;
    protected string $contextField = "main_menu";
    protected string $contextFieldPlural = "main_menus";
    protected string $resourceName = "main_menus";
    protected function getOrderBy(): string
    {
        return 'sorted';
    }

    protected function getOrderDirection(): string
    {
        return 'asc';
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMainMenuRequest $request)
    {
        $data = $request->validated();
        $mainMenu = $this->prepareMainMenuTitle($data);
        // dd($mainMenu);
        $this->modelClass::create(array_merge($mainMenu, $data));

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "მენიუ შეიქმნა წარმატებით");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMainMenuRequest $request, MainMenu $main_menu, )
    {
        $data = $request->validated();
        $updatedData = $this->prepareMainMenuTitle($data);

        $main_menu->update(array_merge($updatedData, $data));

        return redirect()
            ->back()
            ->with("success", "მენიუ განახლდა წარმატებით");
    }

    /**
     * Extract shared logic for preparing $mainMenu data.
     */
    private function prepareMainMenuTitle(array $data): array
    {
        return [
            'title' => [
                'ka' => $data['title_ka'],
                'en' => $data['title_en'],
            ],
        ];
    }
}
