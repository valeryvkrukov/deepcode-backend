<?php
namespace AppBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * @RouteResource("Account", pluralize=false)
 */
class AccountController extends Controller
{
	public function cgetAction()
	{
		$serializer = $this->get('jms_serializer');
        $customers = $this->getUserRepository()->getAllCustomers();
        $customers = $serializer->toArray($customers);
        return new JsonResponse($customers);
	}

	public function postAction(Request $request)
	{
		$data = json_decode($request->getContent());
		try {
			$customer = $this->getUserRepository()->createCustomer($data);
			$response = [
				'status' => 'ok',
				'message' => sprintf('Customer %s created', $customer->getUsername())
			];
		} catch (\Exception $e) {
			$response = $this->buildFailMessage($e);
		}
		return new JsonResponse($response);
	}

	public function getAction(Request $request, $id)
	{
		try {
			$customer = $this->getUserRepository()->getCustomerWithCardsInfo($id);
			$serializer = $this->get('jms_serializer');
			$response = $serializer->toArray($customer);
		} catch (\Exception $e) {
			$response = $this->buildFailMessage($e);
		}
		return new JsonResponse($response);
	}

	public function putAction(Request $request, $id)
	{
		$data = json_decode($request->getContent());
		try {
			$customer = $this->getUserRepository()->updateCustomer($id, $data);
			$serializer = $this->get('jms_serializer');
			$response = $serializer->toArray($customer);
		} catch (\Exception $e) {
			$response = $this->buildFailMessage($e);
		}
		return new JsonResponse($response);
	}

	public function deleteAction(Request $request, $id)
	{
		$serializer = $this->get('jms_serializer');
		try {
			$customers = $this->getUserRepository()->deleteCustomer($id);
			$response = $serializer->toArray($customers);
		} catch (\Exception $e) {
			$response = $this->buildFailMessage($e);
		}
		return new JsonResponse($response);
	}

	public function postCardTransactionsAction(Request $request, $id, $cardId)
	{
		$filters = $request->get('filters');
		try {
			$transactions = $this->getCardRepository()->getCardTransactions($cardId, $filters);
			$serializer = $this->get('jms_serializer');
			$response = $serializer->toArray($transactions);
		} catch (\Exception $e) {
			$response = $this->buildFailMessage($e);
		}
		return new JsonResponse($response);
	}

	public function postCardAction(Request $request, $id)
	{
		$data = json_decode($request->getContent());
		try {
			$cards = $this->getCardRepository()->addCustomerCard($id, $data);
			$serializer = $this->get('jms_serializer');
			$response = $serializer->toArray($cards);
		} catch (\Exception $e) {
			$response = $this->buildFailMessage($e);
		}
		return new JsonResponse($response);
	}

	public function putCardAction(Request $request, $id, $cardId)
	{
		$data = json_decode($request->getContent());
		try {
			$cards = $this->getCardRepository()->updateCustomerCard($id, $cardId, $data);
			$serializer = $this->get('jms_serializer');
			$response = $serializer->toArray($cards);
		} catch (\Exception $e) {
			$response = $this->buildFailMessage($e);
		}
		return new JsonResponse($response);
	}

	public function deleteCardAction(Request $request, $id, $cardId)
	{
		$serializer = $this->get('jms_serializer');
		try {
			$customerCards = $this->getCardRepository()->deleteCustomerCard($id, $cardId);
			$response = $serializer->toArray($customerCards);
		} catch (\Exception $e) {
			$response = $this->buildFailMessage($e);
		}
		return new JsonResponse($response);
	}

	protected function getUserRepository()
	{
		$userManager = $this->get('fos_user.user_manager');
		return $this->getDoctrine()
        	->getManager()
        	->getRepository('AppBundle:User')
        	->setUserManager($userManager);
	}

	protected function getCardRepository()
	{
		return $this->getDoctrine()
        	->getManager()
        	->getRepository('AppBundle:Card');
	}

	protected function buildFailMessage(\Exception $e)
	{
		return [
			'status' => 'fail', 
			'message' => $e->getMessage(),
			'file' => $e->getFile(),
			'line' => $e->getLine(),
		];
	}
}