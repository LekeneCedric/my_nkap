<?php

namespace App\category\Infrastructure\Factories;

use App\category\Application\Command\Save\SaveCategoryCommand;
use Illuminate\Http\Request;
use InvalidArgumentException;

class SaveCategoryCommandFactory
{

    public static function buildFromRequest(Request $request): SaveCategoryCommand
    {
        self::validate($request);
        $command = new SaveCategoryCommand(
            userId: $request->get('userId'),
            categoryColor: $request->get('color'),
            categoryIcon: $request->get('icon'),
            categoryName: $request->get('name'),
            categoryDescription: $request->get('description') ?? $request->get('name'),
        );
        $command->categoryId = $request->get('categoryId');
        return $command;
    }

    private static function validate(Request $request): void
    {
        if (
            empty($request->get('userId')) ||
            empty($request->get('icon')) ||
            empty($request->get('name')) ||
            empty($request->get('color'))
        ) throw new InvalidArgumentException('Commande invalide !');
    }
}
