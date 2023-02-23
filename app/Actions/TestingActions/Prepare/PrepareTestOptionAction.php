<?php

namespace App\Actions\TestingActions\Prepare;

use App\Actions\TestingActions\Create\CreateTestOptionAction;
use App\Actions\TestingActions\Get\GetTestOptionAction;
use App\Models\Option;

class PrepareTestOptionAction
{
    private static $property = null;

    public function __construct()
    {
        if (is_null(self::$property)) {
            self::$property = (new PrepareTestPropertyAction)->short();
        } else {

        }
    }

    public function __destruct()
    {
        self::$property = null;
    }

    public function short(): array
    {
        $option = $this->intoDB();

        $arr = [
            'id' => $option->id,
            'name' => $option->name,
        ];

        return $arr;
    }

    public function full(): array
    {
        $option = $this->intoDB();

        $arr = [
            'id' => $option->id,
            'name' => $option->name,
            'property' => [
                'id' => self::$property['id'],
                'name' => self::$property['name'],
            ],
        ];

        return $arr;
    }

    public function noDB(): array
    {
        return (new GetTestOptionAction)(self::$property['id']);
    }

    private function intoDB(): Option
    {
        return (new CreateTestOptionAction)(
            (new GetTestOptionAction)(self::$property['id'])
        );
    }
}