<?php

namespace App\Services\Scenes;

use App\Models\Dealer;
use App\Models\District;
use App\Models\Region;
use App\Models\User;
use App\Models\UserPurchase;
use App\Services\BotService;
use App\Services\Telegram\ScenesCore\BaseScene;
use App\Services\Telegram\ScenesCore\SceneCbEnum;
use App\Services\Telegram\ScenesCore\SceneStep;
use App\Services\Telegram\TgHelper;
use Exception;
use Throwable;

class PurchaseScene extends BaseScene
{
    protected string $sceneName = 'purchaseScene';

    protected array $messages = [
        'price' => 'Введите сумму покупки:',
    ];

    public function onStart(): void
    {
    }

    public function initSteps(): array
    {
        return [
            'price' => new SceneStep(
                function () {
                    $this->ctx->answer(
                        $this->messages['price'],
                        $this->getCommonMarkup([
                            [SceneCbEnum::CANCEL]
                        ])
                    );
                },
                function () {
                    // check price is integer
                    $price = $this->ctx->getText();
                    if (!is_numeric($price)) {
                        $this->ctx->answer(
                            'Сумма покупки должна быть числом! Введите сумму покупки:',
                            $this->getCommonMarkup([
                                [SceneCbEnum::CANCEL]
                            ])
                        );
                        return;
                    }
                    $this->appendData(['price' => $price]);

                    $this->saveData();
                    $this->ctx->answer('Спасибо за покупку!');
                    $this->finish();
                }
            ),
        ];
    }

    protected function saveData(): void
    {
        try {
            $currentUser = User::where('chat_id', $this->ctx->getFromId())->first();
            if (!$currentUser) {
                throw new Exception();
            }

            $data = $this->getData();

            $userPurchase = new UserPurchase();
            $userPurchase->user_id = $currentUser->id;
            $userPurchase->price = $data['price'];
            $userPurchase->saveOrFail();
        } catch (Throwable $e) {
            TgHelper::console($e->getMessage());
            $this->ctx->answer(
                'Произошла ошибка при сохранении данных. Попробуйте еще раз /' . BotService::PURCHASE_COMMAND
            );
            $this->finish();
        }
    }

    private function getDistrictButtons(): array
    {
        $districts = District::get();
        $buttons = [];
        foreach ($districts as $district) {
            $buttons[] = [
                [
                    'text' => $district->name,
                    'callback_data' => self::DISTRICT_PREFIX . $district->id
                ]
            ];
        }
        return $buttons;
    }

    private function getRegionButtons($districtId): array
    {
        $regions = Region::where('district_id', $districtId)->get();
        $buttons = [];
        foreach ($regions as $region) {
            $buttons[] = [
                [
                    'text' => $region->name,
                    'callback_data' => self::REGION_PREFIX . $region->id
                ]
            ];
        }
        return $buttons;
    }

    private function getDealerButtons(): array
    {
        $dealers = Dealer::get();
        $buttons = [];
        foreach ($dealers as $dealer) {
            $buttons[] = [
                [
                    'text' => $dealer->name,
                    'callback_data' => self::DEALER_PREFIX . $dealer->id
                ]
            ];
        }
        return $buttons;
    }
}
