<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

use AppBundle\Entity\User;
use AppBundle\Entity\Card;
use AppBundle\Entity\Transaction;

class LoadFixtures implements FixtureInterface
{
	public function load(ObjectManager $manager)
	{
		$faker = \Faker\Factory::create('en_US');
		$users = Fixtures::load(__DIR__.'/fixtures.yml', $manager);
		foreach ($users as $i => $user) {
			if (!$user->hasRole('ROLE_ADMIN')) {
				for ($c = 0; $c < $faker->randomDigitNotNull; $c++) {
					$card = new Card();
					$card->setCard($faker->creditCardNumber);
					$card->setCardType($faker->creditCardType);
					$card->setBalance($faker->randomFloat(2, 10, 2000));
					for ($t = 0; $t < $faker->numberBetween(5, 20); $t++) {
						$transaction = new Transaction();
						$transaction->setTransactionType($faker->randomElement(['Debit', 'Credit']));
						$transaction->setAmount($faker->randomFloat(2, 10, 1000));
						$transaction->setCreatedAt($faker->dateTime);
						$manager->persist($transaction);
						$card->addTransaction($transaction);
					}
					$manager->persist($card);
					$user->addCard($card);
					$manager->persist($user);
				}
			}
			var_dump($user->getFirstName());
		}
		$manager->flush();
	}
}