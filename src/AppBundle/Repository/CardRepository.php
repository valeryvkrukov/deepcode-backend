<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Card;
use AppBundle\Entity\Transaction;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\EntityRepository;

class CardRepository extends EntityRepository
{
	public function getCustomerCards($id)
	{
		try {
			$customer = $this->getEntityManager()
				->createQuery(
					'SELECT u FROM AppBundle:User u WHERE u.id = :user_id AND u.roles LIKE :customer_role'
				)
				->setParameters([
					'user_id' => $id,
					'customer_role' => '%ROLE_CUSTOMER%',
				])
				->getSingleResult();
			return $customer->getCards();
		} catch (NoResultException $e) {
			return [];
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	public function getCardTransactions($id, $filters)
	{
		$em = $this->getEntityManager();
		$dql = 'SELECT c, t FROM AppBundle:Card c JOIN c.transactions t WHERE c.id = :card_id';
		$params = ['card_id' => $id];
		if (isset($filters['transactionType']) && !empty($filters['transactionType'])) {
			$dql .= ' AND t.transactionType = :transaction_type';
			$params['transaction_type'] = $filters['transactionType'];
		}
		if (isset($filters['fromDate']) && isset($filters['toDate']) && is_array($filters['fromDate']) && is_array($filters['toDate'])) {
			$dql .= ' AND t.createdAt BETWEEN :from_date AND :to_date';
			$params['from_date'] = (new \DateTime())->setDate(
				$filters['fromDate']['year'], 
				$filters['fromDate']['month'], 
				$filters['fromDate']['day']
			);
			$params['to_date'] = (new \DateTime())->setDate(
				$filters['toDate']['year'], 
				$filters['toDate']['month'], 
				$filters['toDate']['day']
			);
		} elseif (isset($filters['fromDate']) && is_array($filters['fromDate'])) {
			$dql .= ' AND t.createdAt > :from_date';
			$params['from_date'] = (new \DateTime())->setDate(
				$filters['fromDate']['year'], 
				$filters['fromDate']['month'], 
				$filters['fromDate']['day']
			);
		} elseif (isset($filters['toDate']) && is_array($filters['toDate'])) {
			$dql .= ' AND t.createdAt < :to_date';
			$params['to_date'] = (new \DateTime())->setDate(
				$filters['toDate']['year'], 
				$filters['toDate']['month'], 
				$filters['toDate']['day']
			);
		}
		$dql .= ' ORDER BY t.createdAt DESC';
		$query = $em->createQuery($dql)->setParameters($params);
		try {
			$card = $query->getSingleResult();
			return $card->getTransactions();
		} catch (NoResultException $e) {
			return [];
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	public function addCustomerCard($id, $data)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery(
				'SELECT u FROM AppBundle:User u WHERE u.id = :user_id AND u.roles LIKE :customer_role'
			)
			->setParameters([
				'user_id' => $id,
				'customer_role' => '%ROLE_CUSTOMER%',
			]);
		try {
			$customer = $query->getSingleResult();
			$card = new Card();
			$card->setCard($data->card);
			$card->setCardType($data->cardType);
			$card->setBalance($data->balance);
			$this->generateFakeTransactions($em, $card);
			$customer->addCard($card);
			$em->persist($card);
			$em->persist($customer);
			$em->flush();
			return $customer->getCards();
		} catch (NoResultException $e) {
			return [];
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	public function updateCustomerCard($id, $cardId, $data)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery(
				'SELECT c FROM AppBundle:Card c WHERE c.id = :card_id'
			)
			->setParameters(['card_id' => $cardId]);
		try {
			$card = $query->getSingleResult();
			foreach ($data as $field => $value) {
				$setter = 'set' . ucfirst($field);
				if (method_exists($card, $setter)) {
					$card->$setter($value);
				}
			}
			$em->persist($card);
			$em->flush();
			return $this->getCustomerCards($id);
		} catch (NoResultException $e) {
			return [];
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	public function deleteCustomerCard($id, $cardId)
	{
		try {
			$this->getEntityManager()
				->createQuery(
					'DELETE AppBundle:Card c WHERE c.id = :card_id'
				)
				->setParameters([
					'card_id' => $cardId,
				])
				->execute();
			return $this->getCustomerCards($id);
		} catch (NoResultException $e) {
			return [];
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	protected function generateFakeTransactions(&$em, &$card)
	{
		$faker = \Faker\Factory::create('en_US');
		for ($t = 0; $t < $faker->numberBetween(10, 30); $t++) {
			$transaction = new Transaction();
			$transaction->setTransactionType($faker->randomElement(['Debit', 'Credit']));
			$transaction->setAmount($faker->randomFloat(2, 10, 1000));
			$transaction->setCreatedAt($faker->dateTime);
			$em->persist($transaction);
			$card->addTransaction($transaction);
		}
	}
}
