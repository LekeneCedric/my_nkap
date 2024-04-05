<?php

namespace App\Account\Tests\Units;

use App\Account\Application\Command\Save\SaveAccountCommand;
use App\Account\Application\Command\Save\SaveAccountHandler;
use App\Account\Application\Command\Save\SaveAccountResponse;
use App\Account\Domain\Exceptions\ErrorOnSaveAccountException;
use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Account\Tests\Units\Builders\SaveAccountCommandBuilder;
use App\Account\Tests\Units\Repositories\InMemoryAccountRepository;
use App\Shared\VO\Id;
use Tests\TestCase;

class SaveAccountTest extends TestCase
{
    private InMemoryAccountRepository $repository;
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new InMemoryAccountRepository();
    }

    /**
     * @return void
     * @throws ErrorOnSaveAccountException
     * @throws NotFoundAccountException
     */
    public function test_can_create_account()
    {
        $command = SaveAccountCommandBuilder::asCommand()
            ->withUserId((new Id())->value())
            ->withName('Epargne')
            ->withType('compte epargne')
            ->withIcon('icon_name')
            ->withColor('color_name')
            ->withBalance(5000)
            ->withIsIncludeInTotalBalance(true)
            ->build();

        $response = $this->saveAccount($command);

        $this->assertTrue($response->status);
        $this->assertTrue($response->isSaved);
        $this->assertEquals('Epargne',
            $this->repository->account[$response->accountId]->name()->value());
    }

    /**
     * @return void
     * @throws ErrorOnSaveAccountException
     * @throws NotFoundAccountException
     */
    public function test_can_update_account()
    {
        $initData = AccountSUT::asSUT()
            ->withExistingAccount()
            ->build($this->repository);

        $command = SaveAccountCommandBuilder::asCommand()
            ->withUserId((new Id())->value())
            ->withAccountId($initData->account->id()->value())
            ->withName('Courant')
            ->withType('compte courant')
            ->withIcon('icon2_name')
            ->withColor('color2_name')
            ->withBalance(7000)
            ->withIsIncludeInTotalBalance(false)
            ->build();

        $response = $this->saveAccount($command);

        $this->assertTrue($response->status);
        $this->assertTrue($response->isSaved);
        $this->assertEquals('Courant', $this->repository->account[$response->accountId]->name()->value());
    }

    /**
     * @return void
     * @throws ErrorOnSaveAccountException
     * @throws NotFoundAccountException
     */
    public function test_can_throw_not_found_account_exception()
    {
        AccountSUT::asSUT()
            ->withExistingAccount()
            ->build($this->repository);

        $command = SaveAccountCommandBuilder::asCommand()
            ->withUserId((new Id())->value())
            ->withAccountId('wrongId')
            ->withName('Courant')
            ->withType('compte courant')
            ->withIcon('icon2_name')
            ->withColor('color2_name')
            ->withBalance(7000)
            ->withIsIncludeInTotalBalance(false)
            ->build();

        $this->expectException(NotFoundAccountException::class);
        $this->saveAccount($command);
    }

    /**
     * @param SaveAccountCommand $command
     * @return SaveAccountResponse
     * @throws NotFoundAccountException
     * @throws ErrorOnSaveAccountException
     */
    private function saveAccount(SaveAccountCommand $command): SaveAccountResponse
    {
        $handler = new SaveAccountHandler(
            repository: $this->repository
        );
        return $handler->handle($command);
    }
}
