<?php

namespace App\Services\Scenes;

use App\Models\Dealer;
use App\Models\District;
use App\Models\Region;
use App\Models\User;
use App\Services\BotService;
use App\Services\Telegram\ScenesCore\BaseScene;
use App\Services\Telegram\ScenesCore\SceneCbEnum;
use App\Services\Telegram\ScenesCore\SceneStep;
use App\Services\Telegram\TgHelper;
use Illuminate\Support\Facades\Hash;
use Throwable;

class RegisterScene extends BaseScene
{
    protected string $sceneName = 'registerScene';
    protected const DISTRICT_PREFIX = 'district_';
    protected const REGION_PREFIX = 'region_';
    protected const DEALER_PREFIX = 'dealer_';

    protected array $messages = [
        'firstname' => 'Введите ваше имя:',
        'lastname' => 'Введите вашу фамилию:',
        'district' => 'Выберите вашу область:',
        'region' => 'Выберите ваш регион:',
        'location' => 'Выберите ваше местоположение:',
        'dealer' => 'Выберите дилера:',
        'phone' => 'Отправьте свой номер телефона по кнопке ниже, или введите номер в формате <i>998XXXXXXXXX</i>',
    ];

    public function onStart(): void
    {
        $this->ctx->answer('Добро пожаловать в Baraka Dekor!');
    }

    public function initSteps(): array
    {
        return [
            'firstname' => new SceneStep(
                function () {
                    $this->ctx->answer(
                        $this->messages['firstname'],
                        $this->getCommonMarkup([
                            [SceneCbEnum::CANCEL]
                        ])
                    );
                },
                function () {
                    $this->appendData(['firstname' => $this->ctx->getText()]);
                    $this->next();
                }
            ),

            'lastname' => new SceneStep(
                function () {
                    $this->ctx->answer(
                        $this->messages['lastname'],
                        $this->getCommonMarkup([[SceneCbEnum::CANCEL]])
                    );
                },
                function () {
                    $this->appendData(['lastname' => $this->ctx->getText()]);
                    $this->next();
                }
            ),

            'district' => new SceneStep(
                function () {
                    $this->ctx->answer(
                        $this->messages['district'],
                        [
                            'reply_markup' => ['inline_keyboard' => $this->getDistrictButtons()],
                        ]
                    );
                },
                function () {
                    $this->appendData([
                        'district' => str_replace(self::DISTRICT_PREFIX, '', $this->ctx->getCallbackQuery())
                    ]);
                    $this->next(true);
                }
            ),

            'region' => new SceneStep(
                function () {
                    $districtId = $this->getData('district');
                    $this->ctx->answer(
                        $this->messages['region'],
                        [
                            'reply_markup' => ['inline_keyboard' => $this->getRegionButtons($districtId)],
                        ]
                    );
                },
                function () {
                    $this->appendData([
                        'region' => str_replace(self::REGION_PREFIX, '', $this->ctx->getCallbackQuery())
                    ]);
                    $this->next(true);
                }
            ),

            'location' => new SceneStep(
                function () {
                    $this->ctx->answer(
                        $this->messages['location'],
                        [
                            'reply_markup' => [
                                'inline_keyboard' => [
                                    [
                                        ['text' => 'Отмена', 'callback_data' => 'cancel']
                                    ]
                                ],
                                'keyboard' => [
                                    [
                                        ['text' => 'Отправить местоположение', 'request_location' => true]
                                    ]
                                ],
                                'resize_keyboard' => true,
                                'one_time_keyboard' => true
                            ],
                        ]
                    );
                },
                function () {
                    $location = $this->ctx->message['location'];
                    TgHelper::console("Location: " . json_encode($location));
                    $this->appendData([
                        'location' => json_encode($location)
                    ]);
                    $this->next();
                }
            ),

            'dealer' => new SceneStep(
                function () {
                    $this->ctx->answer(
                        $this->messages['dealer'],
                        [
                            'reply_markup' => [
                                'inline_keyboard' => $this->getDealerButtons(),
                                'remove_keyboard' => true,
                            ],
                        ]
                    );
                },
                function () {
                    $this->appendData([
                        'dealer' => str_replace(self::DEALER_PREFIX, '', $this->ctx->getCallbackQuery())
                    ]);
                    $this->next(true);
                }
            ),

            'phone' => new SceneStep(
                function () {
                    $this->ctx->answerHtml(
                        $this->messages['phone'],
                        [
                            'reply_markup' => [
                                'keyboard' => [
                                    [
                                        ['text' => '📱 Отправить номер телефона', 'request_contact' => true]
                                    ]
                                ],
                                'resize_keyboard' => true,
                                'one_time_keyboard' => true
                            ]
                        ]
                    );
                },
                function () {
                    $contact = $this->ctx->message['contact'];
                    $this->appendData(['phone' => $contact['phone_number']]);

                    if ($this->saveData()) {
                        $response = 'Спасибо за регистрацию! Ваши данные успешно сохранены!';
                    } else {
                        $response = 'Произошла ошибка при сохранении данных. Попробуйте еще раз /' . BotService::START_COMMAND;
                    }
                    $this->ctx->answer($response, [
                        'reply_markup' => ['remove_keyboard' => true]
                    ]);
                    $this->finish();
                }
            ),
        ];
    }

    protected function saveData(): bool
    {
        try {
            $data = $this->getData();
            $user = new User();
            $user->chat_id = $this->ctx->getFromId();
            $user->firstname = $data['firstname'];
            $user->lastname = $data['lastname'];
            $user->district_id = $data['district'];
            $user->region_id = $data['region'];
            $user->location = $data['location'];
            $user->dealer_id = $data['dealer'];
            $user->phone = $data['phone'];
            $user->password = Hash::make('12345678');
            $user->saveOrFail();

            return true;
        } catch (Throwable $e) {
            TgHelper::console($e->getMessage());
            return false;
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
