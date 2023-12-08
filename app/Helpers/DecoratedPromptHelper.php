<?php

namespace App\Helpers;

use App\Helpers\PromptHelper;
use Exception;
use Illuminate\Support\Facades\Lang;

class DecoratedPromptHelper
{
    private $promptHelper;
    private $prependInstruction = '';

    public function __construct(PromptHelper $promptHelper)
    {
        $this->promptHelper = $promptHelper;
        $this->prependInstruction = Lang::get('prompt.prepend_inst', [], 'en');
    }

    public function __call($method, $args)
    {
        if (method_exists($this->promptHelper, $method)) {
            $result = call_user_func_array([$this->promptHelper, $method], $args);
            return $this->prependInstruction . $result;
        }
        throw new Exception("Method {$method} not found in " . get_class($this->promptHelper));
    }
}
