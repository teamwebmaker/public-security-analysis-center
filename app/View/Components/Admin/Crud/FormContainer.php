<?php

namespace App\View\Components\Admin\Crud;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormContainer extends Component
{
    public string $title;
    public string $action;
    public string $method;
    public string $insertMethod;

    public bool $hasFileUpload;
    public string $cardClass;
    public string $cardWrapperClass;
    public string $cardBodyClass;
    public string $titleClass;
    public string $formClass;
    public string $submitButtonText;
    public string $submitButtonIcon;
    public string $backRoute;


    public function __construct(
        string $title,
        string $action,
        string $method = 'POST',
        string $insertMethod = '',
        bool $hasFileUpload = false,

        string $cardWrapperClass = 'col col-lg-9',
        string $cardClass = 'border-0 shadow-sm',
        string $cardBodyClass = 'p-4',
        string $titleClass = 'card-header bg-white h4 ps-4 py-4',
        string $formClass = 'needs-validation dirty-check-form',
        string $submitButtonText = 'დადასტურება',
        string $submitButtonIcon = 'bi-check-lg',
        string $backRoute = ''
    ) {
        $this->title = $title;
        $this->action = $action;
        $this->method = $method;
        $this->insertMethod = $insertMethod;
        $this->hasFileUpload = $hasFileUpload;

        $this->cardWrapperClass = $cardWrapperClass;
        $this->cardClass = $cardClass;
        $this->cardBodyClass = $cardBodyClass;
        $this->titleClass = $titleClass;
        $this->formClass = $formClass;
        $this->submitButtonText = $submitButtonText;
        $this->submitButtonIcon = $submitButtonIcon;
        $this->backRoute = $backRoute;
    }
    public function data(): array
    {
        return [
            'title' => $this->title,
            'action' => $this->action,
            'method' => $this->method,
            'insertMethod' => $this->insertMethod,
            'hasFileUpload' => $this->hasFileUpload,
            'cardWrapperClass' => $this->cardWrapperClass,
            'cardClass' => $this->cardClass,
            'cardBodyClass' => $this->cardBodyClass,
            'titleClass' => $this->titleClass,
            'formClass' => $this->formClass,
            'submitButtonText' => $this->submitButtonText,
            'submitButtonIcon' => $this->submitButtonIcon,
            'backRoute' => $this->backRoute,
        ];
    }
    public function render(): View|Closure|string
    {
        return view('components.admin.crud.form-container', $this->data());
    }
}
