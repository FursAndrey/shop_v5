<?php

namespace App\Actions\TestingActions\Prepare;

use App\Actions\TestingActions\Create\CreateTestOptionAction;
use App\Actions\TestingActions\Get\GetTestOptionAction;

class PrepareTestOptionAction extends PrepareTestPropertyAction
{
    private $property = null;

    public function prepare(bool $isFull = true, int $optionsCount = 1): array
    {
        $this->property = parent::prepare();
        
        if ($optionsCount == 1) {
            $arr = $this->prepareOne($isFull);
        } else {
            for ($i = 0; $i < $optionsCount; $i++) {
                $arr[] = $this->prepareOne($isFull);
            }
        }

        return $arr;
    }

    public function prepareNoCreate(): array
    {
        if (is_null($this->property)) {
            $this->property = parent::prepare();
        }
        return (new GetTestOptionAction)($this->property['id']);
    }

    private function prepareOne(bool $isFull = true): array
    {
        $option = (new CreateTestOptionAction)(
            (new GetTestOptionAction)($this->property['id'])
        );
        
        $arr = [
            'id' => $option->id,
            'name' => $option->name,
        ];

        if ($isFull) {
            $arr['property'] = $this->property;
        }

        return $arr;
    }
}