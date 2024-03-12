<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\CategoryFactory;
use App\Factory\FortuneCookieFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // create 10 Categories
        //        CategoryFactory::createMany(10);

        // Create 50 FortuneCookie
        //        FortuneCookieFactory::createMany(50, function () {
        //            return [
        //                'category' => CategoryFactory::random(),
        //            ];
        //        });

        foreach ($this->getCategoryData() as $data) {
            $category = CategoryFactory::new()->create([
                'name' => $data['name'],
                'iconKey' => $data['iconKey'],
            ]);

            foreach ($data['fortuneCookies'] as $fortuneCookieData) {
                FortuneCookieFactory::new()->create([
                    'category' => $category,
                    'fortune' => $fortuneCookieData['fortune'],
                ]);
            }
        }
    }

    /**
     * @return iterable<array{ name: string, iconKey: string, fortuneCookies: array<array{ fortune: string|int}> }>
     */
    private function getCategoryData(): iterable
    {
        yield [
            'name' => 'Job',
            'iconKey' => 'fa-dollar',
            'fortuneCookies' => [
                [
                    'fortune' => 'It would be best to maintain a low profile for now.',
                ],
                [
                    'fortune' => '404 Fortune not found. Abort, Retry, Ignore?',
                ],
                [
                    'fortune' => 'You laugh now, wait til you get home.',
                ],
                [
                    'fortune' => 'If your work is not finished, blame it on the computer.',
                ],
            ],
        ];

        yield [
            'name' => 'Lunch',
            'iconKey' => 'fa-utensils',
            'fortuneCookies' => [
                [
                    'fortune' => 'You will be hungry again in one hour.',
                ],
                [
                    'fortune' => 'Vampires will soon strike you if you do not order again.',
                ],
                [
                    'fortune' => 'A nice cake is waiting for you.',
                ],
                [
                    'fortune' => 'Warning: Do not eat your fortune.',
                ],
            ],
        ];

        yield [
            'name' => 'Proverbs',
            'iconKey' => 'fa-quote-left',
            'fortuneCookies' => [
                [
                    'fortune' => 'A conclusion is simply the place where you got tired of thinking.',
                ],
                [
                    'fortune' => 'Cookie said: "You really crack me up"',
                ],
                [
                    'fortune' => 'When you squeeze an orange, orange juice comes out. Because that\'s what\'s inside.',
                ],
            ],
        ];

        yield [
            'name' => 'Pets',
            'iconKey' => 'fa-paw',
            'fortuneCookies' => [
                [
                    'fortune' => 'There\'s no such thing as an ordinary cat',
                ],
                [
                    'fortune' => 'That wasn\'t chicken',
                ],
            ],
        ];

        yield [
            'name' => 'Love',
            'iconKey' => 'fa-heart',
            'fortuneCookies' => [
                [
                    'fortune' => 'An alien of some sort will be appearing to you shortly!',
                ],
                [
                    'fortune' => 'Are your legs tired? You\'ve been running through someone\'s mind all day long.',
                ],
                [
                    'fortune' => 'run',
                ],
            ],
        ];

        yield [
            'name' => 'Lucky Number',
            'iconKey' => 'fa-clover',
            'fortuneCookies' => [
                [
                    'fortune' => 42,
                ],
                [
                    'fortune' => 12,
                ],
                [
                    'fortune' => '10^2',
                ],
                [
                    'fortune' => 'Jar Jar Binks',
                ],
                [
                    'fortune' => 'Pi',
                ],
            ],
        ];
    }
}
