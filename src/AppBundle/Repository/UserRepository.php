<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use FOS\UserBundle\Doctrine\UserManager;

class UserRepository extends EntityRepository
{
	protected $userManager;

	public function getAllCustomers()
	{
		try {
			return $this->getEntityManager()
				->createQuery(
					'SELECT 
						u.id,
						u.firstName, 
						u.lastName, 
						u.username, 
						u.email, 
						u.enabled,
						u.lastLogin 
					FROM AppBundle:User u WHERE u.roles LIKE :customer_role'
				)
				->setParameters(['customer_role' => '%ROLE_CUSTOMER%'])
				->getResult();
		} catch (NoResultException $e) {
			return [];
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	public function getCustomerWithCardsInfo($id)
	{
		$query = $this->getEntityManager()
			->createQuery(
				'SELECT u FROM AppBundle:User u WHERE u.id = :user_id'
			)
			->setParameters(['user_id' => $id]);
		try {
			return $query->getSingleResult();
		} catch (NoResultException $e) {
			return [];
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	public function createCustomer($data)
	{
		if (null === $this->userManager) {
			throw new \Exception('UserManager is not set');
		}
		$customer = $this->userManager->createUser();
		foreach (['firstName', 'lastName', 'username', 'email', 'password'] as $field) {
			if (!isset($data->$field) || empty($data->$field)) {
				throw new \Exception(sprintf('Field `%s` is required', $field));
			} else {
				$setter = 'set' . ($field == 'password' ? 'Plain' : '') . ucfirst($field);
				$customer->$setter($data->$field);
			}
		}
		$customer->setEnabled(1);
		$customer->setRoles(['ROLE_CUSTOMER']);
		$this->userManager->updateUser($customer);
		return $customer;
	}

	public function updateCustomer($id, $data)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery(
				'SELECT u FROM AppBundle:User u WHERE u.id = :user_id'
			)
			->setParameters(['user_id' => $id]);
		try {
			$customer = $query->getSingleResult();
			foreach ($data as $field => $value) {
				$setter = 'set' . ucfirst($field);
				if (method_exists($customer, $setter)) {
					$customer->$setter($value);
				}
			}
			$em->persist($customer);
			$em->flush();
			return $customer;
		} catch (NoResultException $e) {
			return [];
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	public function deleteCustomer($id)
	{
		try {
			$this->getEntityManager()
				->createQuery(
					'DELETE AppBundle:User u WHERE u.id = :user_id'
				)
				->setParameters(['user_id' => $id])
				->execute();
			return $this->getAllCustomers();
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	public function setUserManager(UserManager $userManager)
	{
		$this->userManager = $userManager;
		return $this;
	}
}