<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Card
 *
 * @ORM\Table(name="cards")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CardRepository")
 */
class Card
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="card", type="string", length=255, unique=true)
     */
    private $card;

    /**
     * @ORM\Column(name="card_type", type="string", length=64)
     */
    private $cardType;

    /**
     * @ORM\Column(name="balance", type="decimal", precision=10, scale=2)
     */
    private $balance;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Transaction")
     * @ORM\JoinTable(name="card_transactions",
     *      joinColumns={ @ORM\JoinColumn(name="card_id", referencedColumnName="id", onDelete="cascade") },
     *      inverseJoinColumns={ @ORM\JoinColumn(name="transaction_id", referencedColumnName="id", unique=true, onDelete="cascade") }
     * )
     */
    private $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set card
     *
     * @param string $card
     *
     * @return Card
     */
    public function setCard($card)
    {
        $this->card = $card;

        return $this;
    }

    /**
     * Get card
     *
     * @return string
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * Set card
     *
     * @param string $cardType
     *
     * @return Card
     */
    public function setCardType($cardType)
    {
        $this->cardType = $cardType;

        return $this;
    }

    /**
     * Get cardType
     *
     * @return string
     */
    public function getCardType()
    {
        return $this->cardType;
    }

    /**
     * Set balance
     *
     * @param string $balance
     *
     * @return Card
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return string
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Add transaction
     *
     * @param Transaction $transaction
     *
     * @return Card
     */
    public function addTransaction($transaction)
    {
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * Remove transaction
     *
     * @param Transaction $transaction
     *
     * @return Card
     */
    public function removeTransaction($transaction)
    {
        $this->transactions->removeElement($transaction);

        return $this;
    }

    /**
     * Set transactions
     *
     * @param Transaction[] $transaction
     *
     * @return Card
     */
    public function setTransactions($transactions)
    {
        $this->transactions = $transaction;

        return $this;
    }

    /**
     * Get transactions
     *
     * @return Transaction[]
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

}

